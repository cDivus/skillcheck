<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    /**
     * Update the grades (marks_awarded) for a specific answer (for essay/QA questions).
     */
    public function update(Request $request, $answerId)
    {
        $validated = $request->validate([
            'marks_awarded' => 'required|numeric|min:0',
        ]);

        $answer = StudentAnswer::with(['attempt.exam', 'question'])->findOrFail($answerId);

        // Ensure the instructor owns this exam
        if ($answer->attempt->exam->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Ensure marks awarded doesn't exceed question's maximum marks
        if ($validated['marks_awarded'] > $answer->question->marks) {
            return redirect()->back()->with('error', 'Marks awarded cannot exceed the question max marks (' . $answer->question->marks . ').');
        }

        $answer->marks_awarded = $validated['marks_awarded'];
        $answer->save();

        return redirect()->back()->with('success', 'Question marks updated successfully.');
    }
}
