<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    /**
     * Save or update a student's answer for a specific question during an attempt.
     */
    public function store(Request $request, $examId, $attemptId)
    {
        $validated = $request->validate([
            'question_id' => 'required|uuid|exists:Questions,question_id',
            'selected_option' => 'nullable|uuid|exists:Options,option_id',
            'text_answer' => 'nullable|string',
        ]);

        $attempt = ExamAttempt::findOrFail($attemptId);

        if ($attempt->student_id !== Auth::id() || $attempt->exam_id !== $examId || $attempt->status !== 'in_progress') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized or exam is not in progress.'], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized or exam is not in progress.');
        }

        // Verify the question belongs to this exam
        $question = Question::where('exam_id', $examId)->findOrFail($validated['question_id']);

        $answer = StudentAnswer::firstOrNew([
            'attempt_id' => $attemptId,
            'question_id' => $validated['question_id'],
        ]);

        if ($question->type === 'essay' || $question->type === 'question_answer') {
            $answer->text_answer = $validated['text_answer'] ?? '';
            $answer->selected_option = null;
        } elseif ($question->type === 'multiple_choice' || $question->type === 'true_false') {
            $answer->selected_option = $validated['selected_option'] ?? null;
            $answer->text_answer = null;
        }

        $answer->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Answer saved successfully.']);
        }

        if ($request->input('action') === 'submit') {
            $this->submitAttempt($attempt);
            return redirect()->route('student.exams.index')
                ->with('success', 'Your exam attempt has been submitted successfully.');
        }

        $nextPage = (int) $request->input('next_page', 1);
        return redirect()->route('student.exams.attempt.take', [
            'exam' => $examId,
            'attempt' => $attemptId,
            'page' => $nextPage
        ]);
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
