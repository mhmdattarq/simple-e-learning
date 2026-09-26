<?php

use App\Livewire\Admin\Materi\MateriSesi;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->category = Category::factory()->create();
    $this->course = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Pelatihan Transformasi Digital Aceh Timur',
        'type' => 'batch',
        'status' => 'published',
    ]);

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Fakhrurrazi, M.Kom',
    ]);
    $this->otherMentor = User::factory()->mentor()->create([
        'name' => 'Dr. Zulkifli, S.T',
    ]);

    $this->schedule1 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 1: Pengenalan Platform E-Learning',
        'session_date' => now()->toDateString(),
        'start_time' => '08:30:00',
        'end_time' => '11:30:00',
        'room_or_link' => 'Lab Komputer 1',
        'status' => 'ongoing',
    ]);

    $this->schedule2 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 2: Tata Kelola Keamanan Siber',
        'session_date' => now()->addDays(1)->toDateString(),
        'start_time' => '08:30:00',
        'end_time' => '11:30:00',
        'room_or_link' => 'Lab Komputer 2',
        'status' => 'scheduled',
    ]);
});

test('admin can access materi sesi list page', function () {
    $this->actingAs($this->admin)
        ->get(route('materi.sesi', $this->course->id))
        ->assertStatus(200)
        ->assertSee('Daftar Sesi Pelatihan')
        ->assertSee($this->course->title)
        ->assertSee($this->schedule1->session_title)
        ->assertSee($this->schedule2->session_title);
});

test('assigned mentor can access materi sesi list page', function () {
    $this->actingAs($this->mentor)
        ->get(route('materi.sesi', $this->course->id))
        ->assertStatus(200)
        ->assertSee('Daftar Sesi Pelatihan')
        ->assertSee($this->schedule1->session_title);
});

test('unassigned mentor cannot access materi sesi list page', function () {
    $this->actingAs($this->otherMentor)
        ->get(route('materi.sesi', $this->course->id))
        ->assertStatus(403);
});

test('materi sesi page displays chapter and lesson count per session', function () {
    $chapter = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab 1: Fundamental Keamanan',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Video Pengantar',
        'order' => 1,
        'content_type' => 'video',
    ]);

    Livewire::actingAs($this->admin)
        ->test(MateriSesi::class, ['id' => $this->course->id])
        ->assertSee('1')
        ->assertSee('Bab Materi')
        ->assertSee('Sudah Ada Materi')
        ->assertSee(route('materi.detail', ['id' => $this->course->id, 'schedule_id' => $this->schedule1->id]));
});

test('materi sesi page displays empty state when no sessions scheduled', function () {
    $emptyCourse = Course::factory()->create([
        'category_id' => $this->category->id,
        'title' => 'Pelatihan Tanpa Sesi',
    ]);

    $this->actingAs($this->admin)
        ->get(route('materi.sesi', $emptyCourse->id))
        ->assertStatus(200)
        ->assertSee('Belum Ada Sesi Pelatihan Terjadwal');
});
