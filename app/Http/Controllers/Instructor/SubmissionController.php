<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of student submissions for a specific exam.
     */
    public function index($examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())->findOrFail($examId);
        
        $attempts = ExamAttempt::where('exam_id', $examId)
            ->whereIn('status', ['submitted', 'graded'])
            ->with('student')
            ->get();

        return view('instructor.submissions.index', compact('exam', 'attempts'));
    }

    /**
     * Show a specific submission for grading.
     */
    public function show($attemptId)
    {
        $attempt = ExamAttempt::with([
            'student',
            'exam.questions.options',
            'answers.question'
        ])->findOrFail($attemptId);

        // Ensure the authenticated instructor owns the exam associated with this attempt
        if ($attempt->exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Key answers by question_id for easy lookup
        $answers = $attempt->answers->keyBy('question_id');

        return view('instructor.submissions.grade', compact('attempt', 'answers'));
    }

    /**
     * Finalize the evaluation and mark the attempt as graded.
     */
    public function finalize(Request $request, $attemptId)
    {
        $attempt = ExamAttempt::with('exam.questions', 'answers')->findOrFail($attemptId);

        if ($attempt->exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure all essay/manual questions have been graded (marks_awarded is not null)
        $ungradedAnswers = $attempt->answers->filter(function ($answer) {
            return $answer->marks_awarded === null;
        });

        if ($ungradedAnswers->isNotEmpty()) {
            return redirect()->back()->with('error', 'Please grade all essay questions before finalising the attempt.');
        }

        $attempt->status = 'graded';
        $attempt->save();

        return redirect()->route('instructor.submissions.index', $attempt->exam_id)
            ->with('success', 'Attempt finalized and graded successfully.');
    }

    /**
     * Delete a student exam attempt.
     */
    public function destroy($attemptId)
    {
        $attempt = ExamAttempt::with('exam')->findOrFail($attemptId);

        if ($attempt->exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $examId = $attempt->exam_id;
        $attempt->delete();

        return redirect()->route('instructor.submissions.index', $examId)
            ->with('success', 'Student attempt deleted successfully.');
    }
}
