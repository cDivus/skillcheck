<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;

class ExamController extends Controller
{
    /**
     * Display a listing of exams created by the instructor.
     */
    public function index()
    {
        // Retrieve exams created by the authenticated instructor, eager loading questions and options
        $exams = Exam::with('questions.options')
            ->where('instructor_id', Auth::id())
            ->get();

        return view('instructor.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new exam.
     */
    public function create()
    {
        return view('instructor.exams.create');
    }

    /**
     * Store a newly created exam in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_s' => 'required|integer|min:1',
        ]);

        Exam::create([
            'instructor_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'duration_s' => $validated['duration_s'],
        ]);

        return redirect()->route('instructor.exams.index')->with('success', 'Exam created successfully.');
    }
}
