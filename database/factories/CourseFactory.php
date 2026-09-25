<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['permanent', 'batch']);
        $startDate = $type === 'batch' ? fake()->dateTimeBetween('+1 week', '+3 months') : null;
        $endDate = $startDate ? fake()->dateTimeBetween($startDate, '+4 months') : null;

        return [
            'code' => 'PLT-'.fake()->year().'-'.fake()->unique()->numberBetween(100, 999),
            'title' => fake()->sentence(5),
            'category_id' => null,
            'type' => $type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'method' => fake()->randomElement(['online', 'offline', 'hybrid']),
            'location' => fake()->city(),
            'quota' => fake()->numberBetween(20, 60),
            'target_audience' => fake()->sentence(4),
            'budget_source' => 'APBK TA '.fake()->year(),
            'description' => fake()->paragraph(),
            'thumbnail' => null,
            'tor_file' => null,
            'status' => 'draft',
            'created_by' => User::factory(),
        ];
    }

    /** Set course status to published. */
    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }
}
