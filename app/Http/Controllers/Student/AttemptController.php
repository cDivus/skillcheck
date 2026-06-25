<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\StudentAnswer;
use App\Models\Exam;
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

        $exam = Exam::findOrFail($examId);

        // Check if the exam has started yet
        if ($exam->start_time && now()->lt(Carbon::parse($exam->start_time))) {
            return redirect()->route('student.exams.show', $examId)
                ->with('error', 'This exam has not started yet.');
        }

        // Check if the exam deadline has passed
        if ($exam->end_time && now()->gt(Carbon::parse($exam->end_time))) {
            return redirect()->route('student.exams.index')
                ->with('error', 'This exam is no longer available (deadline passed).');
        }

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
        $durationSeconds = $attempt->exam->duration_m ? ($attempt->exam->duration_m * 60) : null;
        $endTime = $durationSeconds ? $startTime->copy()->addSeconds($durationSeconds) : null;

        // Check if the current time is past the attempt's end time or past the exam's overall end time
        $timedOut = false;
        if ($endTime && now()->greaterThanOrEqualTo($endTime)) {
            $timedOut = true;
        }
        if ($attempt->exam->end_time) {
            $examEndTime = Carbon::parse($attempt->exam->end_time);
            if (now()->greaterThanOrEqualTo($examEndTime)) {
                $timedOut = true;
            }
        }

        if ($timedOut) {
            $attempt->submit();
            return redirect()->route('student.exams.index')
                ->with('error', 'Time has expired. Your attempt has been automatically submitted.');
        }

        // Calculate exact remaining time in seconds
        $timeLeft = null;
        if ($endTime) {
            $timeLeft = max(0, now()->diffInSeconds($endTime, false));
        }
        if ($attempt->exam->end_time) {
            $examEndTime = Carbon::parse($attempt->exam->end_time);
            $examTimeLeft = max(0, now()->diffInSeconds($examEndTime, false));
            $timeLeft = ($timeLeft !== null) ? min($timeLeft, $examTimeLeft) : $examTimeLeft;
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
            $orderedQuestionIds = $this->generateQuestionOrder($exam);
            $attempt->question_order = $orderedQuestionIds;
            $attempt->save();
        }

        $questionsCount = count($orderedQuestionIds);

        // Paginate questions
        $page = (int) request('page', 1);
        if ($page < 1 || $page > $questionsCount) {
            $page = 1;
        }

        // Find the first unanswered question page index
        $firstUnansweredPage = 1;
        for ($i = 0; $i < $questionsCount; $i++) {
            $qId = $orderedQuestionIds[$i];
            if (!isset($answers[$qId])) {
                $firstUnansweredPage = $i + 1;
                break;
            } else {
                $firstUnansweredPage = $questionsCount;
            }
        }

        // Enforce strict sequential order: cannot skip forward or go back (only if timer_type is per_question)
        if ($exam->timer_type === 'per_question') {
            if ($page !== $firstUnansweredPage) {
                return redirect()->route('student.exams.attempt.take', [
                    'exam' => $examId,
                    'attempt' => $attemptId,
                    'page' => $firstUnansweredPage
                ]);
            }
        }

        // Fetch just the current question
        $question = $exam->questions()
            ->with('options')
            ->where('question_id', $orderedQuestionIds[$page - 1])
            ->first();

        // Track and calculate question-level time limit
        $questionTimeLeft = null;
        if ($exam->timer_type === 'per_question' && $question->time_limit_s) {
            $sessionKey = "exam_attempt_{$attemptId}_question_{$question->question_id}_start";
            if (!session()->has($sessionKey)) {
                session()->put($sessionKey, now()->timestamp);
            }
            $questionStartedAt = session()->get($sessionKey);
            $elapsed = now()->timestamp - $questionStartedAt;
            $questionTimeLeft = max(0, $question->time_limit_s - $elapsed);

            // If question time is expired, auto-submit blank answer and move forward
            if ($questionTimeLeft <= 0) {
                $emptyAnswer = StudentAnswer::firstOrNew([
                    'attempt_id' => $attemptId,
                    'question_id' => $question->question_id,
                ]);
                if (!$emptyAnswer->exists) {
                    if ($question->type === 'essay' || $question->type === 'question_answer') {
                        $emptyAnswer->text_answer = '';
                    }
                    $emptyAnswer->save();
                }

                if ($page === $questionsCount) {
                    $attempt->submit();
                    return redirect()->route('student.exams.index')
                        ->with('error', 'Time has expired for the final question. Your exam has been submitted.');
                }

                return redirect()->route('student.exams.attempt.take', [
                    'exam' => $examId,
                    'attempt' => $attemptId,
                    'page' => $page + 1
                ])->with('error', 'Time has expired for the previous question.');
            }
        }

        return view('student.attempts.take', compact(
            'attempt',
            'exam',
            'question',
            'answers',
            'timeLeft',
            'questionTimeLeft',
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

        $attempt->submit();

        return redirect()->route('student.exams.index')
            ->with('success', 'Your exam attempt has been submitted successfully.');
    }

    /**
     * Review a completed exam attempt if viewable_responses is enabled.
     */
    public function review($examId, $attemptId)
    {
        $attempt = ExamAttempt::with(['exam.instructor', 'exam.questions.options', 'answers'])->findOrFail($attemptId);

        if ($attempt->student_id !== Auth::id() || $attempt->exam_id !== $examId) {
            abort(403, 'Unauthorized action.');
        }

        if ($attempt->status === 'in_progress') {
            return redirect()->route('student.exams.attempt.take', [
                'exam' => $examId,
                'attempt' => $attemptId
            ]);
        }

        if (!$attempt->exam->viewable_responses) {
            return redirect()->route('student.exams.index')
                ->with('error', 'Viewing responses for this exam has been disabled by the instructor.');
        }

        $exam = $attempt->exam;
        $answers = $attempt->answers->keyBy('question_id');

        // Preserves the order in which the student took the questions
        if (!empty($attempt->question_order)) {
            $orderedQuestionIds = $attempt->question_order;
            $questions = $exam->questions()
                ->with('options')
                ->whereIn('question_id', $orderedQuestionIds)
                ->get()
                ->sortBy(function ($question) use ($orderedQuestionIds) {
                    return array_search($question->question_id, $orderedQuestionIds);
                });
        } else {
            $questions = $exam->questions()->with('options')->orderBy('order_index')->get();
        }

        return view('student.attempts.review', compact('attempt', 'exam', 'questions', 'answers'));
    }



    /**
     * Generate question order for the exam attempt, preserving locked question positions.
     */
    protected function generateQuestionOrder(Exam $exam)
    {
        $questions = $exam->questions()->orderBy('order_index')->get();

        if (!$exam->randomize_questions) {
            return $questions->pluck('question_id')->toArray();
        }

        $locked = [];
        $unlocked = [];

        foreach ($questions as $index => $question) {
            if ($question->is_locked) {
                $locked[$index] = $question->question_id;
            } else {
                $unlocked[] = $question->question_id;
            }
        }

        shuffle($unlocked);

        $orderedQuestionIds = [];
        $unlockedIndex = 0;

        for ($i = 0; $i < count($questions); $i++) {
            if (isset($locked[$i])) {
                $orderedQuestionIds[$i] = $locked[$i];
            } else {
                $orderedQuestionIds[$i] = $unlocked[$unlockedIndex++];
            }
        }

        return $orderedQuestionIds;
    }
}
