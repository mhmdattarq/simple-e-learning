<?php

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Livewire\Peserta\Materi\MateriBelajar;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('peserta yang login dapat mengakses materi tanpa menunggu verifikasi', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'permanent',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab 1: Pengenalan',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Pengenalan Framework',
        'content_type' => 'article',
        'body_text' => 'Konten pengenalan framework Laravel untuk ASN.',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('peserta.materi', $course->id));
    $response->assertStatus(200);

    // Pastikan peserta otomatis di-enroll dengan status active
    $enrollment = CourseUser::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->first();

    expect($enrollment)->not->toBeNull();
    expect($enrollment->status)->toBe(RegistrationStatus::Active);
    expect($enrollment->registration_number)->not->toBeEmpty();
});

test('peserta dengan status registrasi pending otomatis diaktifkan saat membuka materi', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(5),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    CourseUser::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'status' => RegistrationStatus::Pending,
        'registration_number' => 'REG-TEST-12345',
    ]);

    Livewire::actingAs($user)
        ->test(MateriBelajar::class, ['id' => $course->id])
        ->assertStatus(200);

    $enrollment = CourseUser::where('user_id', $user->id)
        ->where('course_id', $course->id)
        ->first();

    expect($enrollment->status)->toBe(RegistrationStatus::Active);
});

test('tamu yang belum login diarahkan ke halaman login saat mengakses materi', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('peserta.materi', $course->id));
    $response->assertRedirect(route('login'));
});

test('peserta tidak dapat mengakses materi jika batch belum dimulai dan diarahkan ke detail kelas', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->addDays(2),
        'end_date' => now()->addDays(10),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($user)
        ->test(MateriBelajar::class, ['course' => $course])
        ->assertRedirect(route('landing.kelas.detail', $course));
});

test('peserta tidak dapat mengakses materi jika batch telah berakhir dan diarahkan ke detail kelas', function () {
    $user = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->subDays(10),
        'end_date' => now()->subDays(2),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($user)
        ->test(MateriBelajar::class, ['course' => $course])
        ->assertRedirect(route('landing.kelas.detail', $course));
});

test('admin tetap dapat mengakses materi kelas batch meskipun belum dimulai', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'status' => CourseStatus::Published,
        'type' => 'batch',
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(15),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(MateriBelajar::class, ['course' => $course])
        ->assertOk();
});
