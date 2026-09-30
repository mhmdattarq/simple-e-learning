<?php

use App\Enums\CourseStatus;
use App\Livewire\Peserta\Materi\MateriBelajar;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('kondisi 1: bab tidak ada evaluasi dan ada bab berikutnya maka tombol menampilkan Selanjutnya dan lanjut ke bab berikutnya', function () {
    $course = Course::create([
        'title' => 'Kursus Navigasi 1',
        'slug' => 'kursus-navigasi-1',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Published,
    ]);

    $ch1 = Chapter::create(['course_id' => $course->id, 'title' => 'Bab 1', 'order' => 1]);
    $ch2 = Chapter::create(['course_id' => $course->id, 'title' => 'Bab 2', 'order' => 2]);

    $l1 = Lesson::create(['chapter_id' => $ch1->id, 'title' => 'Materi Bab 1', 'order' => 1, 'content_type' => 'article']);
    $l2 = Lesson::create(['chapter_id' => $ch2->id, 'title' => 'Materi Bab 2', 'order' => 1, 'content_type' => 'article']);

    // Di materi terakhir Bab 1 tanpa kuis bab
    Livewire::actingAs($this->user)
        ->test(MateriBelajar::class, ['id' => $course->id])
        ->set('selectedLessonId', $l1->id)
        ->assertSee('Selanjutnya')
        ->call('confirmCompleteChapter')
        ->assertSet('selectedLessonId', $l2->id);
});

test('kondisi 2: bab memiliki evaluasi bab maka tombol Selesai mengarahkan ke kuis evaluasi bab', function () {
    $course = Course::create([
        'title' => 'Kursus Navigasi 2',
        'slug' => 'kursus-navigasi-2',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Published,
    ]);

    $ch1 = Chapter::create(['course_id' => $course->id, 'title' => 'Bab 1', 'order' => 1]);
    $l1 = Lesson::create(['chapter_id' => $ch1->id, 'title' => 'Materi Bab 1', 'order' => 1, 'content_type' => 'article']);

    $quiz = Quiz::create([
        'course_id' => $course->id,
        'chapter_id' => $ch1->id,
        'type' => 'chapter',
        'title' => 'Kuis Bab 1',
        'passing_score' => 70,
    ]);

    Livewire::actingAs($this->user)
        ->test(MateriBelajar::class, ['id' => $course->id])
        ->set('selectedLessonId', $l1->id)
        ->assertSee('Selesai')
        ->call('confirmCompleteChapter')
        ->assertRedirect(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz_id' => $quiz->id]));
});

test('kondisi 3: seluruh bab selesai di akhir pembelajaran tanpa kuis bab langsung mengarahkan ke ujian akhir final quiz', function () {
    $course = Course::create([
        'title' => 'Kursus Navigasi 3',
        'slug' => 'kursus-navigasi-3',
        'category_id' => $this->category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Published,
    ]);

    $ch1 = Chapter::create(['course_id' => $course->id, 'title' => 'Bab 1', 'order' => 1]);
    $l1 = Lesson::create(['chapter_id' => $ch1->id, 'title' => 'Materi Bab 1', 'order' => 1, 'content_type' => 'article']);

    $finalQuiz = Quiz::create([
        'course_id' => $course->id,
        'chapter_id' => null,
        'type' => 'final',
        'title' => 'Ujian Akhir Kelulusan',
        'passing_score' => 75,
    ]);

    Livewire::actingAs($this->user)
        ->test(MateriBelajar::class, ['id' => $course->id])
        ->set('selectedLessonId', $l1->id)
        ->assertSee('Selesai')
        ->call('confirmCompleteChapter')
        ->assertRedirect(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz_id' => $finalQuiz->id]));
});
