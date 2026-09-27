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
    $admin = User::factory()->admin()->create([
        'email' => 'admin@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

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

test('peserta can login using 18 digit nip and is redirected to landing page with welcome toast', function () {
    $peserta = User::factory()->peserta()->create([
        'nip' => '199205052018011005',
        'email' => 'peserta@simpel.go.id',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', '199205052018011005')
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

test('mentor and verifikator and pimpinan are redirected to admin dashboard', function () {
    $mentor = User::factory()->mentor()->create([
        'nip' => '198002022005011002',
        'password' => bcrypt('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', '198002022005011002')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang mentor',
        ]);

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
        ->assertRedirect(route('admin.dashboard'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang verifikator',
        ]);

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
        ->assertRedirect(route('pimpinan.persetujuan.data'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang pimpinan',
        ]);

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

test('register page can be accessed by guest and shows registration fields', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
    $response->assertSee('Pendaftaran Peserta / Siswa');
    $response->assertSee('Identitas Kepegawaian');
    $response->assertSee('NIP (18 Digit)');
    $response->assertSee('Nama Lengkap');
    $response->assertSee('Instansi / OPD Asal');
    $response->assertSee('Jabatan Saat Ini');
    $response->assertSee('Pangkat / Golongan');
    $response->assertSee('Kontak');
    $response->assertSee('Daftar Akun Peserta');
});

test('guest user can register successfully as peserta with valid data', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'Fauzan Akbar, S.STP')
        ->set('form.nip', '199508172020121002')
        ->set('form.email', 'fauzan@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567890')
        ->set('form.opd_agency', 'Badan Kepegawaian dan Pengembangan SDM')
        ->set('form.position', 'Pranata Komputer Ahli Pertama')
        ->set('form.rank_class', 'Penata Muda - III/a')
        ->set('form.password', 'rahasia123')
        ->set('form.password_confirmation', 'rahasia123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'name' => 'Fauzan Akbar, S.STP',
        'nip' => '199508172020121002',
        'email' => 'fauzan@acehtimurkab.go.id',
        'role' => Role::Peserta->value,
        'phone_number' => '081234567890',
        'opd_agency' => 'Badan Kepegawaian dan Pengembangan SDM',
        'position' => 'Pranata Komputer Ahli Pertama',
        'rank_class' => 'Penata Muda - III/a',
    ]);

    $createdUser = User::where('nip', '199508172020121002')->first();
    expect($createdUser->isPeserta())->toBeTrue();
    expect(Hash::check('rahasia123', $createdUser->password))->toBeTrue();
});

test('registration validates required fields and 18 digits numeric nip', function () {
    Livewire::test(Register::class)
        ->set('form.name', '')
        ->set('form.nip', '12345') // Less than 18 digits
        ->set('form.email', 'bukan-email')
        ->set('form.phone_number', '')
        ->set('form.opd_agency', '')
        ->set('form.position', '')
        ->set('form.rank_class', '')
        ->set('form.password', '123') // Less than 6 chars
        ->set('form.password_confirmation', '456') // Mismatched
        ->call('register')
        ->assertHasErrors([
            'form.name',
            'form.nip',
            'form.email',
            'form.phone_number',
            'form.opd_agency',
            'form.position',
            'form.rank_class',
            'form.password',
        ]);
});

test('registration fails when nip or email already exists in database', function () {
    User::factory()->create([
        'nip' => '199001012015011001',
        'email' => 'existing@simpel.go.id',
    ]);

    Livewire::test(Register::class)
        ->set('form.name', 'Peserta Baru')
        ->set('form.nip', '199001012015011001') // Duplicate NIP
        ->set('form.email', 'existing@simpel.go.id') // Duplicate Email
        ->set('form.phone_number', '081299998888')
        ->set('form.opd_agency', 'Dinas Pendidikan')
        ->set('form.position', 'Guru Ahli Pertama')
        ->set('form.rank_class', 'Penata Muda - III/a')
        ->set('form.password', 'password123')
        ->set('form.password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['form.nip', 'form.email']);
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
