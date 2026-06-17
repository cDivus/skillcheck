<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Option>
 */
class OptionFactory extends Factory
{
    protected $model = Option::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'question_id' => Question::inRandomOrder()->first()?->question_id ?? Question::factory()->create()->question_id,
            'order_index' => $this->faker->numberBetween(1, 4),
            'option_text' => $this->faker->word(),
            'is_correct' => false,
        ];
    }
}
