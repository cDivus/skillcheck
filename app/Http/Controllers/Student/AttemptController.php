<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttemptController extends Controller
{
    /**
     * Start a new exam attempt.
     */
    public function store(Request $request, $examId)
    {
        $studentId = Auth::id();

        // Enforce unique attempt
        $existingAttempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->status === 'in_progress') {
                return redirect()->route('student.exams.attempt.take', [
                    'exam' => $examId,
                    'attempt' => $existingAttempt->attempt_id
                ]);
            }
            return redirect()->route('student.exams.index')
                ->with('error', 'You have already attempted this exam.');
        }

        $attempt = ExamAttempt::create([
            'exam_id' => $examId,
            'student_id' => $studentId,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        return redirect()->route('student.exams.attempt.take', [
            'exam' => $examId,
            'attempt' => $attempt->attempt_id
        ]);
    }

    /**
     * Display the test-taking environment for a specific attempt.
     */
    public function show($examId, $attemptId)
    {
        $attempt = ExamAttempt::with(['exam.questions.options', 'answers'])->findOrFail($attemptId);

        if ($attempt->student_id !== Auth::id() || $attempt->exam_id !== $examId) {
            abort(403, 'Unauthorized action.');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('error', 'This attempt has already been submitted.');
        }

        // Calculate time limit / remaining time
        $startTime = Carbon::parse($attempt->start_time);
        $durationSeconds = $attempt->exam->duration_s;
        $endTime = $startTime->copy()->addSeconds($durationSeconds);

        // Check if the current time is past the attempt's end time or past the exam's overall end time
        $timedOut = now()->greaterThanOrEqualTo($endTime);
        if ($attempt->exam->end_time) {
            $examEndTime = Carbon::parse($attempt->exam->end_time);
            if (now()->greaterThanOrEqualTo($examEndTime)) {
                $timedOut = true;
            }
        }

        if ($timedOut) {
            $this->submitAttempt($attempt);
            return redirect()->route('student.exams.index')
                ->with('error', 'Time has expired. Your attempt has been automatically submitted.');
        }

        // Calculate exact remaining time in seconds
        $timeLeft = max(0, now()->diffInSeconds($endTime, false));
        if ($attempt->exam->end_time) {
            $examEndTime = Carbon::parse($attempt->exam->end_time);
            $examTimeLeft = max(0, now()->diffInSeconds($examEndTime, false));
            $timeLeft = min($timeLeft, $examTimeLeft);
        }
        $exam = $attempt->exam;
        $answers = $attempt->answers->keyBy('question_id');

        // Retrieve or generate persistent question order for this attempt
        if (!empty($attempt->question_order)) {
            $orderedQuestionIds = $attempt->question_order;
            $activeIds = $exam->questions()->pluck('question_id')->toArray();
            
            // Sync: Remove any questions that were deleted
            $orderedQuestionIds = array_values(array_intersect($orderedQuestionIds, $activeIds));
            
            // Sync: Append any new questions that were added since the attempt started
            $newIds = array_diff($activeIds, $orderedQuestionIds);
            if (!empty($newIds)) {
                $newIdsArray = array_values($newIds);
                if ($exam->randomize_questions) {
                    shuffle($newIdsArray);
                }
                $orderedQuestionIds = array_merge($orderedQuestionIds, $newIdsArray);
            }

            // Save the synchronized order if it changed
            if ($attempt->question_order !== $orderedQuestionIds) {
                $attempt->question_order = $orderedQuestionIds;
                $attempt->save();
            }
        } else {
            $orderedQuestionIds = $exam->questions()->orderBy('order_index')->pluck('question_id')->toArray();
            if ($exam->randomize_questions) {
                shuffle($orderedQuestionIds);
            }
            $attempt->question_order = $orderedQuestionIds;
            $attempt->save();
        }

        $questionsCount = count($orderedQuestionIds);

        // Paginate questions
        $page = (int) request('page', 1);
        if ($page < 1 || $page > $questionsCount) {
            $page = 1;
        }

        // Check for first unanswered question up to the current page index
        for ($i = 0; $i < $page - 1; $i++) {
            $qId = $orderedQuestionIds[$i];
            if (!isset($answers[$qId])) {
                $unansweredPage = $i + 1;
                return redirect()->route('student.exams.attempt.take', [
                    'exam' => $examId,
                    'attempt' => $attemptId,
                    'page' => $unansweredPage
                ])->with('error', 'You must answer the current question before proceeding.');
            }
        }

        // Fetch just the current question
        $question = $exam->questions()
            ->with('options')
            ->where('question_id', $orderedQuestionIds[$page - 1])
            ->first();

        return view('student.attempts.take', compact(
            'attempt',
            'exam',
            'question',
            'answers',
            'timeLeft',
            'page',
            'questionsCount'
        ));
    }

    /**
     * Submit the exam attempt for grading.
     */
    public function submit(Request $request, $examId, $attemptId)
    {
        $attempt = ExamAttempt::findOrFail($attemptId);

        if ($attempt->student_id !== Auth::id() || $attempt->status !== 'in_progress') {
            return redirect()->route('student.exams.index')
                ->with('error', 'Invalid attempt submission.');
        }

        $this->submitAttempt($attempt);

        return redirect()->route('student.exams.index')
            ->with('success', 'Your exam attempt has been submitted successfully.');
    }

    /**
     * Reusable logic to finalize and grade auto-gradable questions.
     */
    protected function submitAttempt(ExamAttempt $attempt)
    {
        $attempt->load('exam.questions.options');

        foreach ($attempt->exam->questions as $question) {
            $answer = StudentAnswer::firstOrNew([
                'attempt_id' => $attempt->attempt_id,
                'question_id' => $question->question_id,
            ]);

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $correctOption = $question->options->firstWhere('is_correct', true);
                if ($correctOption && $answer->selected_option === $correctOption->option_id) {
                    $answer->marks_awarded = $question->marks;
                } else {
                    $answer->marks_awarded = 0;
                }
            } elseif ($question->type === 'question_answer') {
                $correctAnswers = $question->options
                    ->where('is_correct', true)
                    ->pluck('option_text')
                    ->map(fn($val) => strtolower(trim($val)))
                    ->toArray();

                $studentText = strtolower(trim($answer->text_answer ?? ''));
                if (in_array($studentText, $correctAnswers) && $studentText !== '') {
                    $answer->marks_awarded = $question->marks;
                } else {
                    $answer->marks_awarded = 0;
                }
            } elseif ($question->type === 'essay') {
                // Keep marks_awarded as null for instructor manual grading.
            }

            $answer->save();
        }

        $attempt->status = 'submitted';
        $attempt->end_time = now();
        $attempt->save();

        // If the exam has no essay questions, it can be auto-finalized as 'graded'
        $hasEssay = $attempt->exam->questions->where('type', 'essay')->isNotEmpty();
        if (!$hasEssay) {
            $attempt->status = 'graded';
            $attempt->save();
        }
    }
}
