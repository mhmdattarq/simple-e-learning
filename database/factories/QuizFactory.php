<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory()->state(fn () => ['category_id' => Category::factory()]),
            'chapter_id' => null,
            'type' => 'chapter',
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'time_limit_minutes' => 30,
            'total_score' => 100,
            'passing_score' => 70,
            'created_by' => User::factory(),
        ];
    }

    /**
     * State for final quiz.
     */
    public function finalQuiz(): static
    {
        return $this->state(fn () => [
            'type' => 'final',
            'chapter_id' => null,
            'title' => 'Ujian Akhir Kelas (Final Exam)',
        ]);
    }

    /**
     * State for chapter quiz.
     */
    public function chapterQuiz(?Chapter $chapter = null): static
    {
        return $this->state(fn () => [
            'type' => 'chapter',
            'chapter_id' => $chapter?->id ?? Chapter::factory(),
        ]);
    }
}
