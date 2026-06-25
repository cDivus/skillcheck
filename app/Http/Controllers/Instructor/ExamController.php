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
        // Retrieve exams created by the authenticated instructor
        $exams = Exam::where('instructor_id', Auth::id())->get();

        return view('instructor.exams.index', compact('exams'));
    }

    /**
     * Display the specified exam and its questions.
     */
    public function show(Exam $exam)
    {
        // Ensure the authenticated instructor owns this exam
        if ($exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load the questions and options for this specific exam
        $exam->load('questions.options');

        return view('instructor.exams.show', compact('exam'));
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
            'randomize_questions' => 'nullable|boolean',
            'viewable_responses' => 'nullable|boolean',
        ]);

        $exam = Exam::create([
            'instructor_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'duration_s' => $validated['duration_s'],
            'randomize_questions' => $request->boolean('randomize_questions'),
            'viewable_responses' => $request->boolean('viewable_responses'),
        ]);

        return redirect()->route('instructor.exams.show', $exam->exam_id)->with('success', 'Exam created successfully.');
    }

    /**
     * Show the form for editing the specified exam.
     */
    public function edit(Exam $exam)
    {
        // Ensure the authenticated instructor owns this exam
        if ($exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.exams.edit', compact('exam'));
    }

    /**
     * Update the specified exam in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // Ensure the authenticated instructor owns this exam
        if ($exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'duration_s' => 'required|integer|min:1',
            'randomize_questions' => 'nullable|boolean',
            'viewable_responses' => 'nullable|boolean',
        ]);

        $exam->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'duration_s' => $validated['duration_s'],
            'randomize_questions' => $request->boolean('randomize_questions'),
            'viewable_responses' => $request->boolean('viewable_responses'),
        ]);

        return redirect()->route('instructor.exams.show', $exam)->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified exam from storage.
     */
    public function destroy(Exam $exam)
    {
        // Ensure the authenticated instructor owns this exam
        if ($exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $exam->delete();

        return redirect()->route('instructor.exams.index')->with('success', 'Exam deleted successfully.');
    }
}
