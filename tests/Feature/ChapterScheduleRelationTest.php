<?php

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('chapter can be associated with a course schedule', function () {
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'category_id' => $category->id,
        'title' => 'Pelatihan Arsitektur Sistem Informasi',
    ]);

    $mentor = User::factory()->mentor()->create();

    $schedule = CourseSchedule::create([
        'course_id' => $course->id,
        'mentor_id' => $mentor->id,
        'session_title' => 'Sesi 1: Pengantar Keamanan Microservices',
        'session_date' => now()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '11:00:00',
        'room_or_link' => 'Lab Komputer 1',
        'status' => 'ongoing',
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'schedule_id' => $schedule->id,
        'title' => 'Bab 1: Konsep Dasar Microservices',
        'order' => 1,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Pengenalan Service Mesh',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Materi pengenalan service mesh.</p>',
    ]);

    // Relasi dari Chapter ke Schedule
    expect($chapter->schedule)->not->toBeNull()
        ->and($chapter->schedule->id)->toBe($schedule->id)
        ->and($chapter->schedule->session_title)->toBe('Sesi 1: Pengantar Keamanan Microservices');

    // Relasi dari Schedule ke Chapters
    $schedule->refresh();
    expect($schedule->chapters)->toHaveCount(1)
        ->and($schedule->chapters->first()->id)->toBe($chapter->id)
        ->and($schedule->chapters->first()->lessons)->toHaveCount(1);
});

test('chapter schedule_id is nullable for existing courses without bound session', function () {
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'category_id' => $category->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'schedule_id' => null,
        'title' => 'Bab Umum',
        'order' => 1,
    ]);

    expect($chapter->schedule)->toBeNull();
});
