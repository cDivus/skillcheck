<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'image_url' => 'nullable|string|max:255',
            'marks' => 'required|numeric|min:0',
            'options' => 'nullable|array',
            'options.*' => 'nullable|string',
            'correct_options' => 'nullable|array',
            'correct_options.*' => 'integer',
            'tf_correct' => 'nullable|string|in:True,False',
            'qa_correct_answer' => 'nullable|string|max:1000',
        ]);

        // Automatically determine the next order_index for questions in this exam
        $maxOrder = Question::where('exam_id', $exam->exam_id)->max('order_index');
        $nextOrder = ($maxOrder !== null) ? $maxOrder + 1 : 1;

        $question = Question::create([
            'exam_id' => $exam->exam_id,
            'order_index' => $nextOrder,
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'time_limit_s' => $validated['time_limit_s'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'marks' => $validated['marks'],
        ]);

        // Save options if the question type is multiple_choice or question_answer
        if (in_array($validated['type'], ['multiple_choice', 'question_answer']) && !empty($validated['options'])) {
            $correctOptions = $validated['correct_options'] ?? [];
            $optionIndex = 1;
            foreach ($validated['options'] as $idx => $optionText) {
                if ($optionText !== null && trim($optionText) !== '') {
                    Option::create([
                        'question_id' => $question->question_id,
                        'order_index' => $optionIndex++,
                        'option_text' => trim($optionText),
                        'is_correct' => ($validated['type'] === 'question_answer') ? true : in_array($idx, $correctOptions),
                    ]);
                }
            }
        }

        // Auto-seed options for True/False question
        if ($validated['type'] === 'true_false') {
            $tfCorrect = $validated['tf_correct'] ?? 'True';
            Option::create([
                'question_id' => $question->question_id,
                'order_index' => 1,
                'option_text' => 'True',
                'is_correct' => ($tfCorrect === 'True'),
            ]);
            Option::create([
                'question_id' => $question->question_id,
                'order_index' => 2,
                'option_text' => 'False',
                'is_correct' => ($tfCorrect === 'False'),
            ]);
        }

        return redirect()->route('instructor.exams.index')->with('success', 'Question added successfully.');
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
            'image_url' => 'nullable|string|max:255',
            'marks' => 'required|numeric|min:0',
            'options' => 'nullable|array',
            'options.*.option_id' => 'nullable|string',
            'options.*.option_text' => 'nullable|string',
            'correct_options' => 'nullable|array',
            'correct_options.*' => 'integer',
            'tf_correct' => 'nullable|string|in:True,False',
            'qa_correct_answer' => 'nullable|string|max:1000',
        ]);

        $question->update([
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'time_limit_s' => $validated['time_limit_s'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'marks' => $validated['marks'],
        ]);

        // Process options based on question type
        if (in_array($validated['type'], ['multiple_choice', 'question_answer'])) {
            $submittedOptions = $request->input('options', []);
            $correctIndices = $request->input('correct_options', []);
            $keptOptionIds = [];
            $optionIndex = 1;

            foreach ($submittedOptions as $idx => $optData) {
                $optionText = $optData['option_text'] ?? '';
                if ($optionText === null || trim($optionText) === '') {
                    continue;
                }

                $optionId = $optData['option_id'] ?? null;
                $isCorrect = ($validated['type'] === 'question_answer') ? true : in_array($idx, $correctIndices);

                if ($optionId) {
                    $option = Option::where('question_id', $question->question_id)
                        ->find($optionId);
                    if ($option) {
                        $option->update([
                            'option_text' => trim($optionText),
                            'is_correct' => $isCorrect,
                            'order_index' => $optionIndex++,
                        ]);
                        $keptOptionIds[] = $option->option_id;
                    }
                } else {
                    $newOption = Option::create([
                        'question_id' => $question->question_id,
                        'order_index' => $optionIndex++,
                        'option_text' => trim($optionText),
                        'is_correct' => $isCorrect,
                    ]);
                    $keptOptionIds[] = $newOption->option_id;
                }
            }

            // Delete removed options
            Option::where('question_id', $question->question_id)
                ->whereNotIn('option_id', $keptOptionIds)
                ->delete();

        } elseif ($validated['type'] === 'true_false') {
            $tfCorrect = $request->input('tf_correct', 'True');
            
            $options = Option::where('question_id', $question->question_id)->get();
            $trueOption = $options->firstWhere('option_text', 'True');
            $falseOption = $options->firstWhere('option_text', 'False');

            if ($trueOption) {
                $trueOption->update(['is_correct' => ($tfCorrect === 'True'), 'order_index' => 1]);
            } else {
                $trueOption = Option::create([
                    'question_id' => $question->question_id,
                    'order_index' => 1,
                    'option_text' => 'True',
                    'is_correct' => ($tfCorrect === 'True'),
                ]);
            }

            if ($falseOption) {
                $falseOption->update(['is_correct' => ($tfCorrect === 'False'), 'order_index' => 2]);
            } else {
                $falseOption = Option::create([
                    'question_id' => $question->question_id,
                    'order_index' => 2,
                    'option_text' => 'False',
                    'is_correct' => ($tfCorrect === 'False'),
                ]);
            }

            Option::where('question_id', $question->question_id)
                ->whereNotIn('option_id', [$trueOption->option_id, $falseOption->option_id])
                ->delete();

        } elseif ($validated['type'] === 'essay') {
            Option::where('question_id', $question->question_id)->delete();
        }

        return redirect()->route('instructor.exams.index')->with('success', 'Question updated successfully.');
    }
}
