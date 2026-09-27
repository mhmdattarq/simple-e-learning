<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    Mail::fake();
});

test('peserta manual login flashes reusable toast alert-show and renders on landing page', function () {
    $peserta = User::factory()->peserta()->create([
        'name' => 'Fahmi Hidayat, S.IP',
        'email' => 'fahmi.hidayat@acehtimurkab.go.id',
        'password' => Hash::make('secretPassword123'),
        'email_verified_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'fahmi.hidayat@acehtimurkab.go.id')
        ->set('password', 'secretPassword123')
        ->call('authenticate')
        ->assertRedirect(route('landing'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => 'berhasil login selamat datang peserta',
        ]);

    // Test that the landing page renders the livewire admin.toast component
    $response = $this->actingAs($peserta)
        ->withSession([
            'alert-show' => [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'berhasil login selamat datang peserta',
            ],
        ])
        ->get(route('landing'));

    $response->assertOk();
    $response->assertSee('berhasil login selamat datang peserta');
});

test('peserta manual registration flashes reusable toast alert-show and renders on login page', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'Zulkifli, S.Kom')
        ->set('form.nip', '199506102020011002')
        ->set('form.email', 'zulkifli@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567800')
        ->set('form.opd_agency', 'Dinas Komunikasi dan Informatika')
        ->set('form.position', 'Pranata Komputer Ahli Pertama')
        ->set('form.rank_class', 'Penata Muda / III.a')
        ->set('form.password', 'Password123!')
        ->set('form.password_confirmation', 'Password123!')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'))
        ->assertSessionHas('alert-show', [
            'type' => 'success',
            'title' => 'Pendaftaran Berhasil',
            'message' => 'Pendaftaran akun berhasil! Tautan aktivasi telah dikirimkan ke email zulkifli@acehtimurkab.go.id.',
        ]);

    // Test that login page renders the livewire admin.toast component with the flash message
    $response = $this->withSession([
        'alert-show' => [
            'type' => 'success',
            'title' => 'Pendaftaran Berhasil',
            'message' => 'Pendaftaran akun berhasil! Tautan aktivasi telah dikirimkan ke email zulkifli@acehtimurkab.go.id.',
        ],
    ])->get(route('login'));

    $response->assertOk();
    $response->assertSee('Pendaftaran akun berhasil!');
});

test('peserta google login flashes reusable toast alert-show and renders on landing page', function () {
    $existingUser = User::factory()->peserta()->create([
        'name' => 'Syahrul Ramadhan, S.STP',
        'email' => 'syahrul.ramadhan@gmail.com',
        'google_id' => 'google-user-777',
    ]);

    $mockSocialite = new SocialiteUser;
    $mockSocialite->id = 'google-user-777';
    $mockSocialite->name = 'Syahrul Ramadhan, S.STP';
    $mockSocialite->email = 'syahrul.ramadhan@gmail.com';
    $mockSocialite->avatar = 'https://lh3.googleusercontent.com/avatar.jpg';

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($mockSocialite);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('landing'));
    $response->assertSessionHas('alert-show', [
        'type' => 'success',
        'title' => 'Berhasil',
        'message' => 'berhasil login selamat datang peserta',
    ]);

    // Render landing page
    $landingResponse = $this->actingAs($existingUser)
        ->withSession([
            'alert-show' => [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'berhasil login selamat datang peserta',
            ],
        ])
        ->get(route('landing'));

    $landingResponse->assertOk();
    $landingResponse->assertSee('berhasil login selamat datang peserta');
});

test('new user registering via google flashes reusable toast alert-show and renders on landing page', function () {
    $mockSocialite = new SocialiteUser;
    $mockSocialite->id = 'google-new-user-888';
    $mockSocialite->name = 'Cut Annisa, S.Pd';
    $mockSocialite->email = 'annisa.cut@gmail.com';
    $mockSocialite->avatar = 'https://lh3.googleusercontent.com/avatar-new.jpg';

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($mockSocialite);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('landing'));
    $response->assertSessionHas('alert-show', [
        'type' => 'success',
        'title' => 'Pendaftaran Berhasil',
        'message' => 'Pendaftaran akun melalui Google berhasil! Selamat datang di SIMPEL BKPSDM, Cut Annisa, S.Pd.',
    ]);

    $createdUser = User::where('google_id', 'google-new-user-888')->first();
    expect($createdUser)->not->toBeNull();

    // Render landing page
    $landingResponse = $this->actingAs($createdUser)
        ->withSession([
            'alert-show' => [
                'type' => 'success',
                'title' => 'Pendaftaran Berhasil',
                'message' => 'Pendaftaran akun melalui Google berhasil! Selamat datang di SIMPEL BKPSDM, Cut Annisa, S.Pd.',
            ],
        ])
        ->get(route('landing'));

    $landingResponse->assertOk();
    $landingResponse->assertSee('Pendaftaran akun melalui Google berhasil!');
});
