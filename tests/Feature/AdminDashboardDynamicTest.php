<?php

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access dashboard and view dynamic cards and tables', function () {
    $admin = User::factory()->create([
        'name' => 'Fauzan Administrator',
        'email' => 'admin@acehtimurkab.go.id',
        'role' => 'admin',
    ]);

    $category = Category::factory()->create(['name' => 'Teknologi Informasi']);

    $course = Course::factory()->create([
        'title' => 'Mastering Laravel Cloud',
        'category_id' => $category->id,
        'status' => CourseStatus::Published,
        'created_by' => $admin->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab 1: Pengenalan',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Setup Lingkungan Kerja',
        'body_text' => '<p>Instalasi framework</p>',
        'order' => 1,
    ]);

    $peserta = User::factory()->create([
        'name' => 'Budi Santoso',
        'email' => 'budi@acehtimurkab.go.id',
        'role' => 'peserta',
    ]);

    CourseUser::create([
        'user_id' => $peserta->id,
        'course_id' => $course->id,
        'registration_number' => 'REG-2026-0001',
        'status' => RegistrationStatus::Active,
        'enrolled_at' => now(),
    ]);

    $quiz = Quiz::create([
        'course_id' => $course->id,
        'title' => 'Kuis Pengenalan',
        'type' => 'chapter',
        'time_limit_minutes' => 15,
        'passing_score' => 70,
        'total_score' => 100,
        'created_by' => $admin->id,
    ]);

    QuizAttempt::create([
        'quiz_id' => $quiz->id,
        'user_id' => $peserta->id,
        'total_earned_score' => 80,
        'total_possible_score' => 100,
        'percentage' => 80.0,
        'is_passed' => true,
        'started_at' => now()->subMinutes(10),
        'submitted_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Beranda Administrator')
        ->assertSee('Mastering Laravel Cloud')
        ->assertSee('Budi Santoso')
        ->assertSee('REG-2026-0001')
        ->assertSee('Teknologi Informasi');
});
