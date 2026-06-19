<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionImporter
{
    /**
     * Parse and import questions from a JSON string into an exam.
     *
     * @param Exam $exam
     * @param string $jsonContent
     * @param string $importMode
     * @return int The number of imported questions.
     * @throws \Exception
     */
    public function import(Exam $exam, string $jsonContent, string $importMode): int
    {
        $questions = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON file structure: ' . json_last_error_msg());
        }

        if (!is_array($questions)) {
            throw new \Exception('The JSON file must contain an array of questions.');
        }

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

        return count($questions);
    }
}
