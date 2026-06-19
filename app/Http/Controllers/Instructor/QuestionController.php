<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'time_limit_s' => 'nullable|integer|min:1',
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
            'time_limit_s' => 'nullable|integer|min:1',
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
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($question->image_url);
                }
                $imagePath = !empty($newImageUrl) ? trim($newImageUrl) : null;
            }
        }

        if ($request->has('remove_image')) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($question->image_url);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($question->image_url);
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

    /**
     * Import questions from a JSON file.
     */
    public function import(Request $request, $examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->findOrFail($examId);

        $request->validate([
            'json_file' => 'required|file|max:2048', // Validate size, check type manually to support text/json variations
            'import_mode' => 'required|string|in:append,overwrite',
        ]);

        $file = $request->file('json_file');
        $jsonContent = file_get_contents($file->getRealPath());
        $questions = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return redirect()->back()->with('error', 'Invalid JSON file structure: ' . json_last_error_msg());
        }

        if (!is_array($questions)) {
            return redirect()->back()->with('error', 'The JSON file must contain an array of questions.');
        }

        $importMode = $request->input('import_mode');

        try {
            DB::transaction(function () use ($exam, $questions, $importMode) {
                // If overwrite mode, delete existing questions first (triggered individually to invoke Model deleting event and remove files)
                if ($importMode === 'overwrite') {
                    $exam->questions()->get()->each(function ($question) {
                        $question->delete();
                    });
                    $nextOrder = 1;
                } else {
                    // Determine the next order index
                    $maxOrder = Question::where('exam_id', $exam->exam_id)->max('order_index');
                    $nextOrder = ($maxOrder !== null) ? $maxOrder + 1 : 1;
                }

                foreach ($questions as $index => $qData) {
                    $questionNum = $index + 1;

                    // Basic validation of fields
                    if (empty($qData['question_text'])) {
                        throw new \Exception("Question #{$questionNum} is missing 'question_text'.");
                    }
                    if (empty($qData['type'])) {
                        throw new \Exception("Question #{$questionNum} is missing 'type'.");
                    }

                    $validTypes = ['multiple_choice', 'true_false', 'question_answer', 'essay'];
                    if (!in_array($qData['type'], $validTypes)) {
                        throw new \Exception("Question #{$questionNum} has an invalid 'type' ({$qData['type']}). Valid types: multiple_choice, true_false, question_answer, essay.");
                    }

                    // Create the question
                    $question = Question::create([
                        'exam_id' => $exam->exam_id,
                        'order_index' => $nextOrder++,
                        'question_text' => trim($qData['question_text']),
                        'type' => $qData['type'],
                        'time_limit_s' => isset($qData['time_limit_s']) ? intval($qData['time_limit_s']) : null,
                        'image_url' => !empty($qData['image_url']) ? trim($qData['image_url']) : null,
                        'marks' => isset($qData['marks']) ? floatval($qData['marks']) : 1.0,
                        'is_locked' => !empty($qData['is_locked']),
                    ]);

                    // Validate JSON data structures
                    if ($qData['type'] === 'multiple_choice') {
                        if (empty($qData['options']) || !is_array($qData['options'])) {
                            throw new \Exception("Question #{$questionNum} of type 'multiple_choice' must contain an array of 'options'.");
                        }
                        foreach ($qData['options'] as $opt) {
                            if (!isset($opt['option_text'])) {
                                throw new \Exception("An option in Question #{$questionNum} is missing 'option_text'.");
                            }
                        }
                    } elseif ($qData['type'] === 'question_answer') {
                        if (empty($qData['correct_answers'])) {
                            throw new \Exception("Question #{$questionNum} of type 'question_answer' must contain 'correct_answers'.");
                        }
                    }

                    // Save and sync options using the model method
                    $question->syncOptions($qData);
                }
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        $count = count($questions);
        return redirect()->route('instructor.exams.show', $exam->exam_id)
            ->with('success', "Successfully imported {$count} questions.");
    }

    /**
     * Export questions to a JSON file.
     */
    public function export($examId)
    {
        $exam = Exam::where('instructor_id', Auth::id())
            ->with(['questions.options'])
            ->findOrFail($examId);

        $exportData = [];

        foreach ($exam->questions as $question) {
            $qData = [
                'question_text' => $question->question_text,
                'type' => $question->type,
                'marks' => floatval($question->marks),
                'time_limit_s' => $question->time_limit_s ? intval($question->time_limit_s) : null,
                'is_locked' => (bool) $question->is_locked,
                'image_url' => ($question->image_url && str_starts_with($question->image_url, 'http')) ? $question->image_url : null,
            ];

            if ($question->type === 'multiple_choice') {
                $qData['options'] = [];
                foreach ($question->options as $option) {
                    $qData['options'][] = [
                        'option_text' => $option->option_text,
                        'is_correct' => (bool) $option->is_correct,
                    ];
                }
            } elseif ($question->type === 'true_false') {
                $trueOption = $question->options->firstWhere('option_text', 'True');
                $qData['correct_answer'] = $trueOption ? (bool) $trueOption->is_correct : true;
            } elseif ($question->type === 'question_answer') {
                $qData['correct_answers'] = $question->options->where('is_correct', true)->pluck('option_text')->toArray();
            }

            $exportData[] = $qData;
        }

        $jsonString = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $fileName = 'exam_' . \Illuminate\Support\Str::slug($exam->title) . '_questions.json';

        return response($jsonString, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
