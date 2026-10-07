<?php

use App\Enums\Role;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('login page can be accessed and assets are correctly routed', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('admin/assets/css/remixicon.css');
    $response->assertSee('admin/assets/css/lib/bootstrap.min.css');
    $response->assertSee('admin/assets/css/style.css');
    $response->assertSee('admin/assets/js/lib/jquery-3.7.1.min.js');
    $response->assertSee('admin/assets/js/app.js');
    $response->assertSee('SIMPEL');
    $response->assertSee('BKPSDM Aceh Timur');
    $response->assertSee('Masuk ke Akun');
});

test('admin can login using email and is redirected to admin dashboard with welcome toast', function () {
    $admin = User::updateOrCreate(
        ['email' => 'admin@simpel.go.id'],
        [
            'name' => 'Admin Simpel',
            'password' => bcrypt('password123'),
            'role' => Role::Admin,
            'phone_number' => '081234567890',
            'email_verified_at' => now(),
        ]
    );

    Livewire::test(Login::class)
        ->set('identifier', 'admin@simpel.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang admin',
        ]);

    $this->assertAuthenticatedAs($admin);
    expect(auth()->user()->isAdmin())->toBeTrue();
});

test('peserta can login using email and is redirected to landing page with welcome toast', function () {
    $peserta = User::factory()->peserta()->create([
        'email' => 'peserta@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'peserta@simpel.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('landing'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang peserta',
        ]);

    $this->assertAuthenticatedAs($peserta);
    expect(auth()->user()->isPeserta())->toBeTrue();
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

test('peserta can login using NIP and is redirected to landing page', function () {
    $peserta = User::factory()->peserta()->create([
        'email' => 'asn@simpel.go.id',
        'nip' => '199405302020121007',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', '199405302020121007')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('landing'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang peserta',
        ]);

    $this->assertAuthenticatedAs($peserta);
});

test('login fails gracefully when entering unknown NIP without database error', function () {
    Livewire::test(Login::class)
        ->set('identifier', '199405302020121007')
        ->set('password', 'anypassword123')
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

test('register page can be accessed by guest and shows registration fields', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Pendaftaran Peserta');
    $response->assertSee('Nama Lengkap');
    $response->assertSee('Alamat Email');
    $response->assertSee('No. Handphone / WhatsApp');
    $response->assertSee('Alamat Lengkap');
    $response->assertSee('Daftar Akun Peserta');
});

test('guest user can register successfully as peserta with valid data', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'Fauzan Akbar')
        ->set('form.email', 'fauzan@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567890')
        ->set('form.address', 'Jl. Medan - B. Aceh No. 12, Idi Rayeuk')
        ->set('form.password', 'rahasia123')
        ->set('form.password_confirmation', 'rahasia123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'name' => 'Fauzan Akbar',
        'email' => 'fauzan@acehtimurkab.go.id',
        'role' => Role::Peserta->value,
        'phone_number' => '081234567890',
        'address' => 'Jl. Medan - B. Aceh No. 12, Idi Rayeuk',
    ]);

    $createdUser = User::where('email', 'fauzan@acehtimurkab.go.id')->first();
    expect($createdUser->isPeserta())->toBeTrue();
    expect(Hash::check('rahasia123', $createdUser->password))->toBeTrue();
});

test('guest user can register with nip and nip is stored correctly', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'PNS Aceh Timur')
        ->set('form.nip', '199001012020121001')
        ->set('form.email', 'pns@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567891')
        ->set('form.address', 'Idi Rayeuk')
        ->set('form.password', 'rahasia123')
        ->set('form.password_confirmation', 'rahasia123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'email' => 'pns@acehtimurkab.go.id',
        'nip' => '199001012020121001',
    ]);
});

test('registration validates nip format if provided', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'PNS Aceh Timur')
        ->set('form.nip', '123456') // Not 18 digits
        ->set('form.email', 'pns@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567891')
        ->set('form.address', 'Idi Rayeuk')
        ->set('form.password', 'rahasia123')
        ->set('form.password_confirmation', 'rahasia123')
        ->call('register')
        ->assertHasErrors(['form.nip'])
        ->assertSee('NIP harus berjumlah 18 digit angka.');
});

test('registration validates required fields', function () {
    Livewire::test(Register::class)
        ->set('form.name', '')
        ->set('form.email', 'bukan-email')
        ->set('form.phone_number', '')
        ->set('form.address', '')
        ->set('form.password', '123') // Less than 6 chars
        ->set('form.password_confirmation', '456') // Mismatched
        ->call('register')
        ->assertHasErrors([
            'form.name',
            'form.email',
            'form.phone_number',
            'form.address',
            'form.password',
        ]);
});

test('registration fails when email already exists in database', function () {
    User::factory()->create([
        'email' => 'existing@simpel.go.id',
    ]);

    Livewire::test(Register::class)
        ->set('form.name', 'Peserta Baru')
        ->set('form.email', 'existing@simpel.go.id') // Duplicate Email
        ->set('form.phone_number', '081299998888')
        ->set('form.address', 'Idi Rayeuk')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['form.email']);
});

test('authenticated user is redirected away from register page by guest middleware', function () {
    $peserta = User::factory()->peserta()->create();

    $this->actingAs($peserta)
        ->get(route('register'))
        ->assertRedirect(route('admin.dashboard'));
});

test('logout modal is present in modal component with confirmation message and action buttons', function () {
    Livewire::test('admin.modal')
        ->assertSee('id="modalLogout"', false)
        ->assertSee('Konfirmasi Keluar')
        ->assertSee('Apakah Anda yakin ingin keluar?')
        ->assertSee('Batal')
        ->assertSee('Ya, Keluar')
        ->assertSee(route('logout'));
});

test('admin header renders logout button that triggers modalLogout', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test('admin.header')
        ->assertSee('data-bs-target="#modalLogout"', false)
        ->assertSee('Keluar');
});

test('landing navbar renders logout button that triggers modalLogout for authenticated peserta', function () {
    $peserta = User::factory()->peserta()->create();

    $this->actingAs($peserta);

    Livewire::test('landing.navbar')
        ->assertSee('data-bs-target="#modalLogout"', false)
        ->assertSee('Keluar');
});

test('authenticated user can logout via post route and session is invalidated', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);
    $this->assertAuthenticated();

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
