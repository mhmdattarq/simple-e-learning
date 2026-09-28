<?php

use App\Enums\Role;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
});

test('google oauth redirect route redirects user to google accounts', function () {
    $response = $this->get(route('auth.google.redirect'));

    $response->assertRedirect();
    $targetUrl = $response->headers->get('Location');
    expect($targetUrl)->toContain('accounts.google.com');
});

test('login and register views render clickable google oauth buttons', function () {
    $loginResponse = $this->get(route('login'));
    $loginResponse->assertStatus(200);
    $loginResponse->assertSee(route('auth.google.redirect'));
    $loginResponse->assertSee('Masuk Dengan Google');

    $registerResponse = $this->get(route('register'));
    $registerResponse->assertStatus(200);
    $registerResponse->assertSee(route('auth.google.redirect'));
    $registerResponse->assertSee('Buat Akun Dengan Google');
});

test('new user logging in with google is automatically registered as peserta and redirected to landing', function () {
    $mockSocialiteUser = new SocialiteUser;
    $mockSocialiteUser->id = 'google-uid-1234567890';
    $mockSocialiteUser->name = 'Cut Putri Malahayati';
    $mockSocialiteUser->email = 'putri.malahayati@gmail.com';
    $longAvatarUrl = 'https://lh3.googleusercontent.com/a-/'.str_repeat('ALV-UjW9NqHJL14EN5TZg0E-Iw7Zo2mdXunwnvgcXh6XTzFtjrVuD2X4xz0P7g4LsmKe57woeLMxROqoUHjhopN', 10);
    $mockSocialiteUser->avatar = $longAvatarUrl;

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($mockSocialiteUser);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('landing'));
    $response->assertSessionHas('alert-show');

    // User is created in database
    $user = User::where('google_id', 'google-uid-1234567890')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Cut Putri Malahayati');
    expect($user->email)->toBe('putri.malahayati@gmail.com');
    expect($user->role)->toBe(Role::Peserta);
    expect($user->email_verified_at)->not->toBeNull();
    expect($user->nip)->toBeNull(); // NIP will be completed in Profile step
    expect($user->last_login_at)->not->toBeNull();

    // User is logged in
    $this->assertAuthenticatedAs($user);

    // Audit logs recorded
    $registeredAudit = AuditLog::where('action', 'user.registered_via_google')
        ->where('auditable_id', $user->id)
        ->first();
    expect($registeredAudit)->not->toBeNull();

    $loginAudit = AuditLog::where('action', 'user.login_google')
        ->where('auditable_id', $user->id)
        ->first();
    expect($loginAudit)->not->toBeNull();
});

test('existing user without google_id links their google account seamlessly on google login', function () {
    $existingUser = User::factory()->peserta()->create([
        'email' => 'asn.terdaftar@acehtimurkab.go.id',
        'google_id' => null,
        'avatar_url' => null,
    ]);

    $mockSocialiteUser = new SocialiteUser;
    $mockSocialiteUser->id = 'google-uid-9988776655';
    $mockSocialiteUser->name = 'ASN Terdaftar';
    $mockSocialiteUser->email = 'asn.terdaftar@acehtimurkab.go.id';
    $mockSocialiteUser->avatar = 'https://lh3.googleusercontent.com/avatar-linked.jpg';

    Socialite::shouldReceive('driver->user')
        ->once()
        ->andReturn($mockSocialiteUser);

    $response = $this->get(route('auth.google.callback'));

    $response->assertRedirect(route('landing'));
    $response->assertSessionHas('alert-show');

    $existingUser->refresh();
    expect($existingUser->google_id)->toBe('google-uid-9988776655');
    expect($existingUser->avatar_url)->toBe('https://lh3.googleusercontent.com/avatar-linked.jpg');

    $this->assertAuthenticatedAs($existingUser);

    // Audit log for linking
    $linkAudit = AuditLog::where('action', 'user.linked_google')
        ->where('auditable_id', $existingUser->id)
        ->first();
    expect($linkAudit)->not->toBeNull();
});

test('google oauth callback handles denial or exceptions gracefully without breaking', function () {
    // 1. Google OAuth denied or error parameter returned
    $response = $this->get(route('auth.google.callback', ['error' => 'access_denied']));
    $response->assertRedirect(route('login'));
    $response->assertSessionHas('error');
    $this->assertGuest();

    // 2. Exception during driver communication
    Socialite::shouldReceive('driver->user')
        ->once()
        ->andThrow(new Exception('Network connection timeout with Google API'));

    $response2 = $this->get(route('auth.google.callback'));
    $response2->assertRedirect(route('login'));
    $response2->assertSessionHas('error');
    $this->assertGuest();
});
