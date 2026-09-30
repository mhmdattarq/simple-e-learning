<?php

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('detail kelas page renders course details and syllabus correctly', function () {
    $category = Category::factory()->create([
        'name' => 'Teknologi Informasi',
        'description' => 'Pelatihan intensif pengembangan aplikasi pemerintahan modern.',
    ]);
    $admin = User::factory()->admin()->create();

    $course = Course::factory()->create([
        'title' => 'Mastering Laravel dan Livewire ASN',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab 1: Pengenalan Komponen Livewire',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Pelajaran 1.1: Instalasi & Konfigurasi Dasar',
        'content_type' => 'article',
        'body_text' => 'Konten teks pelajaran instalasi.',
        'order' => 1,
    ]);

    $response = $this->get(route('landing.kelas.detail', $course->id));

    $response->assertStatus(200);
    $response->assertSee('Mastering Laravel dan Livewire ASN');
    $response->assertSee('Pelatihan intensif pengembangan aplikasi pemerintahan modern.');
    $response->assertSee('Bab 1: Pengenalan Komponen Livewire');
    $response->assertSee('Pelajaran 1.1: Instalasi & Konfigurasi Dasar');
    $response->assertSee('Kembali ke Katalog');
    $response->assertSee(route('landing.kelas.permanen'));
});

test('detail kelas shows login button for guests and learning link for authenticated users', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->peserta()->create();

    $course = Course::factory()->create([
        'title' => 'Dasar Keamanan Siber ASN',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    // Guest sees login button
    $this->get(route('landing.kelas.detail', $course))
        ->assertStatus(200)
        ->assertSee(route('login'))
        ->assertSee('Masuk untuk Belajar');

    // Authenticated user sees start/continue learning link to materi
    $this->actingAs($user)->get(route('landing.kelas.detail', $course))
        ->assertStatus(200)
        ->assertSee(route('peserta.materi', $course))
        ->assertSee('Mulai Belajar Sekarang');
});

test('backward compatibility redirect works from /pelatihan/{id} to /kelas/{id}', function () {
    $category = Category::factory()->create();
    $course = Course::factory()->create([
        'title' => 'Kelas Arsip Digital',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
    ]);

    $response = $this->get('/pelatihan/'.$course->id);
    $response->assertRedirect(route('landing.kelas.detail', $course));
});

test('flow from materi back button redirects to detail kelas instead of landing', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->peserta()->create();

    $course = Course::factory()->create([
        'title' => 'Manajemen ASN Era Digital',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab Pengantar',
        'order' => 1,
    ]);
    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Pembuka',
        'content_type' => 'article',
        'order' => 1,
    ]);

    $response = $this->actingAs($user)->get(route('peserta.materi', $course));

    $response->assertStatus(200);
    $response->assertSee(route('landing.kelas.detail', $course));
    $response->assertSee('Kembali ke Detail Kelas');
});

test('end-to-end routing flow: katalog master -> detail kelas -> materi', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->peserta()->create();

    $course = Course::factory()->create([
        'title' => 'Pelatihan AI Terapan BKPSDM',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab 1 AI',
        'order' => 1,
    ]);
    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi 1 Pengantar AI',
        'content_type' => 'article',
        'order' => 1,
    ]);

    // 1. Katalog Master has link to Detail Kelas
    $katalogResponse = $this->get(route('landing.kelas.permanen'));
    $katalogResponse->assertStatus(200);
    $katalogResponse->assertSee(route('landing.kelas.detail', $course));

    // 2. Detail Kelas has link to Materi and back to Katalog Master
    $detailResponse = $this->actingAs($user)->get(route('landing.kelas.detail', $course));
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee(route('peserta.materi', $course));
    $detailResponse->assertSee(route('landing.kelas.permanen'));

    // 3. Materi has back link to Detail Kelas
    $materiResponse = $this->actingAs($user)->get(route('peserta.materi', $course));
    $materiResponse->assertStatus(200);
    $materiResponse->assertSee(route('landing.kelas.detail', $course));
});

test('detail kelas and catalog render custom course description set by admin', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();

    $course = Course::factory()->create([
        'title' => 'Kelas Spesialis Keamanan Data',
        'description' => 'Ini adalah deskripsi khusus kelas keamanan data dari admin.',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    // Check on detail page
    $detailResponse = $this->get(route('landing.kelas.detail', $course->id));
    $detailResponse->assertStatus(200);
    $detailResponse->assertSee('Ini adalah deskripsi khusus kelas keamanan data dari admin.');

    // Check on catalog page
    $katalogResponse = $this->get(route('landing.kelas.permanen'));
    $katalogResponse->assertStatus(200);
    $katalogResponse->assertSee('Ini adalah deskripsi khusus kelas keamanan data dari admin.');
});
