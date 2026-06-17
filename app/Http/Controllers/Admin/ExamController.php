<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    /**
     * Display a listing of all exams on the platform.
     */
    public function index(Request $request)
    {
        $query = Exam::query()->with('instructor');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Remove the specified exam from the system.
     */
    public function destroy($examId)
    {
        $exam = Exam::findOrFail($examId);
        $exam->delete();

        return redirect()->route('admin.exams.index')->with('success', "Exam '{$exam->title}' was successfully deleted.");
    }
}
