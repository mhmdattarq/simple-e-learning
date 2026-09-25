<?php

use App\Enums\CourseStatus;
use App\Livewire\Admin\Perencanaan\PerencanaanCreate;
use App\Livewire\Admin\Perencanaan\PerencanaanData;
use App\Livewire\Admin\Perencanaan\PerencanaanEdit;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Repositories\PerencanaanRepo;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
});

test('unauthorized users cannot access perencanaan routes', function () {
    // Guest redirected to login
    $this->get(route('perencanaan.data'))->assertRedirect(route('login'));
    $this->get(route('perencanaan.create'))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)->get(route('perencanaan.data'))->assertStatus(403);
    $this->actingAs($peserta)->get(route('perencanaan.create'))->assertStatus(403);
});

test('authorized internal roles can access perencanaan index page', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('perencanaan.data'));

    $response->assertStatus(200);
    $response->assertSee('Tahap 1: Perencanaan Diklat');
    $response->assertSee('tablePerencanaan');
    $response->assertSee('Tambah Pelatihan Baru');

    Livewire::actingAs($admin)
        ->test(PerencanaanData::class)
        ->assertOk()
        ->assertSee('Daftar Pelatihan (Katalog Perencanaan)');
});

test('perencanaan datatable endpoint returns valid yajra json response', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    Course::create([
        'code' => 'PLT-2026-001',
        'title' => 'Pelatihan Manajemen Administrator Angkatan I',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 40,
        'status' => 'published',
        'created_by' => $admin->id,
    ]);

    $response = $this->actingAs($admin)
        ->getJson(route('perencanaan.dt'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['code'])->toBe('PLT-2026-001');
    expect($data[0]['title'])->toBe('Pelatihan Manajemen Administrator Angkatan I');
});

test('perencanaan create validates required fields and batch dates', function () {
    $admin = User::factory()->admin()->create();

    // 1. Validates required fields
    Livewire::actingAs($admin)
        ->test(PerencanaanCreate::class)
        ->set('form.code', '')
        ->set('form.title', '')
        ->set('form.category_id', '')
        ->call('formSubmit')
        ->assertHasErrors(['form.code', 'form.title', 'form.category_id']);

    // 2. Validates batch requires start_date and end_date
    $category = Category::first();
    Livewire::actingAs($admin)
        ->test(PerencanaanCreate::class)
        ->set('form.code', 'PLT-BATCH-01')
        ->set('form.title', 'Pelatihan Batch Kepemimpinan')
        ->set('form.category_id', $category->id)
        ->set('form.type', 'batch')
        ->set('form.start_date', '')
        ->set('form.end_date', '')
        ->call('formSubmit')
        ->assertHasErrors(['form.start_date', 'form.end_date']);
});

test('perencanaan create successfully saves course into database and redirects', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    Livewire::actingAs($admin)
        ->test(PerencanaanCreate::class)
        ->set('form.code', 'PLT-2026-009')
        ->set('form.title', 'Pelatihan Teknis Tata Naskah Dinas Elektronik')
        ->set('form.category_id', $category->id)
        ->set('form.type', 'permanent')
        ->set('form.method', 'daring')
        ->set('form.quota', 50)
        ->set('form.target_audience', 'Seluruh Staf OPD')
        ->set('form.budget_source', 'APBK Aceh Timur')
        ->set('form.description', 'Pelatihan penguasaan TNDE terintegrasi.')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('perencanaan.data'));

    $this->assertDatabaseHas('courses', [
        'code' => 'PLT-2026-009',
        'title' => 'Pelatihan Teknis Tata Naskah Dinas Elektronik',
        'category_id' => $category->id,
        'quota' => 50,
        'status' => 'draft',
    ]);
});

test('perencanaan edit mounts existing data and successfully updates course', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-2026-002',
        'title' => 'Pelatihan Fungsional Analis Kebijakan',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(PerencanaanEdit::class, ['id' => $course->id])
        ->assertSet('form.code', 'PLT-2026-002')
        ->assertSet('form.title', 'Pelatihan Fungsional Analis Kebijakan')
        ->set('form.title', 'Pelatihan Fungsional Analis Kebijakan Tk. Madya')
        ->set('form.quota', 45)
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('perencanaan.data'));

    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Pelatihan Fungsional Analis Kebijakan Tk. Madya',
        'quota' => 45,
        'status' => 'draft',
    ]);
});

test('perencanaan edit cannot edit or mount courses with non-draft status', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $publishedCourse = Course::create([
        'code' => 'PLT-2026-PUB',
        'title' => 'Pelatihan Sudah Dibuka',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => CourseStatus::Published,
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(PerencanaanEdit::class, ['id' => $publishedCourse->id])
        ->assertRedirect(route('perencanaan.data'));

    // Attempt direct repo update
    $updateResult = PerencanaanRepo::update($publishedCourse->id, [
        'title' => 'Coba Ubah Judul',
    ]);
    expect($updateResult)->toBeFalse();

    // Attempt direct repo delete
    $deleteResult = PerencanaanRepo::delete($publishedCourse->id);
    expect($deleteResult)->toBeFalse();
    expect($publishedCourse->fresh())->not->toBeNull();
});

test('perencanaan delete event deletes course from database and dispatches events', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-2026-099',
        'title' => 'Pelatihan Uji Coba Hapus',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 20,
        'status' => 'draft',
    ]);

    Livewire::actingAs($admin)
        ->test(PerencanaanData::class)
        ->call('hookModalDelete', $course->id, $course->title)
        ->assertDispatched('modal-delete-setDeleteId')
        ->dispatch('PerencanaanData-delete', ['id' => $course->id])
        ->assertDispatched('closeModal', id: 'modalDelete')
        ->assertDispatched('alert-show')
        ->assertDispatched('reloadDT', data: 'dtTable');

    $this->assertDatabaseMissing('courses', [
        'id' => $course->id,
    ]);
});

test('header user profile dropdown and logout form render on beranda, data, create, and edit pages', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-2026-077',
        'title' => 'Pelatihan Pengujian Dropdown',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 25,
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    $routes = [
        route('admin.dashboard'),
        route('perencanaan.data'),
        route('perencanaan.create'),
        route('perencanaan.edit', $course->id),
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

test('admin can submit a draft course to leader via edit page', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-2026-SUBMIT',
        'title' => 'Pelatihan Transformasi Digital ASN',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 30,
        'status' => 'draft',
        'created_by' => $admin->id,
    ]);

    Livewire::actingAs($admin)
        ->test(PerencanaanEdit::class, ['id' => $course->id])
        ->call('submitToLeader')
        ->assertHasNoErrors()
        ->assertRedirect(route('perencanaan.data'));

    expect($course->fresh()->status->value)->toBe('submitted');
});

test('admin can archive completed course in perencanaan', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::first();

    $course = Course::create([
        'code' => 'PLT-2026-FLOW',
        'title' => 'Pelatihan Manajemen Risiko SPBE',
        'category_id' => $category->id,
        'type' => 'permanent',
        'method' => 'daring',
        'quota' => 25,
        'status' => CourseStatus::Completed,
        'created_by' => $admin->id,
    ]);

    // Completed -> Archived (Diarsipkan)
    Livewire::actingAs($admin)
        ->test(PerencanaanData::class)
        ->call('archiveCourse', $course->id)
        ->assertDispatched('alert-show');
    expect($course->fresh()->status)->toBe(CourseStatus::Archived);
});
