<?php

use App\Livewire\Admin\Materi\MateriDetail;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->category = Category::first();

    $this->admin = User::factory()->admin()->create();
    $this->mentor = User::factory()->mentor()->create([
        'name' => 'Fauzan, M.Cs',
    ]);
    $this->otherMentor = User::factory()->mentor()->create([
        'name' => 'Munawar, M.Kom',
    ]);

    $this->course = Course::create([
        'code' => 'TIK-2026-DEV',
        'title' => 'Pengembangan Aplikasi Pelayanan Publik',
        'category_id' => $this->category->id,
        'type' => 'batch',
        'method' => 'luring',
        'quota' => 30,
        'status' => 'published',
        'created_by' => $this->admin->id,
    ]);

    $this->schedule1 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 1: Perancangan Arsitektur Web',
        'session_date' => now()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '11:00:00',
        'room_or_link' => 'Lab Komputer 1',
        'status' => 'ongoing',
    ]);

    $this->schedule2 = CourseSchedule::create([
        'course_id' => $this->course->id,
        'mentor_id' => $this->mentor->id,
        'session_title' => 'Sesi 2: Implementasi API & Basis Data',
        'session_date' => now()->addDay()->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '11:00:00',
        'room_or_link' => 'Lab Komputer 2',
        'status' => 'scheduled',
    ]);
});

test('admin can access materi detail with schedule_id and see session context', function () {
    $this->actingAs($this->admin)
        ->get(route('materi.detail', ['id' => $this->course->id, 'schedule_id' => $this->schedule1->id]))
        ->assertStatus(200)
        ->assertSee($this->schedule1->session_title)
        ->assertSee('Sesi: '.$this->schedule1->session_title)
        ->assertSee(route('materi.sesi', $this->course->id));
});

test('materi detail scopes curriculum chapters to the selected session', function () {
    // Bab untuk Sesi 1
    $chapterSesi1 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab Sesi 1: Wireframing & Prototyping',
        'order' => 1,
    ]);

    // Bab untuk Sesi 2
    $chapterSesi2 = Chapter::create([
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule2->id,
        'title' => 'Bab Sesi 2: Normalisasi Basis Data',
        'order' => 1,
    ]);

    // Akses Sesi 1 -> hanya melihat Bab Sesi 1
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, [
            'id' => $this->course->id,
            'schedule_id' => $this->schedule1->id,
        ])
        ->assertSee('Bab Sesi 1: Wireframing & Prototyping')
        ->assertDontSee('Bab Sesi 2: Normalisasi Basis Data');

    // Akses Sesi 2 -> hanya melihat Bab Sesi 2
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, [
            'id' => $this->course->id,
            'schedule_id' => $this->schedule2->id,
        ])
        ->assertSee('Bab Sesi 2: Normalisasi Basis Data')
        ->assertDontSee('Bab Sesi 1: Wireframing & Prototyping');
});

test('saving chapter inside session context binds chapter to that schedule_id', function () {
    Livewire::actingAs($this->admin)
        ->test(MateriDetail::class, [
            'id' => $this->course->id,
            'schedule_id' => $this->schedule1->id,
        ])
        ->call('saveChapter', [
            'title' => 'Bab Baru Khusus Sesi 1',
            'order' => 1,
        ])
        ->assertDispatched('alert-show');

    $this->assertDatabaseHas('chapters', [
        'course_id' => $this->course->id,
        'schedule_id' => $this->schedule1->id,
        'title' => 'Bab Baru Khusus Sesi 1',
    ]);
});

test('assigned mentor can access session materi detail while unassigned mentor is forbidden', function () {
    // Assigned mentor -> 200
    $this->actingAs($this->mentor)
        ->get(route('materi.detail', ['id' => $this->course->id, 'schedule_id' => $this->schedule1->id]))
        ->assertStatus(200);

    // Unassigned mentor -> 403
    $this->actingAs($this->otherMentor)
        ->get(route('materi.detail', ['id' => $this->course->id, 'schedule_id' => $this->schedule1->id]))
        ->assertStatus(403);
});
