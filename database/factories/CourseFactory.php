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
        $type = fake()->randomElement(['bangkom', 'bulanan']);
        $startDate = $type === 'bulanan' ? fake()->dateTimeBetween('+1 week', '+3 months') : null;
        $endDate = $startDate ? fake()->dateTimeBetween($startDate, '+4 months') : null;

        return [
            'slug' => fake()->unique()->slug(),
            'title' => fake()->sentence(5),
            'description' => null,
            'category_id' => null,
            'type' => $type,
            'price' => 0,
            'jam_pelajaran' => 24,
            'start_date' => $startDate,
            'end_date' => $endDate,
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

    /** Set course type to Bangkom (mandiri / self-paced). */
    public function bangkom(): static
    {
        return $this->state([
            'type' => 'bangkom',
            'start_date' => null,
            'end_date' => null,
            'price' => 0,
        ]);
    }

    /** Backward compatibility alias for bangkom(). */
    public function permanent(): static
    {
        return $this->bangkom();
    }

    /** Set course type to Bulanan (berjadwal). */
    public function bulanan(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): static
    {
        return $this->state([
            'type' => 'bulanan',
            'start_date' => $startDate ?? now()->addDay(),
            'end_date' => $endDate ?? now()->addMonth(),
            'price' => 0,
        ]);
    }

    /** Backward compatibility alias for bulanan(). */
    public function batch(?\DateTimeInterface $startDate = null, ?\DateTimeInterface $endDate = null): static
    {
        return $this->bulanan($startDate, $endDate);
    }

    /** Set course type to Bangkom External (berbayar). */
    public function bangkomExternal(float $price = 100000): static
    {
        return $this->state([
            'type' => 'bangkom_external',
            'price' => $price,
        ]);
    }

    /** Backward compatibility alias for bangkomExternal(). */
    public function paid(float $price = 100000): static
    {
        return $this->bangkomExternal($price);
    }
}
