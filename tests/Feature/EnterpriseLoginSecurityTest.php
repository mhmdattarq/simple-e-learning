<?php

use App\Livewire\Auth\Login;
use App\Models\AuditLog;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    RateLimiter::clear('login-ip|127.0.0.1');
});

test('successful login records last login metadata and audit log', function () {
    $user = User::factory()->peserta()->create([
        'email' => 'login.meta@acehtimurkab.go.id',
        'password' => Hash::make('secret123'),
        'email_verified_at' => now(),
    ]);

    expect($user->last_login_at)->toBeNull();
    expect($user->last_login_ip)->toBeNull();

    Livewire::test(Login::class)
        ->set('identifier', 'login.meta@acehtimurkab.go.id')
        ->set('password', 'secret123')
        ->call('authenticate')
        ->assertRedirect(route('landing'));

    $user->refresh();
    expect($user->last_login_at)->not->toBeNull();
    expect($user->last_login_ip)->not->toBeNull();

    // Verify audit log has ip and user details
    $audit = AuditLog::where('action', 'user.login')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->new_values)->toHaveKey('ip');
    expect($audit->new_values)->toHaveKey('role');
});

test('failed login attempts record user.login_failed audit logs', function () {
    $user = User::factory()->peserta()->create([
        'email' => 'fail.test@acehtimurkab.go.id',
        'password' => Hash::make('correctpass'),
        'email_verified_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'fail.test@acehtimurkab.go.id')
        ->set('password', 'wrongpass')
        ->call('authenticate')
        ->assertHasErrors(['identifier'])
        ->assertSee('Email/NIP atau kata sandi yang Anda masukkan salah.');

    $audit = AuditLog::where('action', 'user.login_failed')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->new_values['identifier'])->toBe('fail.test@acehtimurkab.go.id');
});

test('non-existent account produces identical generic error message without leaking existence', function () {
    Livewire::test(Login::class)
        ->set('identifier', 'tidak.terdaftar@acehtimurkab.go.id')
        ->set('password', 'bebas12345')
        ->call('authenticate')
        ->assertHasErrors(['identifier'])
        ->assertSee('Email/NIP atau kata sandi yang Anda masukkan salah.');

    $audit = AuditLog::where('action', 'user.login_failed')->first();
    expect($audit)->not->toBeNull();
    expect($audit->auditable_id)->toBeNull();
});

test('lockout triggers after 5 failed attempts and records audit log', function () {
    $user = User::factory()->peserta()->create([
        'email' => 'lockout@acehtimurkab.go.id',
        'password' => Hash::make('correctpass'),
        'email_verified_at' => now(),
    ]);

    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Login::class)
            ->set('identifier', 'lockout@acehtimurkab.go.id')
            ->set('password', 'wrong-pass-'.$i)
            ->call('authenticate');
    }

    // 6th attempt is locked out
    $component = Livewire::test(Login::class)
        ->set('identifier', 'lockout@acehtimurkab.go.id')
        ->set('password', 'correctpass')
        ->call('authenticate')
        ->assertSee('Terlalu banyak percobaan masuk');

    expect($component->get('lockoutSeconds'))->toBeGreaterThan(0);

    // Audit log records lockout
    $lockoutAudit = AuditLog::where('action', 'user.login_lockout')->first();
    expect($lockoutAudit)->not->toBeNull();
});
