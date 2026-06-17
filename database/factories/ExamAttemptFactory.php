<?php

namespace Database\Factories;

use App\Models\ExamAttempt;
use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    protected $model = ExamAttempt::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::inRandomOrder()->first()?->exam_id ?? Exam::factory()->create()->exam_id,
            'student_id' => User::where('role', 'student')->inRandomOrder()->first()?->user_id ?? User::factory()->create(['role' => 'student'])->user_id,
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHours(1),
            'status' => $this->faker->randomElement(['in_progress', 'submitted', 'graded']),
            'question_order' => null,
        ];
    }
}
