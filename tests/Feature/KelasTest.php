<?php

use App\Enums\CourseStatus;
use App\Enums\RegistrationStatus;
use App\Livewire\Admin\Kelas\KelasCreate;
use App\Livewire\Admin\Kelas\KelasData;
use App\Livewire\Admin\Kelas\KelasEdit;
use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseUser;
use App\Models\Lesson;
use App\Models\User;
use App\Repositories\KelasRepo;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
});

test('unauthorized users cannot access kelas routes', function () {
    // Guest redirected to login
    $this->get(route('kelas.data'))->assertRedirect(route('login'));
    $this->get(route('kelas.create'))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)->get(route('kelas.data'))->assertStatus(403);
    $this->actingAs($peserta)->get(route('kelas.create'))->assertStatus(403);
});

test('authorized internal roles can access kelas index page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('kelas.data'));

    $response->assertStatus(200);
    $response->assertSee('Data Kelas');
    $response->assertSee('tableKelas');

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->assertOk()
        ->assertSee('Daftar Kelas');
});

test('kelas datatable endpoint returns valid yajra json response', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    Course::create([
        'title' => 'Pelatihan Manajemen Administrator Angkatan I',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)
        ->getJson(route('kelas.dt'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['title'])->toBe('Pelatihan Manajemen Administrator Angkatan I');
    expect($data[0])->toHaveKeys(['chapters_count', 'lessons_count']);
    expect($data[0]['chapters_count'])->toBe(0);
    expect($data[0]['lessons_count'])->toBe(0);
});

test('kelas create validates required fields and batch dates', function () {
    $admin = User::factory()->admin()->create();

    // 1. Validates required fields (All text inputs)
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', '')
        ->set('form.category_id', '')
        ->set('form.category_name', '')
        ->set('form.description', '')
        ->call('formSubmit')
        ->assertHasErrors([
            'form.title',
            'form.category_id',
            'form.description',
        ])
        ->assertSee('Nama kelas wajib diisi.')
        ->assertSee('Kategori kelas wajib dipilih.')
        ->assertSee('Deskripsi kelas wajib diisi.')
        ->assertDontSee('The Kategori Kelas field is required when Kategori Kelas is not present.');

    // 2. Validates batch requires start_date and end_date
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Batch Kepemimpinan')
        ->set('form.category_name', 'Pelatihan Kepemimpinan')
        ->set('form.description', 'Deskripsi kelas pelatihan batch kepemimpinan aparatur sipil negara.')
        ->set('form.type', 'batch')
        ->set('form.start_date', '')
        ->set('form.end_date', '')
        ->call('formSubmit')
        ->assertHasErrors(['form.start_date', 'form.end_date']);
});

test('kelas create successfully saves course into database and redirects', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    // 1. Simpan & Lanjut Kelola Materi (default redirect to materi.detail)
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Teknis Tata Naskah Dinas Elektronik')
        ->set('form.category_name', $category->name)
        ->set('form.description', 'Deskripsi pelatihan teknis tata naskah dinas elektronik untuk ASN.')
        ->set('form.type', 'permanent')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 1));

    $this->assertDatabaseHas('courses', [
        'slug' => 'pelatihan-teknis-tata-naskah-dinas-elektronik',
        'title' => 'Pelatihan Teknis Tata Naskah Dinas Elektronik',
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // 2. Simpan dengan status Draft yang dipilih dari dropdown status
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Draf Dasar')
        ->set('form.category_name', $category->name)
        ->set('form.description', 'Deskripsi pelatihan draf dasar kompetensi aparatur.')
        ->set('form.type', 'permanent')
        ->set('form.status', 'draft')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 2));

    $this->assertDatabaseHas('courses', [
        'title' => 'Pelatihan Draf Dasar',
        'status' => 'draft',
    ]);
});

test('kelas edit mounts existing data and successfully updates course', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Fungsional Analis Kebijakan',
        'description' => 'Deskripsi pelatihan fungsional analis kebijakan tingkat pertama.',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasEdit::class, ['id' => $course->id])
        ->assertSet('form.title', 'Pelatihan Fungsional Analis Kebijakan')
        ->assertSet('form.category_name', $category->name)
        ->set('form.title', 'Pelatihan Fungsional Analis Kebijakan Tk. Madya')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('kelas.data'));

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Pelatihan Fungsional Analis Kebijakan Tk. Madya',
        'status' => 'draft',
    ]);
});

test('admin can edit and update courses with any status including published', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $publishedCourse = Course::create([
        'title' => 'Pelatihan Sudah Dibuka',
        'description' => 'Deskripsi pelatihan yang sudah dibuka untuk umum.',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Published,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasEdit::class, ['id' => $publishedCourse->id])
        ->assertOk()
        ->assertSet('form.title', 'Pelatihan Sudah Dibuka')
        ->set('form.title', 'Pelatihan Sudah Dibuka Diperbarui')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('kelas.data'));

    expect($publishedCourse->fresh()->title)->toBe('Pelatihan Sudah Dibuka Diperbarui');
});

test('kelas delete event deletes course from database and dispatches events', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Uji Coba Hapus',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('hookModalDelete', $course->id, $course->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->dispatch('KelasData-delete', ['id' => $course->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT', data: 'dtTable');

    $this->assertSoftDeleted('courses', [
        'id' => $course->id,
    ]);
});

test('header user profile dropdown and logout form render on beranda, data, create, and edit pages', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Pengujian Dropdown',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    $routes = [
        route('admin.dashboard'),
        route('kelas.data'),
        route('kelas.create'),
        route('kelas.edit', $course->id),
    ];

    foreach ($routes as $url) {
        $response = $this->actingAs($admin)->get($url);
        $response->assertOk();
        $response->assertSee('Profil Saya');
        $response->assertSee('Pengaturan Akun');
        $response->assertSee('Keluar');
        $response->assertSee(route('logout'));
    }
});

test('admin can create and save classes with paid, batch, and permanent types', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    // 1. Paid Class
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Pemrograman Fullstack Web')
        ->set('form.category_id', $category->id)
        ->set('form.description', 'Kelas pemrograman fullstack web modern dengan framework terpercaya.')
        ->set('form.type', 'paid')
        ->set('form.price', 350000)
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 1));

    $this->assertDatabaseHas('courses', [
        'title' => 'Kelas Pemrograman Fullstack Web',
        'type' => 'paid',
        'price' => 350000,
    ]);

    // 2. Batch Class with dates
    $batchStart = now()->addDay()->format('Y-m-d\TH:i');
    $batchEnd = now()->addDays(15)->format('Y-m-d\TH:i');

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Batch Kepemimpinan 2026')
        ->set('form.category_id', $category->id)
        ->set('form.description', 'Kelas batch kepemimpinan bagi aparatur sipil negara di lingkungan pemda.')
        ->set('form.type', 'batch')
        ->set('form.start_date', $batchStart)
        ->set('form.end_date', $batchEnd)
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 2));

    $this->assertDatabaseHas('courses', [
        'title' => 'Kelas Batch Kepemimpinan 2026',
        'type' => 'batch',
        'price' => 0,
    ]);
});

test('admin can archive completed course in kelas', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Manajemen Risiko SPBE',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => CourseStatus::Completed,
        'created_by' => $admin->id,
    ]);

    // Completed -> Archived (Diarsipkan)
    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('archiveCourse', $course->id)
        ->assertDispatched('alert-show');
    expect($course->fresh()->status)->toBe(CourseStatus::Archived);
});

test('kelas create auto-creates new category when user inputs a novel category name', function () {
    $admin = User::factory()->admin()->create();

    $novelCategoryName = 'Pelatihan Keamanan Siber ASN';

    // Pastikan kategori belum ada
    $this->assertDatabaseMissing('categories', [
        'name' => $novelCategoryName,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Dasar Keamanan Siber Pemerintah')
        ->set('form.category_name', $novelCategoryName)
        ->set('form.description', 'Materi dasar mengenai keamanan siber dan informasi rahasia dinas.')
        ->set('form.type', 'permanent')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 1));

    $createdCategory = Category::where('name', $novelCategoryName)->first();
    expect($createdCategory)->not->toBeNull();
    expect($createdCategory->slug)->toBe('pelatihan-keamanan-siber-asn');

    $this->assertDatabaseHas('courses', [
        'title' => 'Dasar Keamanan Siber Pemerintah',
        'category_id' => $createdCategory->id,
    ]);
});

test('kelas edit can update category name to a different category', function () {
    $admin = User::factory()->admin()->create();
    $catA = Category::create(['name' => 'Kategori Awal', 'slug' => 'kategori-awal']);

    $course = Course::create([
        'title' => 'Pelatihan Ganti Kategori',
        'description' => 'Deskripsi awal pelatihan sebelum kategori diubah.',
        'category_id' => $catA->id,
        'type' => 'permanent',
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasEdit::class, ['id' => $course->id])
        ->assertSet('form.category_name', 'Kategori Awal')
        ->set('form.category_name', 'Kategori Baru Terverifikasi')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('kelas.data'));

    $catB = Category::where('name', 'Kategori Baru Terverifikasi')->first();
    expect($catB)->not->toBeNull();
    expect($course->fresh()->category_id)->toBe($catB->id);
});

test('kelas create validates title length', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'AB') // less than 3 chars
        ->call('formSubmit')
        ->assertHasErrors([
            'form.title' => 'min',
        ]);
});

test('kelas create validates file upload mime types', function () {
    $admin = User::factory()->admin()->create();

    $invalidFile = UploadedFile::fake()->create('dokumen.txt', 100, 'text/plain');

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('thumbnailFile', $invalidFile)
        ->call('formSubmit')
        ->assertHasErrors(['thumbnailFile']);
});

test('kelas auto-generates slug from title and handles collisions', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    // First course
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Transformasi Digital')
        ->set('form.category_id', $category->id)
        ->set('form.description', 'Pelatihan transformasi digital untuk unit layanan publik.')
        ->set('form.type', 'permanent')
        ->call('formSubmit')
        ->assertHasNoErrors();

    $firstCourse = Course::where('title', 'Pelatihan Transformasi Digital')->first();
    expect($firstCourse)->not->toBeNull();
    expect($firstCourse->slug)->toBe('pelatihan-transformasi-digital');

    // Second course with identical title generates unique slug
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Transformasi Digital')
        ->set('form.category_id', $category->id)
        ->set('form.description', 'Pelatihan transformasi digital untuk unit layanan publik batch kedua.')
        ->set('form.type', 'permanent')
        ->call('formSubmit')
        ->assertHasNoErrors();

    $secondCourse = Course::where('slug', 'pelatihan-transformasi-digital-1')->first();
    expect($secondCourse)->not->toBeNull();
    expect($secondCourse->id)->not->toBe($firstCourse->id);
});

test('kelas create wizard step-by-step navigation and validation works properly', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        // 1. Initial Step is 1
        ->assertSet('currentStep', 1)
        // 2. Fails step 1 validation when empty
        ->call('nextStep')
        ->assertHasErrors(['form.title', 'form.description', 'form.category_id'])
        ->assertSet('currentStep', 1)
        // 3. Fill step 1 and advance to step 2
        ->set('form.title', 'Pelatihan Kepemimpinan Pengawas 2026')
        ->set('form.description', 'Program kepemimpinan pengawas aparatur terstruktur.')
        ->set('form.category_id', $category->id)
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 2)
        // 4. In step 2 with batch type, fails without dates
        ->set('form.type', 'batch')
        ->set('form.start_date', '')
        ->set('form.end_date', '')
        ->call('nextStep')
        ->assertHasErrors(['form.start_date', 'form.end_date'])
        ->assertSet('currentStep', 2)
        // 5. Navigate back to step 1 via previousStep
        ->call('previousStep')
        ->assertSet('currentStep', 1)
        // 6. Navigate forward to step 2 again
        ->call('nextStep')
        ->assertSet('currentStep', 2)
        // 7. Fill valid batch dates and advance to step 3
        ->set('form.start_date', now()->addMonth()->format('Y-m-d\TH:i'))
        ->set('form.end_date', now()->addMonth()->addDays(14)->format('Y-m-d\TH:i'))
        ->call('nextStep')
        ->assertHasNoErrors()
        ->assertSet('currentStep', 3)
        // 8. In step 3, choose draft status and submit
        ->set('form.status', 'draft')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('materi.detail', 1));

    $this->assertDatabaseHas('courses', [
        'title' => 'Pelatihan Kepemimpinan Pengawas 2026',
        'type' => 'batch',
        'status' => 'draft',
    ]);
});

test('kelas create wizard goToStep prevents skipping unvalidated steps but allows jumping back', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->assertSet('currentStep', 1)
        // Cannot jump directly to step 3 without valid step 1 and 2
        ->call('goToStep', 3)
        ->assertHasErrors(['form.title', 'form.description'])
        ->assertSet('currentStep', 1)
        // Fill step 1 and step 2
        ->set('form.title', 'Pelatihan Transformasi Layanan Publik')
        ->set('form.description', 'Deskripsi transformasi pelayanan publik prima untuk masyarakat.')
        ->set('form.category_id', $category->id)
        ->set('form.type', 'permanent')
        // Now can jump to step 3
        ->call('goToStep', 3)
        ->assertHasNoErrors()
        ->assertSet('currentStep', 3)
        // Can jump back to step 1
        ->call('goToStep', 1)
        ->assertSet('currentStep', 1)
        // Can jump back to step 2
        ->call('goToStep', 2)
        ->assertSet('currentStep', 2);
});

test('course with registered participants cannot be deleted via repo or livewire', function () {
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->peserta()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Terproteksi Peserta',
        'category_id' => $category->id,
        'type' => 'batch',
        'status' => 'ongoing',
    ]);

    CourseUser::create([
        'registration_number' => 'REG-TEST-001',
        'course_id' => $course->id,
        'user_id' => $participant->id,
        'status' => RegistrationStatus::Active,
    ]);

    expect(KelasRepo::canBeDeleted($course))->toBeFalse();
    expect(KelasRepo::delete($course->id))->toBeFalse();

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'deleted_at' => null,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('hookModalDelete', $course->id, $course->title)
        ->assertDispatched('alert-show', function ($eventName, $params) {
            return ($params['data']['type'] ?? '') === 'warning';
        })
        ->assertNotDispatched('modal-delete-setDeleteId')
        ->dispatch('KelasData-delete', ['id' => $course->id])
        ->assertDispatched('alert-show', function ($eventName, $params) {
            return ($params['data']['type'] ?? '') === 'warning';
        });

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'deleted_at' => null,
    ]);
});

test('course without registered participants can be soft deleted along with chapters and lessons', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan Siap Dihapus Bersih',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
    ]);

    $chapter = Chapter::create([
        'course_id' => $course->id,
        'title' => 'Bab 1 Uji Coba',
        'order' => 1,
    ]);

    $lesson = Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Uji Coba',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten</p>',
    ]);

    expect(KelasRepo::canBeDeleted($course))->toBeTrue();

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('hookModalDelete', $course->id, $course->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->dispatch('KelasData-delete', ['id' => $course->id])
        ->assertDispatched('alert-show', function ($eventName, $params) {
            return ($params['data']['type'] ?? '') === 'success';
        });

    $this->assertSoftDeleted('courses', ['id' => $course->id]);
    $this->assertSoftDeleted('chapters', ['id' => $chapter->id]);
    $this->assertSoftDeleted('lessons', ['id' => $lesson->id]);
});

test('course can be archived via KelasData component and repo, updating status to archived', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'title' => 'Pelatihan untuk Diarsipkan',
        'category_id' => $category->id,
        'type' => 'batch',
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('archiveCourse', $course->id)
        ->assertDispatched('alert-show', function ($eventName, $params) {
            return ($params['data']['type'] ?? '') === 'success';
        })
        ->assertDispatched('reloadDT', data: 'dtTable');

    expect($course->fresh()->status)->toBe(CourseStatus::Archived);
});

test('hookModalDelete displays enhanced curriculum warning when course has lessons and standard message when empty', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $courseWithLessons = Course::create([
        'title' => 'Pelatihan Memiliki Materi',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
    ]);

    $chapter = Chapter::create([
        'course_id' => $courseWithLessons->id,
        'title' => 'Bab Pembuka',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Materi Bab Pembuka',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten</p>',
    ]);

    $emptyCourse = Course::create([
        'title' => 'Pelatihan Tanpa Materi',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test(KelasData::class)
        ->call('hookModalDelete', $courseWithLessons->id, $courseWithLessons->title)
        ->assertDispatched('modal-delete-setDeleteId', function ($eventName, $params) {
            $payload = $params[0] ?? $params;

            return str_contains($payload['title'] ?? '', 'Peringatan Hapus Kelas & Materi') &&
                str_contains($payload['msg'] ?? '', 'PERHATIAN KURIKULUM & MATERI') &&
                ! str_contains(strtolower($payload['msg'] ?? ''), 'soft delete') &&
                ($payload['msgBoxClass'] ?? '') === 'bg-danger-subtle border-danger text-danger';
        })
        ->call('hookModalDelete', $emptyCourse->id, $emptyCourse->title)
        ->assertDispatched('modal-delete-setDeleteId', function ($eventName, $params) {
            $payload = $params[0] ?? $params;

            return str_contains($payload['title'] ?? '', 'Konfirmasi Hapus Kelas') &&
                ! str_contains($payload['msg'] ?? '', 'PERHATIAN KURIKULUM & MATERI') &&
                ! str_contains(strtolower($payload['msg'] ?? ''), 'soft delete') &&
                ($payload['msgBoxClass'] ?? '') === '';
        });
});

test('kelas datatables query includes chapters, lessons, and registrations count, and excludes soft-deleted courses', function () {
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->peserta()->create();
    $category = Category::first();

    $activeCourse = Course::create([
        'title' => 'Kelas Aktif untuk DT',
        'category_id' => $category->id,
        'type' => 'batch',
        'status' => 'published',
    ]);

    $chapter = Chapter::create([
        'course_id' => $activeCourse->id,
        'title' => 'Bab DT',
        'order' => 1,
    ]);

    Lesson::create([
        'chapter_id' => $chapter->id,
        'title' => 'Lesson DT',
        'order' => 1,
        'content_type' => 'article',
        'body_text' => '<p>Konten</p>',
    ]);

    CourseUser::create([
        'registration_number' => 'REG-DT-001',
        'course_id' => $activeCourse->id,
        'user_id' => $participant->id,
        'status' => RegistrationStatus::Active,
    ]);

    $deletedCourse = Course::create([
        'title' => 'Kelas Terhapus untuk DT',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
    ]);
    $deletedCourse->delete();

    $response = $this->actingAs($admin)->getJson(route('kelas.dt'));
    $response->assertOk();

    $json = $response->json();
    $courseData = collect($json['data'] ?? [])->firstWhere('id', $activeCourse->id);

    expect($courseData)->not->toBeNull();
    expect($courseData['chapters_count'])->toBe(1);
    expect($courseData['lessons_count'])->toBe(1);
    expect($courseData['registrations_count'])->toBe(1);

    $deletedInDt = collect($json['data'] ?? [])->firstWhere('id', $deletedCourse->id);
    expect($deletedInDt)->toBeNull();
});

test('admin dapat menyimpan kelas baru dengan deskripsi pada langkah 1', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Pelatihan Transformasi Digital ASN')
        ->set('form.description', 'Deskripsi lengkap pelatihan transformasi digital untuk pengembangan kompetensi aparatur.')
        ->set('form.category_id', $category->id)
        ->call('nextStep')
        ->set('form.type', 'permanent')
        ->call('nextStep')
        ->set('form.status', 'published')
        ->call('formSubmit', 'index')
        ->assertHasNoErrors();

    $course = Course::where('title', 'Pelatihan Transformasi Digital ASN')->first();
    expect($course)->not->toBeNull();
    expect($course->description)->toBe('Deskripsi lengkap pelatihan transformasi digital untuk pengembangan kompetensi aparatur.');
});

test('admin dapat memperbarui deskripsi kelas pada form edit kelas', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $course = Course::factory()->create([
        'title' => 'Kelas Awal',
        'description' => 'Deskripsi lama',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(KelasEdit::class, ['id' => $course->id])
        ->assertSet('form.description', 'Deskripsi lama')
        ->set('form.description', 'Deskripsi baru yang telah diperbarui')
        ->call('formSubmit')
        ->assertHasNoErrors();

    $course->refresh();
    expect($course->description)->toBe('Deskripsi baru yang telah diperbarui');
});

test('kelas create validates batch start_date cannot be before today', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Batch Validasi Tanggal')
        ->set('form.category_name', 'Kelas Teknis')
        ->set('form.description', 'Deskripsi kelas batch validasi tanggal mulai sebelum hari ini.')
        ->set('form.type', 'batch')
        ->set('form.start_date', now()->subDay()->format('Y-m-d\TH:i'))
        ->set('form.end_date', now()->addDays(2)->format('Y-m-d\TH:i'))
        ->call('formSubmit')
        ->assertHasErrors(['form.start_date' => 'after_or_equal']);
});

test('kelas create validates batch end_date must be strictly after start_date', function () {
    $admin = User::factory()->admin()->create();
    $sameDateTime = now()->addDays(2)->format('Y-m-d\TH:i');

    // 1. Same datetime should fail
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Batch Tanggal Sama')
        ->set('form.category_name', 'Kelas Teknis')
        ->set('form.description', 'Deskripsi kelas batch pengujian tanggal sama.')
        ->set('form.type', 'batch')
        ->set('form.start_date', $sameDateTime)
        ->set('form.end_date', $sameDateTime)
        ->call('formSubmit')
        ->assertHasErrors(['form.end_date' => 'after']);

    // 2. End date before start date should fail
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Batch Tanggal Terbalik')
        ->set('form.category_name', 'Kelas Teknis')
        ->set('form.description', 'Deskripsi kelas batch pengujian tanggal terbalik.')
        ->set('form.type', 'batch')
        ->set('form.start_date', now()->addDays(5)->format('Y-m-d\TH:i'))
        ->set('form.end_date', now()->addDays(2)->format('Y-m-d\TH:i'))
        ->call('formSubmit')
        ->assertHasErrors(['form.end_date' => 'after']);
});

test('kelas create successfully saves batch course with valid datetime', function () {
    $admin = User::factory()->admin()->create();
    $start = now()->addDay()->setHour(9)->setMinute(0)->format('Y-m-d\TH:i');
    $end = now()->addDays(5)->setHour(17)->setMinute(0)->format('Y-m-d\TH:i');

    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.title', 'Kelas Batch Datetime Sukses')
        ->set('form.category_name', 'Kelas Teknis')
        ->set('form.description', 'Deskripsi lengkap kelas batch datetime sukses terverifikasi.')
        ->set('form.type', 'batch')
        ->set('form.start_date', $start)
        ->set('form.end_date', $end)
        ->call('formSubmit', 'index')
        ->assertHasNoErrors();

    $created = Course::where('title', 'Kelas Batch Datetime Sukses')->first();
    expect($created)->not->toBeNull();
    expect($created->start_date)->not->toBeNull();
    expect($created->end_date)->not->toBeNull();
});

test('kelas create validates description requirements and length constraints', function () {
    $admin = User::factory()->admin()->create();

    // 1. Required
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.description', '')
        ->call('formSubmit')
        ->assertHasErrors(['form.description' => 'required'])
        ->assertSee('Deskripsi kelas wajib diisi.');

    // 2. Min length 10
    Livewire::actingAs($admin)
        ->test(KelasCreate::class)
        ->set('form.description', 'Pendek')
        ->call('formSubmit')
        ->assertHasErrors(['form.description' => 'min'])
        ->assertSee('Deskripsi kelas minimal 10 karakter.');
});
