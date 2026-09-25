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
    $response->assertSee('Jadwal Pelatihan');
    $response->assertSee('Terdekat');
    $response->assertSee('Katalog Pelatihan Digital Terbuka');
    $response->assertSee('Alur Mudah Pendaftaran');
    $response->assertSee('Pelatihan Mandiri');
    $response->assertSee('Batch Berkala');
    $response->assertSee('Penugasan Khusus');
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

test('published courses appear in the catalog section on the landing page', function () {
    $category = Category::factory()->create(['name' => 'Transformasi Digital']);
    $admin = User::factory()->admin()->create();

    $published = Course::factory()->count(3)->create([
        'status' => 'published',
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'status' => 'draft',
        'category_id' => $category->id,
        'created_by' => $admin->id,
        'title' => 'Draft Yang Tidak Tampil',
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);

    foreach ($published as $course) {
        $response->assertSee($course->title);
    }

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

test('quick info jadwal section on landing page displays maximum 3 upcoming batch courses and links to jadwal page', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();

    // Buat 4 batch course
    Course::factory()->create([
        'title' => 'Batch 1 Terdekat',
        'type' => 'batch',
        'status' => 'published',
        'start_date' => now()->addDays(5)->format('Y-m-d'),
        'end_date' => now()->addDays(10)->format('Y-m-d'),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'title' => 'Batch 2 Terdekat',
        'type' => 'batch',
        'status' => 'published',
        'start_date' => now()->addDays(15)->format('Y-m-d'),
        'end_date' => now()->addDays(20)->format('Y-m-d'),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'title' => 'Batch 3 Terdekat',
        'type' => 'batch',
        'status' => 'published',
        'start_date' => now()->addDays(25)->format('Y-m-d'),
        'end_date' => now()->addDays(30)->format('Y-m-d'),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Course::factory()->create([
        'title' => 'Batch 4 Lebih Jauh',
        'type' => 'batch',
        'status' => 'published',
        'start_date' => now()->addDays(40)->format('Y-m-d'),
        'end_date' => now()->addDays(45)->format('Y-m-d'),
        'category_id' => $category->id,
        'created_by' => $admin->id,
    ]);

    Livewire::test(LandingIndex::class)
        ->assertViewHas('upcomingJadwals', function ($jadwals) {
            return $jadwals->count() === 3
                && $jadwals->contains('title', 'Batch 1 Terdekat')
                && $jadwals->contains('title', 'Batch 2 Terdekat')
                && $jadwals->contains('title', 'Batch 3 Terdekat')
                && ! $jadwals->contains('title', 'Batch 4 Lebih Jauh');
        });

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Batch 1 Terdekat');
    $response->assertSee('Batch 2 Terdekat');
    $response->assertSee('Batch 3 Terdekat');
    $response->assertSee(route('jadwal'));
    $response->assertSee('Lihat Semua Jadwal');
    $response->assertSee('Lihat Detail Penjadwalan');
});
