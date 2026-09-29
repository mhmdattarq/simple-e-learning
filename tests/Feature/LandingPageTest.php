<?php

use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Landing\LandingIndex;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('landing page can be accessed successfully and displays core sections', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('landing/assets/css/bootstrap.min.css');
    $response->assertSee('landing/assets/js/script.js');
    $response->assertSee('Akselerasi Kompetensi');
    $response->assertSee('Daftar Jenis');
    $response->assertSee('Kelas');
    $response->assertSee('Katalog');
    $response->assertSee('Alur Mudah Pendaftaran');
    $response->assertSee('Kelas Batch');
    $response->assertSee('Kelas Permanen');
    $response->assertSee('Kelas Berbayar');
    $response->assertSee('SIMPEL');
    $response->assertSee('BKPSDM Aceh Timur');
    $response->assertSee('Menu Utama');
    $response->assertDontSee('E-Sertifikat Digital & Integrasi SIASN BKN');
    $response->assertDontSee('(0646) 7000-111');
});

test('admin dashboard can be accessed on admin.dashboard by authenticated admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get(route('admin.dashboard'));
    $response->assertStatus(200);

    Livewire::actingAs($admin)
        ->test(DashboardIndex::class)
        ->assertOk()
        ->assertSee('Beranda Administrator');
});

test('guest sees masuk and daftar buttons on landing navbar', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('class="thm-btn">Masuk</a>', false);
    $response->assertSee('class="thm-btn">Daftar</a>', false);
    $response->assertDontSee('Keluar');
    $response->assertDontSee('Profil Saya');

    Livewire::test('landing.navbar')
        ->assertSee('Masuk')
        ->assertSee('Daftar')
        ->assertDontSee('Keluar');
});

test('authenticated peserta sees user profile dropdown and does not see masuk and daftar buttons', function () {
    $peserta = User::factory()->peserta()->create([
        'name' => 'Cut Nyak Dien',
        'nip' => '199501012020012001',
    ]);

    $response = $this->actingAs($peserta)->get('/');

    $response->assertStatus(200);
    $response->assertSee('Cut Nyak Dien');
    $response->assertSee('Peserta');
    $response->assertSee('199501012020012001');
    $response->assertSee('Profil Saya');
    $response->assertSee('Keluar');
    $response->assertDontSee('class="thm-btn">Masuk</a>', false);
    $response->assertDontSee('class="thm-btn">Daftar</a>', false);

    Livewire::actingAs($peserta)
        ->test('landing.navbar')
        ->assertSee('Cut Nyak Dien')
        ->assertSee('Peserta')
        ->assertSee('199501012020012001')
        ->assertSee('Keluar')
        ->assertDontSee('class="thm-btn">Masuk</a>', false);
});

test('authenticated user can logout from landing navbar and is redirected to landing', function () {
    $peserta = User::factory()->peserta()->create();

    $this->actingAs($peserta);
    $this->assertAuthenticatedAs($peserta);

    Livewire::actingAs($peserta)
        ->test('landing.navbar')
        ->call('logout')
        ->assertRedirect(route('landing'));

    $this->assertGuest();

    // Subsequent visit to landing shows guest buttons again
    $this->get('/')
        ->assertStatus(200)
        ->assertSee('class="thm-btn">Masuk</a>', false)
        ->assertSee('class="thm-btn">Daftar</a>', false);
});

test('published courses appear in the catalog section on the landing page limited to 6 items', function () {
    $category = Category::factory()->create(['name' => 'Transformasi Digital']);
    $admin = User::factory()->admin()->create();

    // Kursus ke-7 yang dibuat lebih awal (ID lebih kecil)
    $courseOld = Course::factory()->create([
        'status' => 'published',
        'type' => 'permanent',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'title' => 'Kursus Ketujuh Tidak Tampil Di Landing',
    ]);

    for ($i = 1; $i <= 6; $i++) {
        Course::factory()->create([
            'status' => 'published',
            'type' => 'permanent',
            'category_id' => $category->id,
            'created_by' => $admin->id,
            'title' => 'Kursus Unggulan '.$i,
        ]);
    }

    Course::factory()->create([
        'status' => 'draft',
        'type' => 'permanent',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'title' => 'Draft Yang Tidak Tampil',
    ]);

    Livewire::test(LandingIndex::class)
        ->assertViewHas('courses', function ($courses) {
            return $courses->count() === 6
                && $courses->contains('title', 'Kursus Unggulan 1')
                && $courses->contains('title', 'Kursus Unggulan 6')
                && ! $courses->contains('title', 'Kursus Ketujuh Tidak Tampil Di Landing');
        });

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Kursus Unggulan 1');
    $response->assertSee('Kursus Unggulan 6');
    $response->assertDontSee('Kursus Ketujuh Tidak Tampil Di Landing');
    $response->assertDontSee('Draft Yang Tidak Tampil');
});

test('hero section shows dynamic published course count from database', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Course::factory()->count(5)->create([
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    // 5 published courses → shows "5+" in hero stat
    $response->assertSee('5+');
});

test('landing page shows fallback placeholder when no published courses exist', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // No published courses → shows static fallback "—" for count
    $response->assertSee('—');
});

test('daftar jenis kelas section on landing page displays 3 course types with dynamic counts and links', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    Course::factory()->create([
        'title' => 'Batch 1 Terdekat',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'title' => 'Permanen 1',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'title' => 'Berbayar 1',
        'type' => 'paid',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::test(LandingIndex::class)
        ->assertViewHas('batchCoursesCount', 1)
        ->assertViewHas('permanentCoursesCount', 1)
        ->assertViewHas('paidCoursesCount', 1);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Daftar Jenis');
    $response->assertSee('Kelas Batch');
    $response->assertSee('Kelas Permanen');
    $response->assertSee('Kelas Berbayar');
    $response->assertSee(route('landing.kelas.batch'));
    $response->assertSee(route('landing.kelas.permanen'));
    $response->assertSee(route('landing.kelas.berbayar'));
});

test('landing page catalog allows filtering active courses by type', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $batch = Course::factory()->create([
        'title' => 'Batch Khusus Filter',
        'type' => 'batch',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    $permanent = Course::factory()->create([
        'title' => 'Permanen Khusus Filter',
        'type' => 'permanent',
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::test(LandingIndex::class)
        ->assertSet('selectedType', 'all')
        ->assertViewHas('courses', function ($courses) {
            return $courses->count() === 2;
        })
        ->call('filterType', 'batch')
        ->assertSet('selectedType', 'batch')
        ->assertViewHas('courses', function ($courses) use ($batch) {
            return $courses->count() === 1 && $courses->contains('id', $batch->id);
        })
        ->call('filterType', 'permanent')
        ->assertSet('selectedType', 'permanent')
        ->assertViewHas('courses', function ($courses) use ($permanent) {
            return $courses->count() === 1 && $courses->contains('id', $permanent->id);
        })
        ->call('filterType', 'paid')
        ->assertSet('selectedType', 'paid')
        ->assertViewHas('courses', function ($courses) {
            return $courses->isEmpty();
        })
        ->call('filterType', 'all')
        ->assertSet('selectedType', 'all')
        ->assertViewHas('courses', function ($courses) {
            return $courses->count() === 2;
        });
});
