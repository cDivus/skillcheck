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
            $attempt->submit();
            return redirect()->route('student.exams.index')
                ->with('success', 'Your exam attempt has been submitted successfully.');
        }

        $currentPage = (int) $request->input('page', 1);
        $action = $request->input('action', 'next');
        $nextPage = ($action === 'prev') ? max(1, $currentPage - 1) : ($currentPage + 1);

        return redirect()->route('student.exams.attempt.take', [
            'exam' => $examId,
            'attempt' => $attemptId,
            'page' => $nextPage
        ]);
    }


}
