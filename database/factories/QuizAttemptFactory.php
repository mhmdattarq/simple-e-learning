<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quiz_id' => Quiz::factory(),
            'user_id' => User::factory(),
            'total_earned_score' => 80,
            'total_possible_score' => 100,
            'percentage' => 80.00,
            'is_passed' => true,
            'answers_data' => [],
            'started_at' => now()->subMinutes(15),
            'submitted_at' => now(),
        ];
    }
}
