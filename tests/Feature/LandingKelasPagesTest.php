<?php

use App\Livewire\Landing\KelasBatch;
use App\Livewire\Landing\KelasBerbayar;
use App\Livewire\Landing\KelasPermanen;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Attributes\Url;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('landing navbar renders menu items for Beranda, Kelas Batch, Kelas Permanen, Kelas Berbayar, and Kontak', function () {
    $response = $this->get(route('landing'));

    $response->assertStatus(200);
    $response->assertSee(route('landing'));
    $response->assertSee(route('landing.kelas.batch'));
    $response->assertSee(route('landing.kelas.permanen'));
    $response->assertSee(route('landing.kelas.berbayar'));
    $response->assertSee('Kelas Batch');
    $response->assertSee('Kelas Permanen');
    $response->assertSee('Kelas Berbayar');
    $response->assertSee('Kontak');
});

test('navbar highlights current menu depending on active route', function () {
    $responseBatch = $this->get(route('landing.kelas.batch'));
    $responseBatch->assertStatus(200);
    $responseBatch->assertSee('<li class="current">', false);

    $responsePermanen = $this->get(route('landing.kelas.permanen'));
    $responsePermanen->assertStatus(200);
    $responsePermanen->assertSee('<li class="current">', false);

    $responseBerbayar = $this->get(route('landing.kelas.berbayar'));
    $responseBerbayar->assertStatus(200);
    $responseBerbayar->assertSee('<li class="current">', false);
});

test('kelas batch page displays only published batch courses and excludes draft, archived, or other types', function () {
    $category = Category::factory()->create(['name' => 'Kepemimpinan']);
    $admin = User::factory()->admin()->create();

    $publishedBatch = Course::factory()->create([
        'title' => 'Pelatihan Batch Kepemimpinan Administrator',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'start_date' => now()->addDays(5)->format('Y-m-d'),
        'end_date' => now()->addDays(15)->format('Y-m-d'),
    ]);

    $draftBatch = Course::factory()->create([
        'title' => 'Draft Batch Pelatihan Pengawas',
        'type' => 'batch',
        'status' => 'draft',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $archivedBatch = Course::factory()->create([
        'title' => 'Archived Batch Pelatihan Dasar',
        'type' => 'batch',
        'status' => 'archived',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $publishedPermanent = Course::factory()->create([
        'title' => 'Kursus Permanen Literasi Digital',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('landing.kelas.batch'));
    $response->assertStatus(200);
    $response->assertSee('Pelatihan Batch Kepemimpinan Administrator');
    $response->assertDontSee('Draft Batch Pelatihan Pengawas');
    $response->assertDontSee('Archived Batch Pelatihan Dasar');
    $response->assertDontSee('Kursus Permanen Literasi Digital');

    Livewire::test(KelasBatch::class)
        ->assertViewHas('courses', function ($courses) use ($publishedBatch) {
            return $courses->count() === 1
                && $courses->contains('id', $publishedBatch->id);
        });
});

test('kelas permanen page displays only published permanent courses', function () {
    $category = Category::factory()->create(['name' => 'Teknologi Informasi']);
    $admin = User::factory()->admin()->create();

    $publishedPermanent = Course::factory()->create([
        'title' => 'Manajemen Data & Keamanan Informasi ASN',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $publishedBatch = Course::factory()->create([
        'title' => 'Pelatihan Batch Tidak Muncul Disini',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('landing.kelas.permanen'));
    $response->assertStatus(200);
    $response->assertSee('Manajemen Data & Keamanan Informasi ASN');
    $response->assertDontSee('Pelatihan Batch Tidak Muncul Disini');

    Livewire::test(KelasPermanen::class)
        ->assertViewHas('courses', function ($courses) use ($publishedPermanent) {
            return $courses->count() === 1
                && $courses->contains('id', $publishedPermanent->id);
        });
});

test('kelas berbayar page displays only published paid courses and price', function () {
    $category = Category::factory()->create(['name' => 'Sertifikasi Keahlian']);
    $admin = User::factory()->admin()->create();

    $publishedPaid = Course::factory()->create([
        'title' => 'Sertifikasi Ahli Pengadaan Barang dan Jasa Pemerintah',
        'type' => 'paid',
        'price' => 750000,
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $publishedPermanent = Course::factory()->create([
        'title' => 'Kursus Gratis Permanen Tidak Muncul',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get(route('landing.kelas.berbayar'));
    $response->assertStatus(200);
    $response->assertSee('Sertifikasi Ahli Pengadaan Barang dan Jasa Pemerintah');
    $response->assertSee('750.000');
    $response->assertDontSee('Kursus Gratis Permanen Tidak Muncul');

    Livewire::test(KelasBerbayar::class)
        ->assertViewHas('courses', function ($courses) use ($publishedPaid) {
            return $courses->count() === 1
                && $courses->contains('id', $publishedPaid->id);
        });
});

test('livewire search and category filtering works on kelas pages', function () {
    $categoryA = Category::factory()->create(['name' => 'Kategori A']);
    $categoryB = Category::factory()->create(['name' => 'Kategori B']);
    $admin = User::factory()->admin()->create();

    $courseA = Course::factory()->create([
        'title' => 'Pelatihan Khusus Topik Alpha',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $categoryA->id,
        'created_by' => $admin->id,
    ]);

    $courseB = Course::factory()->create([
        'title' => 'Pelatihan Topik Beta',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $categoryB->id,
        'created_by' => $admin->id,
    ]);

    Livewire::test(KelasBatch::class)
        // Search by keyword
        ->set('search', 'Alpha')
        ->assertViewHas('courses', function ($courses) use ($courseA) {
            return $courses->count() === 1 && $courses->contains('id', $courseA->id);
        })
        // Reset and filter by Category B
        ->call('resetFilter')
        ->call('filterCategory', $categoryB->slug)
        ->assertViewHas('courses', function ($courses) use ($courseB) {
            return $courses->count() === 1 && $courses->contains('id', $courseB->id);
        })
        // Reset clears everything
        ->call('resetFilter')
        ->assertSet('search', '')
        ->assertSet('selectedCategory', 'all')
        ->assertViewHas('courses', function ($courses) {
            return $courses->count() === 2;
        });
});

test('kelas pages search and category filter run silently without exposing query params in url', function () {
    $response = $this->get(route('landing.kelas.batch'));
    $response->assertStatus(200);

    $refBatch = new ReflectionClass(KelasBatch::class);
    expect($refBatch->getProperty('search')->getAttributes(Url::class))->toBeEmpty();
    expect($refBatch->getProperty('selectedCategory')->getAttributes(Url::class))->toBeEmpty();

    $refPermanen = new ReflectionClass(KelasPermanen::class);
    expect($refPermanen->getProperty('search')->getAttributes(Url::class))->toBeEmpty();
    expect($refPermanen->getProperty('selectedCategory')->getAttributes(Url::class))->toBeEmpty();

    $refBerbayar = new ReflectionClass(KelasBerbayar::class);
    expect($refBerbayar->getProperty('search')->getAttributes(Url::class))->toBeEmpty();
    expect($refBerbayar->getProperty('selectedCategory')->getAttributes(Url::class))->toBeEmpty();
});

test('setting selectedCategory directly filters courses by category slug across all 3 pages', function () {
    $categoryA = Category::factory()->create(['name' => 'Backend Development', 'slug' => 'backend-development']);
    $categoryB = Category::factory()->create(['name' => 'Frontend Development', 'slug' => 'frontend-development']);
    $admin = User::factory()->admin()->create();

    // Batch courses
    $batchA = Course::factory()->create(['type' => 'batch', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $batchB = Course::factory()->create(['type' => 'batch', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasBatch::class)
        ->set('selectedCategory', 'frontend-development')
        ->assertViewHas('courses', function ($courses) use ($batchB) {
            return $courses->count() === 1 && $courses->contains('id', $batchB->id);
        });

    // Permanen courses
    $permA = Course::factory()->create(['type' => 'permanent', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $permB = Course::factory()->create(['type' => 'permanent', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasPermanen::class)
        ->set('selectedCategory', 'backend-development')
        ->assertViewHas('courses', function ($courses) use ($permA) {
            return $courses->count() === 1 && $courses->contains('id', $permA->id);
        });

    // Berbayar courses
    $paidA = Course::factory()->create(['type' => 'paid', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $paidB = Course::factory()->create(['type' => 'paid', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasBerbayar::class)
        ->set('selectedCategory', 'frontend-development')
        ->assertViewHas('courses', function ($courses) use ($paidB) {
            return $courses->count() === 1 && $courses->contains('id', $paidB->id);
        });
});

test('tombol aksi kelas mengarahkan pengguna login langsung ke ruang materi', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $batchCourse = Course::factory()->create([
        'title' => 'Kelas Batch Spesial',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $permanentCourse = Course::factory()->create([
        'title' => 'Kelas Permanen Spesial',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $paidCourse = Course::factory()->create([
        'title' => 'Kelas Berbayar Spesial',
        'type' => 'paid',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    // Guest sees login button
    $this->get(route('landing.kelas.batch'))
        ->assertStatus(200)
        ->assertSee(route('login'));

    // Authenticated user sees direct link to peserta.materi
    $this->actingAs($user)->get(route('landing.kelas.batch'))
        ->assertStatus(200)
        ->assertSee(route('peserta.materi', $batchCourse->id))
        ->assertSee('Mulai Belajar');

    $this->actingAs($user)->get(route('landing.kelas.permanen'))
        ->assertStatus(200)
        ->assertSee(route('peserta.materi', $permanentCourse->id))
        ->assertSee('Mulai Belajar');

    $this->actingAs($user)->get(route('landing.kelas.berbayar'))
        ->assertStatus(200)
        ->assertSee(route('peserta.materi', $paidCourse->id))
        ->assertSee('Mulai Belajar');
});
