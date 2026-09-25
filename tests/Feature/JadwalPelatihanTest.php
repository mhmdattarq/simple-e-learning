<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman jadwal pelatihan dapat diakses oleh publik', function () {
    $response = $this->get(route('jadwal'));

    $response->assertStatus(200);
    $response->assertSee('Jadwal Pelatihan');
    $response->assertSee('Agenda Resmi BKPSDM');
});

test('halaman jadwal menampilkan kursus batch published yang ada', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Pelatihan Teknis']);

    $batch = Course::factory()->create([
        'title' => 'Pelatihan Cyber Security Batch I',
        'status' => 'published',
        'type' => 'batch',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'start_date' => '2026-11-01',
        'end_date' => '2026-11-15',
    ]);

    $response = $this->get(route('jadwal'));

    $response->assertStatus(200);
    $response->assertSee('Pelatihan Cyber Security Batch I');
    $response->assertSee('Pelatihan Teknis');
    $response->assertSee('Daftar Pelatihan');
    $response->assertSee(route('pelatihan.daftar', $batch->id), false);
});

test('halaman jadwal tidak menampilkan kursus permanent atau yang berstatus draft', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Course::factory()->create([
        'title' => 'Kursus Permanent Yang Tidak Tampil',
        'status' => 'published',
        'type' => 'permanent',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'start_date' => null,
        'end_date' => null,
    ]);

    Course::factory()->create([
        'title' => 'Draft Batch Yang Tidak Tampil',
        'status' => 'draft',
        'type' => 'batch',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'start_date' => '2026-12-01',
        'end_date' => '2026-12-15',
    ]);

    $response = $this->get(route('jadwal'));

    $response->assertStatus(200);
    $response->assertDontSee('Kursus Permanent Yang Tidak Tampil');
    $response->assertDontSee('Draft Batch Yang Tidak Tampil');
});

test('halaman jadwal menampilkan empty state jika tidak ada jadwal batch', function () {
    $response = $this->get(route('jadwal'));

    $response->assertStatus(200);
    $response->assertSee('Belum Ada Jadwal Pelatihan');
    $response->assertSee('Lihat Katalog Pelatihan');
});

test('menu navbar jadwal pelatihan aktif ketika berada di halaman jadwal', function () {
    $response = $this->get(route('jadwal'));

    $response->assertStatus(200);
    $response->assertSee('Jadwal Pelatihan');
});
