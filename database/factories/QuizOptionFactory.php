<?php

namespace Database\Factories;

use App\Models\QuizOption;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizOption>
 */
class QuizOptionFactory extends Factory
{
    protected $model = QuizOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'question_id' => QuizQuestion::factory(),
            'option_text' => fake()->sentence(4),
            'is_correct' => false,
            'order' => 1,
        ];
    }

    /**
     * State for correct option.
     */
    public function correct(): static
    {
        return $this->state(fn () => [
            'is_correct' => true,
        ]);
    }
}
