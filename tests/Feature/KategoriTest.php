<?php

use App\Livewire\Admin\Kategori\KategoriCreate;
use App\Livewire\Admin\Kategori\KategoriData;
use App\Livewire\Admin\Kategori\KategoriEdit;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    $this->admin = User::factory()->admin()->create();
    $this->peserta = User::factory()->peserta()->create();
});

test('unauthorized users cannot access kategori routes', function () {
    // Guest redirected to login
    $this->get(route('kategori.data'))->assertRedirect(route('login'));
    $this->get(route('kategori.create'))->assertRedirect(route('login'));

    $category = Category::first();
    $this->get(route('kategori.edit', $category->id))->assertRedirect(route('login'));

    // Peserta gets 403 Forbidden
    $this->actingAs($this->peserta)->get(route('kategori.data'))->assertStatus(403);
    $this->actingAs($this->peserta)->get(route('kategori.create'))->assertStatus(403);
    $this->actingAs($this->peserta)->get(route('kategori.edit', $category->id))->assertStatus(403);
});

test('admin can access kategori index page and see datatable element', function () {
    $response = $this->actingAs($this->admin)->get(route('kategori.data'));

    $response->assertStatus(200);
    $response->assertSee('Daftar Kategori Kelas');
    $response->assertSee('tableKategori');
    $response->assertSee(route('kategori.create'));

    Livewire::actingAs($this->admin)
        ->test(KategoriData::class)
        ->assertOk()
        ->assertSee('Daftar Kategori Kelas');
});

test('kategori datatable endpoint returns valid yajra json response with courses count', function () {
    $category = Category::first();

    // Create a course linked to this category
    Course::create([
        'slug' => 'test-kat-01',
        'title' => 'Pelatihan Manajemen Modern',
        'category_id' => $category->id,
        'type' => 'permanent',
        'status' => 'draft',
        'created_by' => $this->admin->id,
    ]);

    $response = $this->actingAs($this->admin)
        ->getJson(route('kategori.dt'));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data',
    ]);

    $data = collect($response->json('data'));
    $found = $data->firstWhere('id', $category->id);

    expect($found)->not->toBeNull();
    expect($found['name'])->toBe($category->name);
    expect($found['courses_count'])->toBe(1);
});

test('admin can access kategori create page and submit valid data', function () {
    $response = $this->actingAs($this->admin)->get(route('kategori.create'));
    $response->assertStatus(200);
    $response->assertSee('Tambah Kategori Baru');

    Livewire::actingAs($this->admin)
        ->test(KategoriCreate::class)
        ->set('form.name', 'Pelatihan Sosial Kultural')
        ->set('form.description', 'Pengembangan wawasan kebangsaan dan perekat persatuan ASN.')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('kategori.data'));

    $created = Category::where('name', 'Pelatihan Sosial Kultural')->first();
    expect($created)->not->toBeNull();
    expect($created->slug)->toBe('pelatihan-sosial-kultural');
    expect($created->description)->toBe('Pengembangan wawasan kebangsaan dan perekat persatuan ASN.');
});

test('kategori create validates required, min length, and unique name', function () {
    $existing = Category::first();

    Livewire::actingAs($this->admin)
        ->test(KategoriCreate::class)
        ->set('form.name', '')
        ->call('formSubmit')
        ->assertHasErrors(['form.name' => 'required']);

    Livewire::actingAs($this->admin)
        ->test(KategoriCreate::class)
        ->set('form.name', 'AB')
        ->call('formSubmit')
        ->assertHasErrors(['form.name' => 'min']);

    Livewire::actingAs($this->admin)
        ->test(KategoriCreate::class)
        ->set('form.name', $existing->name)
        ->call('formSubmit')
        ->assertHasErrors(['form.name' => 'unique']);
});

test('admin can access kategori edit page and update data successfully', function () {
    $category = Category::create([
        'name' => 'Kategori Lama',
        'slug' => 'kategori-lama',
        'description' => 'Deskripsi lama',
    ]);

    $response = $this->actingAs($this->admin)->get(route('kategori.edit', $category->id));
    $response->assertStatus(200);
    $response->assertSee('Edit Kategori Kelas');

    Livewire::actingAs($this->admin)
        ->test(KategoriEdit::class, ['id' => $category->id])
        ->assertSet('form.name', 'Kategori Lama')
        ->set('form.name', 'Kategori Baru Diperbarui')
        ->set('form.description', 'Deskripsi baru yang diperbarui')
        ->call('formSubmit')
        ->assertHasNoErrors()
        ->assertRedirect(route('kategori.data'));

    $updated = $category->fresh();
    expect($updated->name)->toBe('Kategori Baru Diperbarui');
    expect($updated->slug)->toBe('kategori-baru-diperbarui');
    expect($updated->description)->toBe('Deskripsi baru yang diperbarui');
});

test('admin can delete category with 0 courses, but cannot delete category with existing courses', function () {
    $categoryEmpty = Category::create([
        'name' => 'Kategori Kosong',
        'slug' => 'kategori-kosong',
    ]);

    $categoryHasCourse = Category::create([
        'name' => 'Kategori Berisi Kelas',
        'slug' => 'kategori-berisi-kelas',
    ]);

    Course::create([
        'slug' => 'plt-kat-test',
        'title' => 'Kelas Uji Coba Kategori',
        'category_id' => $categoryHasCourse->id,
        'type' => 'permanent',
        'status' => 'draft',
        'created_by' => $this->admin->id,
    ]);

    // 1. Delete empty category succeeds
    Livewire::actingAs($this->admin)
        ->test(KategoriData::class)
        ->call('delete', ['id' => $categoryEmpty->id])
        ->assertDispatched('alert-show', fn ($event, $params) => $params['data']['type'] === 'success')
        ->assertDispatched('reloadDT');

    expect(Category::find($categoryEmpty->id))->toBeNull();

    // 2. Delete category with courses fails safely
    Livewire::actingAs($this->admin)
        ->test(KategoriData::class)
        ->call('delete', ['id' => $categoryHasCourse->id])
        ->assertDispatched('alert-show', fn ($event, $params) => $params['data']['type'] === 'danger');

    expect(Category::find($categoryHasCourse->id))->not->toBeNull();
});
