<?php

use App\Livewire\Landing\KelasIndex;
use App\Models\Category;
use App\Models\Course;
use App\Models\Quiz;
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

    Livewire::test(KelasIndex::class, ['type' => 'batch'])
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

    Livewire::test(KelasIndex::class, ['type' => 'permanent'])
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

    Livewire::test(KelasIndex::class, ['type' => 'paid'])
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

    Livewire::test(KelasIndex::class, ['type' => 'batch'])
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

    $ref = new ReflectionClass(KelasIndex::class);
    expect($ref->getProperty('search')->getAttributes(Url::class))->toBeEmpty();
    expect($ref->getProperty('selectedCategory')->getAttributes(Url::class))->toBeEmpty();
});

test('setting selectedCategory directly filters courses by category slug across all 3 pages', function () {
    $categoryA = Category::factory()->create(['name' => 'Backend Development', 'slug' => 'backend-development']);
    $categoryB = Category::factory()->create(['name' => 'Frontend Development', 'slug' => 'frontend-development']);
    $admin = User::factory()->admin()->create();

    // Batch courses
    $batchA = Course::factory()->create(['type' => 'batch', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $batchB = Course::factory()->create(['type' => 'batch', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasIndex::class, ['type' => 'batch'])
        ->set('selectedCategory', 'frontend-development')
        ->assertViewHas('courses', function ($courses) use ($batchB) {
            return $courses->count() === 1 && $courses->contains('id', $batchB->id);
        });

    // Permanen courses
    $permA = Course::factory()->create(['type' => 'permanent', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $permB = Course::factory()->create(['type' => 'permanent', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasIndex::class, ['type' => 'permanent'])
        ->set('selectedCategory', 'backend-development')
        ->assertViewHas('courses', function ($courses) use ($permA) {
            return $courses->count() === 1 && $courses->contains('id', $permA->id);
        });

    // Berbayar courses
    $paidA = Course::factory()->create(['type' => 'paid', 'status' => 'published', 'category_id' => $categoryA->id, 'created_by' => $admin->id]);
    $paidB = Course::factory()->create(['type' => 'paid', 'status' => 'published', 'category_id' => $categoryB->id, 'created_by' => $admin->id]);

    Livewire::test(KelasIndex::class, ['type' => 'paid'])
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

    // Guest and Authenticated users see direct link to detail page
    $this->get(route('landing.kelas.batch'))
        ->assertStatus(200)
        ->assertSee(route('landing.kelas.detail', $batchCourse))
        ->assertSee('Lihat Detail');

    $this->actingAs($user)->get(route('landing.kelas.batch'))
        ->assertStatus(200)
        ->assertSee(route('landing.kelas.detail', $batchCourse))
        ->assertSee('Lihat Detail');

    $this->actingAs($user)->get(route('landing.kelas.permanen'))
        ->assertStatus(200)
        ->assertSee(route('landing.kelas.detail', $permanentCourse))
        ->assertSee('Lihat Detail');

    $this->actingAs($user)->get(route('landing.kelas.berbayar'))
        ->assertStatus(200)
        ->assertSee(route('landing.kelas.detail', $paidCourse))
        ->assertSee('Lihat Detail');
});

test('new category without courses appears in category filter dropdown on landing pages', function () {
    $newCategory = Category::factory()->create([
        'name' => 'Kategori Baru Tanpa Kelas',
        'slug' => 'kategori-baru-tanpa-kelas',
    ]);

    Livewire::test(KelasIndex::class, ['type' => 'batch'])
        ->assertViewHas('categories', function ($categories) use ($newCategory) {
            return $categories->contains('id', $newCategory->id);
        })
        ->assertSee('Kategori Baru Tanpa Kelas');

    Livewire::test(KelasIndex::class, ['type' => 'permanent'])
        ->assertViewHas('categories', function ($categories) use ($newCategory) {
            return $categories->contains('id', $newCategory->id);
        })
        ->assertSee('Kategori Baru Tanpa Kelas');

    Livewire::test(KelasIndex::class, ['type' => 'paid'])
        ->assertViewHas('categories', function ($categories) use ($newCategory) {
            return $categories->contains('id', $newCategory->id);
        })
        ->assertSee('Kategori Baru Tanpa Kelas');
});

test('navbar active menu reflects current page and course context across index, detail, and materi', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->peserta()->create();

    $batchCourse = Course::factory()->create([
        'title' => 'Kelas Batch Spesial ASN',
        'type' => 'batch',
        'start_date' => now()->subDay(),
        'end_date' => now()->addDays(5),
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $permanentCourse = Course::factory()->create([
        'title' => 'Kelas Permanen Spesial ASN',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $paidCourse = Course::factory()->create([
        'title' => 'Kelas Berbayar Spesial ASN',
        'type' => 'paid',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    // 1. Landing Beranda
    $resHome = $this->get(route('landing'));
    $resHome->assertStatus(200);
    $resHome->assertSeeInOrder(['<li class="current"', 'Beranda']);

    // 2. Kelas Batch Index, Detail, and Materi
    $resBatchIndex = $this->get(route('landing.kelas.batch'));
    $resBatchIndex->assertStatus(200);
    $resBatchIndex->assertSeeInOrder(['<li class="current"', 'Kelas Batch']);

    $resBatchDetail = $this->get(route('landing.kelas.detail', $batchCourse->id));
    $resBatchDetail->assertStatus(200);
    $resBatchDetail->assertSeeInOrder(['<li class="current"', 'Kelas Batch']);

    $resBatchMateri = $this->actingAs($user)->get(route('peserta.materi', $batchCourse->id));
    $resBatchMateri->assertStatus(200);
    $resBatchMateri->assertSeeInOrder(['<li class="current"', 'Kelas Batch']);

    // 3. Kelas Permanen Index, Detail, and Materi
    $resPermIndex = $this->get(route('landing.kelas.permanen'));
    $resPermIndex->assertStatus(200);
    $resPermIndex->assertSeeInOrder(['<li class="current"', 'Kelas Permanen']);

    $resPermDetail = $this->get(route('landing.kelas.detail', $permanentCourse->id));
    $resPermDetail->assertStatus(200);
    $resPermDetail->assertSeeInOrder(['<li class="current"', 'Kelas Permanen']);

    $resPermMateri = $this->actingAs($user)->get(route('peserta.materi', $permanentCourse->id));
    $resPermMateri->assertStatus(200);
    $resPermMateri->assertSeeInOrder(['<li class="current"', 'Kelas Permanen']);

    $quiz = Quiz::factory()->create([
        'course_id' => $permanentCourse->id,
        'title' => 'Evaluasi Permanen',
        'type' => 'final',
    ]);
    $evaluasiUrl = route('peserta.evaluasi.kerjakan', ['course' => $permanentCourse, 'quiz_id' => $quiz->id]);
    expect($evaluasiUrl)->toContain('/kelas-permanen/'.$permanentCourse->slug.'/evaluasi/'.$quiz->slug);
    expect($evaluasiUrl)->not->toContain('/evaluasi/'.$quiz->id);

    $resPermEvaluasi = $this->actingAs($user)->get($evaluasiUrl);
    $resPermEvaluasi->assertStatus(200);
    $resPermEvaluasi->assertSeeInOrder(['<li class="current"', 'Kelas Permanen']);

    // Redirect when accessing via numeric ID
    $rawIdUrl = url('/kelas-permanen/'.$permanentCourse->slug.'/evaluasi/'.$quiz->id);
    $resRawRedirect = $this->actingAs($user)->get($rawIdUrl);
    $resRawRedirect->assertRedirect($evaluasiUrl);

    // Redirect when accessing standalone /evaluasi/kerjakan/{quiz}
    $resStandaloneRedirect = $this->actingAs($user)->get(route('peserta.evaluasi.show', ['quiz' => $quiz->slug]));
    $resStandaloneRedirect->assertRedirect($evaluasiUrl);

    // 4. Kelas Berbayar Index, Detail, Materi, and Evaluasi
    $resPaidIndex = $this->get(route('landing.kelas.berbayar'));
    $resPaidIndex->assertStatus(200);
    $resPaidIndex->assertSeeInOrder(['<li class="current"', 'Kelas Berbayar']);

    $resPaidDetail = $this->get(route('landing.kelas.detail', $paidCourse->id));
    $resPaidDetail->assertStatus(200);
    $resPaidDetail->assertSeeInOrder(['<li class="current"', 'Kelas Berbayar']);

    $resPaidMateri = $this->actingAs($user)->get(route('peserta.materi', $paidCourse->id));
    $resPaidMateri->assertStatus(200);
    $resPaidMateri->assertSeeInOrder(['<li class="current"', 'Kelas Berbayar']);

    $paidQuiz = Quiz::factory()->create([
        'course_id' => $paidCourse->id,
        'title' => 'Evaluasi Berbayar',
        'type' => 'final',
    ]);
    $paidEvaluasiUrl = route('peserta.evaluasi.kerjakan', ['course' => $paidCourse, 'quiz' => $paidQuiz]);
    expect($paidEvaluasiUrl)->toContain('/kelas-berbayar/'.$paidCourse->slug.'/evaluasi/'.$paidQuiz->slug);
    $resPaidEvaluasi = $this->actingAs($user)->get($paidEvaluasiUrl);
    $resPaidEvaluasi->assertStatus(200);
    $resPaidEvaluasi->assertSeeInOrder(['<li class="current"', 'Kelas Berbayar']);
});

test('batch course detail page displays Batch Belum Dibuka for upcoming batch and Batch Telah Berakhir for expired batch', function () {
    $category = Category::factory()->create();
    $admin = User::factory()->admin()->create();
    $user = User::factory()->peserta()->create();

    $futureBatch = Course::factory()->create([
        'title' => 'Batch Masa Depan',
        'type' => 'batch',
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(15),
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $expiredBatch = Course::factory()->create([
        'title' => 'Batch Telah Lalu',
        'type' => 'batch',
        'start_date' => now()->subDays(20),
        'end_date' => now()->subDays(5),
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $resFuture = $this->actingAs($user)->get(route('landing.kelas.detail', $futureBatch));
    $resFuture->assertOk();
    $resFuture->assertSee('Batch Belum Dibuka');
    $resFuture->assertSee('Materi dapat diakses mulai');

    $resExpired = $this->actingAs($user)->get(route('landing.kelas.detail', $expiredBatch));
    $resExpired->assertOk();
    $resExpired->assertSee('Batch Telah Berakhir');
    $resExpired->assertSee('Masa pembelajaran kelas ini telah selesai pada');
});
