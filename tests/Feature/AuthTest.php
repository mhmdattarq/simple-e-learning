<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('login page can be accessed and assets are correctly routed', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('admin/assets/images/favicon.png');
    $response->assertSee('admin/assets/css/remixicon.css');
    $response->assertSee('admin/assets/css/lib/bootstrap.min.css');
    $response->assertSee('admin/assets/css/style.css');
    $response->assertSee('admin/assets/js/lib/jquery-3.7.1.min.js');
    $response->assertSee('admin/assets/js/app.js');
    $response->assertSee('SIMPEL');
    $response->assertSee('BKPSDM Aceh Timur');
    $response->assertSee('Masuk ke Akun');
});

test('admin can login using email and is redirected to admin dashboard', function () {
    $admin = User::factory()->admin()->create([
        'email' => 'admin@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'admin@simpel.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
    expect(auth()->user()->isAdmin())->toBeTrue();
});

test('peserta can login using 18 digit nip and is redirected to landing page', function () {
    $peserta = User::factory()->peserta()->create([
        'nip' => '199205052018011005',
        'email' => 'peserta@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', '199205052018011005')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('landing'));

    $this->assertAuthenticatedAs($peserta);
    expect(auth()->user()->isPeserta())->toBeTrue();
});

test('mentor and verifikator and pimpinan are redirected to admin dashboard', function () {
    $mentor = User::factory()->mentor()->create([
        'nip' => '198002022005011002',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', '198002022005011002')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($mentor);
    expect($mentor->hasAdminAccess())->toBeTrue();

    auth()->logout();

    $verifikator = User::factory()->verifikator()->create([
        'email' => 'verifikator@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'verifikator@simpel.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($verifikator);
    expect($verifikator->hasAdminAccess())->toBeTrue();

    auth()->logout();

    $pimpinan = User::factory()->pimpinan()->create([
        'email' => 'pimpinan@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'pimpinan@simpel.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($pimpinan);
    expect($pimpinan->hasAdminAccess())->toBeTrue();
});

test('login component validates required fields', function () {
    Livewire::test(Login::class)
        ->set('identifier', '')
        ->set('email', '')
        ->set('password', '')
        ->call('authenticate')
        ->assertHasErrors(['identifier', 'password'])
        ->assertSeeHtml('Email atau NIP wajib diisi.')
        ->assertDontSee('@elseerror');
});

test('login fails with invalid credentials', function () {
    User::factory()->create([
        'email' => 'user@simpel.go.id',
        'password' => bcrypt('correct_password'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'user@simpel.go.id')
        ->set('password', 'wrong_password')
        ->call('authenticate')
        ->assertHasErrors(['identifier'])
        ->assertSee('Email/NIP atau kata sandi yang Anda masukkan salah.');

    $this->assertGuest();
});

test('role middleware protects admin route from unauthorized roles', function () {
    // Guest redirected to login
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));

    // Peserta receives 403 Forbidden
    $peserta = User::factory()->peserta()->create();
    $this->actingAs($peserta)
        ->get(route('admin.dashboard'))
        ->assertStatus(403);

    // Admin allowed
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertStatus(200);
});

test('user can logout successfully via post request', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
