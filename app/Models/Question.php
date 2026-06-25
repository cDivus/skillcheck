<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'Questions';
    protected $primaryKey = 'question_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'exam_id',
        'order_index',
        'question_text',
        'image_url',
        'type',
        'time_limit_s',
        'marks',
        'is_locked',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'exam_id');
    }

    public function options()
    {
        return $this->hasMany(Option::class, 'question_id', 'question_id')->orderBy('order_index');
    }

    /**
     * Synchronize options for the question based on its type and input data.
     *
     * @param array $data
     * @return void
     */
    public function syncOptions(array $data)
    {
        if (in_array($this->type, ['multiple_choice', 'question_answer'])) {
            $submittedOptions = $data['options'] ?? [];
            $correctIndices = $data['correct_options'] ?? [];
            $keptOptionIds = [];
            $optionIndex = 1;

            foreach ($submittedOptions as $idx => $optData) {
                // Support both array inputs (from UI updates) and string inputs (from UI store)
                if (is_array($optData)) {
                    $optionText = $optData['option_text'] ?? '';
                    $optionId = $optData['option_id'] ?? null;
                    $isCorrect = $optData['is_correct'] ?? null;
                } else {
                    $optionText = $optData;
                    $optionId = null;
                    $isCorrect = null;
                }

                if ($optionText === null || trim($optionText) === '') {
                    continue;
                }

                // Calculate correctness if not explicitly provided
                if ($isCorrect === null) {
                    $isCorrect = ($this->type === 'question_answer') ? true : in_array($idx, $correctIndices);
                } else {
                    $isCorrect = (bool) $isCorrect;
                }

                if ($optionId) {
                    $option = $this->options()->find($optionId);
                    if ($option) {
                        $option->update([
                            'option_text' => trim($optionText),
                            'is_correct' => $isCorrect,
                            'order_index' => $optionIndex++,
                        ]);
                        $keptOptionIds[] = $option->option_id;
                    }
                } else {
                    $newOption = $this->options()->create([
                        'order_index' => $optionIndex++,
                        'option_text' => trim($optionText),
                        'is_correct' => $isCorrect,
                    ]);
                    $keptOptionIds[] = $newOption->option_id;
                }
            }
            // Delete removed options
            $this->options()->whereNotIn('option_id', $keptOptionIds)->delete();

        } elseif ($this->type === 'true_false') {
            $tfCorrect = $data['tf_correct'] ?? 'True';
            $correctVal = filter_var($tfCorrect, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($correctVal === null) {
                $correctVal = (strtolower(trim($tfCorrect)) === 'true');
            }

            $options = $this->options()->get();
            $trueOption = $options->firstWhere('option_text', 'True');
            $falseOption = $options->firstWhere('option_text', 'False');

            if ($trueOption) {
                $trueOption->update(['is_correct' => ($correctVal === true), 'order_index' => 1]);
            } else {
                $trueOption = $this->options()->create([
                    'order_index' => 1,
                    'option_text' => 'True',
                    'is_correct' => ($correctVal === true),
                ]);
            }

            if ($falseOption) {
                $falseOption->update(['is_correct' => ($correctVal === false), 'order_index' => 2]);
            } else {
                $falseOption = $this->options()->create([
                    'order_index' => 2,
                    'option_text' => 'False',
                    'is_correct' => ($correctVal === false),
                ]);
            }

            $this->options()->whereNotIn('option_id', [$trueOption->option_id, $falseOption->option_id])->delete();

        } elseif ($this->type === 'essay') {
            $this->options()->delete();
        }
    }

    protected static function booted()
    {
        static::deleting(function ($question) {
            if ($question->image_url && !filter_var($question->image_url, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($question->image_url);
            }
        });
    }
}
