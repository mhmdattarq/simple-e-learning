<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\CourseExternal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CourseExternal>
 */
class CourseExternalFactory extends Factory
{
    protected $model = CourseExternal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Learning Portal';

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'logo' => null,
            'url' => fake()->url(),
            'description' => fake()->sentence(12),
            'type' => fake()->randomElement(['gratis', 'berbayar']),
            'benefit' => fake()->randomElement(['Sertifikat', 'Sertifikat & SKP', 'Piagam']),
            'status' => 'published',
            'created_by' => User::factory(),
        ];
    }

    /**
     * State untuk kursus eksternal gratis.
     */
    public function gratis(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'gratis',
        ]);
    }

    /**
     * State untuk kursus eksternal berbayar.
     */
    public function berbayar(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'berbayar',
        ]);
    }
}
