<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::inRandomOrder()->first()?->exam_id ?? Exam::factory()->create()->exam_id,
            'order_index' => $this->faker->numberBetween(1, 10),
            'question_text' => $this->faker->sentence(10) . '?',
            'image_url' => null,
            'type' => $this->faker->randomElement(['multiple_choice', 'question_answer']),
            'time_limit_s' => $this->faker->randomElement([60, 120, 180]), // 1m, 2m, 3m
            'marks' => $this->faker->randomElement([5, 10, 15]),
            'is_locked' => false,
        ];
    }
}
