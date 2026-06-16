<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Display list of exams the student has already taken/attempted.
     */
    public function index()
    {
        $attempts = ExamAttempt::where('student_id', Auth::id())
            ->with(['exam'])
            ->get();

        return view('student.exams.index', compact('attempts'));
    }

    /**
     * Display the exam landing/start page from a shared instructor link.
     */
    public function show($examId)
    {
        $exam = Exam::findOrFail($examId);

        $attempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', Auth::id())
            ->first();

        if ($attempt) {
            if ($attempt->status === 'in_progress') {
                return redirect()->route('student.exams.attempt.take', [
                    'exam' => $examId,
                    'attempt' => $attempt->attempt_id
                ]);
            }
            return redirect()->route('student.exams.index')
                ->with('error', 'You have already attempted this exam.');
        }

        return view('student.exams.show', compact('exam'));
    }
}
