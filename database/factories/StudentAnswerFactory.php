<?php

namespace Database\Factories;

use App\Models\StudentAnswer;
use App\Models\ExamAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentAnswer>
 */
class StudentAnswerFactory extends Factory
{
    protected $model = StudentAnswer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'attempt_id' => ExamAttempt::inRandomOrder()->first()?->attempt_id ?? ExamAttempt::factory()->create()->attempt_id,
            'question_id' => Question::inRandomOrder()->first()?->question_id ?? Question::factory()->create()->question_id,
            'selected_option' => null,
            'text_answer' => $this->faker->sentence(5),
            'marks_awarded' => $this->faker->randomElement([0, 5, 10]),
        ];
    }
}
