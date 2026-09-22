<?php

use App\Livewire\Admin\Dashboard\DashboardIndex;
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
    $response->assertSee('Pilihan Akademi Berstandar Nasional');
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
