<?php

use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('halaman katalog pelatihan dapat diakses oleh publik', function () {
    $response = $this->get(route('pelatihan.index'));

    $response->assertStatus(200);
    $response->assertSee('Katalog Pelatihan');
    $response->assertSee('Program Diklat ASN');
});

test('halaman katalog pelatihan menampilkan kursus yang berstatus published', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Pelatihan Fungsional']);

    $coursePermanent = Course::factory()->create([
        'title' => 'Pelatihan Tata Kelola Data SPBE',
        'status' => 'published',
        'type' => 'permanent',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $courseBatch = Course::factory()->create([
        'title' => 'Pelatihan Manajemen Risiko ASN',
        'status' => 'published',
        'type' => 'batch',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('pelatihan.index'));

    $response->assertStatus(200);
    $response->assertSee('Pelatihan Tata Kelola Data SPBE');
    $response->assertSee('Pelatihan Manajemen Risiko ASN');
    $response->assertSee('Pelatihan Fungsional');
    $response->assertSee('Mandiri 24/7');
    $response->assertSee('Batch Terjadwal');
    $response->assertSee(route('landing.kelas.detail', $coursePermanent->id));
    $response->assertSee(route('landing.kelas.detail', $courseBatch->id));
    $response->assertSee('Lihat Detail');
});

test('halaman katalog pelatihan tidak menampilkan kursus yang berstatus draft', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Course::factory()->create([
        'title' => 'Draft Pelatihan Rahasia',
        'status' => 'draft',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('pelatihan.index'));

    $response->assertStatus(200);
    $response->assertDontSee('Draft Pelatihan Rahasia');
});

test('halaman katalog pelatihan menampilkan empty state jika tidak ada kursus published', function () {
    $response = $this->get(route('pelatihan.index'));

    $response->assertStatus(200);
    $response->assertSee('Belum Ada');
    $response->assertSee('Kembali ke Beranda');
});

test('menu navbar katalog pelatihan aktif ketika berada di halaman katalog pelatihan', function () {
    $response = $this->get(route('pelatihan.index'));

    $response->assertStatus(200);
    $response->assertSee('Katalog');
});
