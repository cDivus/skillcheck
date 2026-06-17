<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'instructor_id' => User::where('role', 'instructor')->inRandomOrder()->first()?->user_id ?? User::factory()->create(['role' => 'instructor'])->user_id,
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(2),
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(2),
            'duration_s' => $this->faker->randomElement([1800, 3600, 5400]), // 30m, 1h, 1.5h
            'randomize_questions' => $this->faker->boolean(),
            'viewable_responses' => $this->faker->boolean(),
        ];
    }
}
