<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;


class QuestionController extends Controller
{
    /**
     * Show the form for creating a new question for the exam.
     */
    public function create($examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        return view('instructor.questions.create', compact('exam'));
    }

    /**
     * Store a newly created question in storage for the given exam.
     */
    public function store(Request $request, $examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string|in:multiple_choice,true_false,question_answer,essay',
            'time_limit_s' => ($exam->timer_type === 'per_question' ? 'required' : 'nullable') . '|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|string|max:2048',
            'marks' => 'required|numeric|min:0',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_options' => 'nullable|array',
            'correct_options.*' => 'integer',
            'tf_correct' => 'nullable|string|in:True,False',
            'qa_correct_answer' => 'nullable|string|max:1000',
            'is_locked' => 'nullable|boolean',
        ]);

        // Automatically determine the next order_index for questions in this exam
        $maxOrder = Question::where('exam_id', $exam->exam_id)->max('order_index');
        $nextOrder = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        $imagePath = $validated['image_url'] ?? null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question = Question::create([
            'exam_id' => $exam->exam_id,
            'order_index' => $nextOrder,
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'time_limit_s' => $validated['time_limit_s'] ?? null,
            'image_url' => $imagePath,
            'marks' => $validated['marks'],
            'is_locked' => $request->boolean('is_locked'),
        ]);

        // Save options using the model method
        $question->syncOptions($validated);

        return redirect()->route('instructor.exams.show', $exam->exam_id)->with('success', 'Question added successfully.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit($examId, $questionId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $question = Question::where('exam_id', $exam->exam_id)
            ->with('options')
            ->findOrFail($questionId);

        return view('instructor.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, $examId, $questionId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $question = Question::where('exam_id', $exam->exam_id)
            ->findOrFail($questionId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|string|in:multiple_choice,true_false,question_answer,essay',
            'time_limit_s' => ($exam->timer_type === 'per_question' ? 'required' : 'nullable') . '|integer|min:1',
            'image' => 'nullable|image|max:2048',
            'image_url' => 'nullable|string|max:2048',
            'remove_image' => 'nullable',
            'marks' => 'required|numeric|min:0',
            'options' => 'nullable|array',
            'options.*.option_id' => 'nullable|string',
            'options.*.option_text' => 'nullable|string',
            'correct_options' => 'nullable|array',
            'correct_options.*' => 'integer',
            'tf_correct' => 'nullable|string|in:True,False',
            'qa_correct_answer' => 'nullable|string|max:1000',
            'is_locked' => 'nullable|boolean',
        ]);

        $imagePath = $question->image_url;

        // If direct image URL input is changed/provided
        if ($request->has('image_url')) {
            $newImageUrl = $validated['image_url'];
            if ($newImageUrl !== $question->image_url) {
                if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($question->image_url);
                }
                $imagePath = !empty($newImageUrl) ? trim($newImageUrl) : null;
            }
        }

        if ($request->has('remove_image')) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($question->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($question->image_url);
            }
            $imagePath = $request->file('image')->store('questions', 'public');
        }

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'time_limit_s' => $validated['time_limit_s'] ?? null,
            'image_url' => $imagePath,
            'marks' => $validated['marks'],
            'is_locked' => $request->boolean('is_locked'),
        ]);

        // Process and sync options using the model method
        $question->syncOptions($validated);

        return redirect()->route('instructor.exams.show', $exam->exam_id)->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage and reorder remaining questions.
     */
    public function destroy($examId, $questionId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $question = Question::where('exam_id', $exam->exam_id)
            ->findOrFail($questionId);

        DB::transaction(function () use ($exam, $question) {
            $deletedOrderIndex = $question->order_index;

            // Delete the question (Model event handles image cleanup)
            $question->delete();

            // Retrieve remaining questions with a higher index, sorted ascending
            $subsequentQuestions = Question::where('exam_id', $exam->exam_id)
                ->where('order_index', '>', $deletedOrderIndex)
                ->orderBy('order_index', 'asc')
                ->get();

            // Reorder by decrementing their index
            foreach ($subsequentQuestions as $q) {
                $q->update(['order_index' => $q->order_index - 1]);
            }
        });

        return redirect()->route('instructor.exams.show', $exam->exam_id)
            ->with('success', 'Question deleted and order reindexed successfully.');
    }

    /**
     * Show the form for reordering questions.
     */
    public function reorder($examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->with(['questions' => function ($query) {
                $query->orderBy('order_index');
            }])
            ->findOrFail($examId);

        return view('instructor.questions.reorder', compact('exam'));
    }

    /**
     * Save the new order of questions.
     */
    public function saveOrder(Request $request, $examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'required|string|exists:Questions,question_id',
        ]);

        $questionIds = $validated['question_ids'];

        try {
            DB::transaction(function () use ($exam, $questionIds) {
                // Validate all question IDs belong to this exam
                $questionsCount = Question::where('exam_id', $exam->exam_id)
                    ->whereIn('question_id', $questionIds)
                    ->count();

                // Also make sure we account for all questions currently in the exam
                $totalExamQuestions = Question::where('exam_id', $exam->exam_id)->count();

                if ($questionsCount !== count($questionIds) || $questionsCount !== $totalExamQuestions) {
                    throw new \Exception('Invalid or incomplete list of questions submitted.');
                }

                // Step 1: Shift temporarily to negative indices to prevent duplicate key violations
                foreach ($questionIds as $newIndex => $questionId) {
                    Question::where('question_id', $questionId)
                        ->update(['order_index' => -($newIndex + 1)]);
                }

                // Step 2: Convert them back to positive values
                Question::where('exam_id', $exam->exam_id)
                    ->where('order_index', '<', 0)
                    ->update(['order_index' => DB::raw('ABS(order_index)')]);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reorder questions: ' . $e->getMessage());
        }

        return redirect()->route('instructor.exams.show', $exam->exam_id)
            ->with('success', 'Question order updated successfully.');
    }




}
