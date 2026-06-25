<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function index()
    {
        $attempts = ExamAttempt::where('student_id', Auth::id())
            ->where('status', 'in_progress')
            ->with(['exam.questions'])
            ->get();

        foreach ($attempts as $attempt) {
            $startTime = Carbon::parse($attempt->start_time);
            $durationSeconds = $attempt->exam->duration_m ? ($attempt->exam->duration_m * 60) : null;
            $endTime = $durationSeconds ? $startTime->copy()->addSeconds($durationSeconds) : null;
            $isExpired = $endTime ? now()->greaterThanOrEqualTo($endTime) : false;

            if ($attempt->exam->end_time) {
                $examEndTime = Carbon::parse($attempt->exam->end_time);
                if (now()->greaterThanOrEqualTo($examEndTime)) {
                    $isExpired = true;
                }
            }

            if ($isExpired) {
                $attempt->submit();
            }
        }

        $attempts = ExamAttempt::where('student_id', Auth::id())
            ->with(['exam'])
            ->get();

        return view('student.exams.index', compact('attempts'));
    }

    public function show($examId)
    {
        $exam = Exam::with('instructor')->findOrFail($examId);

        $attempt = ExamAttempt::where('exam_id', $examId)
            ->where('student_id', Auth::id())
            ->first();

        if ($attempt) {
            if ($attempt->status === 'in_progress') {
                $startTime = Carbon::parse($attempt->start_time);
                $durationSeconds = $exam->duration_m ? ($exam->duration_m * 60) : null;
                $endTime = $durationSeconds ? $startTime->copy()->addSeconds($durationSeconds) : null;
                $isExpired = $endTime ? now()->greaterThanOrEqualTo($endTime) : false;

                if ($exam->end_time) {
                    $examEndTime = Carbon::parse($exam->end_time);
                    if (now()->greaterThanOrEqualTo($examEndTime)) {
                        $isExpired = true;
                    }
                }

                if ($isExpired) {
                    $attempt->submit();
                    return redirect()->route('student.exams.index')
                        ->with('error', 'You have already attempted this exam (time limit expired).');
                }

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
