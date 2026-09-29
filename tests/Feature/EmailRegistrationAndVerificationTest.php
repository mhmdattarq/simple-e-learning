<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Mail\VerifyEmailNotification;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(CategorySeeder::class);
    Mail::fake();
    RateLimiter::clear('resend-verify');
});

test('participant registration sends activation email, creates secure verification token, and logs audit', function () {
    Livewire::test(Register::class)
        ->set('form.name', 'Teuku Ryan, S.STP')
        ->set('form.email', 'teuku.ryan@acehtimurkab.go.id')
        ->set('form.phone_number', '081234567891')
        ->set('form.address', 'Jl. Merdeka No. 10, Idi Rayeuk')
        ->set('form.password', 'Rahasia123!')
        ->set('form.password_confirmation', 'Rahasia123!')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('login'))
        ->assertSessionHas('alert-show');

    $user = User::where('email', 'teuku.ryan@acehtimurkab.go.id')->first();
    expect($user)->not->toBeNull();
    expect($user->email_verified_at)->toBeNull();
    expect($user->isPeserta())->toBeTrue();

    // Verify token was stored in email_verifications as SHA-256 hash
    $verification = EmailVerification::where('user_id', $user->id)->first();
    expect($verification)->not->toBeNull();
    expect(strlen($verification->token_hash))->toBe(64);
    expect($verification->expires_at->isFuture())->toBeTrue();

    // Verify activation mail was sent
    Mail::assertSent(VerifyEmailNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email) && $mail->user->id === $user->id;
    });

    // Verify append-only audit log entry
    $audit = AuditLog::where('action', 'user.registered')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->notes)->toContain('Pendaftaran mandiri');
});

test('unverified user cannot login and is prompted to verify email with resend action', function () {
    $user = User::factory()->unverified()->peserta()->create([
        'email' => 'unverified@acehtimurkab.go.id',
        'password' => Hash::make('password123'),
    ]);

    $loginComponent = Livewire::test(Login::class)
        ->set('identifier', 'unverified@acehtimurkab.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertHasErrors(['identifier'])
        ->assertSee('Alamat email akun Anda belum diverifikasi');

    $this->assertGuest();

    // Reactive resend activation link via Livewire action
    $loginComponent->call('resendVerification')
        ->assertSee('Tautan aktivasi baru telah dikirimkan');

    Mail::assertSent(VerifyEmailNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('visiting livewire verify email page with valid token activates account and logs audit', function () {
    $user = User::factory()->unverified()->peserta()->create([
        'email' => 'verifyme@acehtimurkab.go.id',
    ]);

    $rawToken = EmailVerification::createTokenFor($user);

    // Livewire Full-Page Component execution
    Livewire::test(VerifyEmail::class, ['token' => $rawToken])
        ->assertSet('isVerified', true)
        ->assertSee('Aktivasi Email Berhasil!')
        ->assertSee('Masuk ke Portal Sekarang');

    $user->refresh();
    expect($user->email_verified_at)->not->toBeNull();

    // Token must be consumed
    expect(EmailVerification::where('user_id', $user->id)->count())->toBe(0);

    // Audit log recorded
    $audit = AuditLog::where('action', 'user.email_verified')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
});

test('invalid or expired verification token displays failed state and allows reactive resend', function () {
    $user = User::factory()->unverified()->peserta()->create([
        'email' => 'expired@acehtimurkab.go.id',
    ]);

    // Create an already-expired token
    $rawToken = 'sample-raw-token-12345';
    EmailVerification::create([
        'user_id' => $user->id,
        'token_hash' => hash('sha256', $rawToken),
        'expires_at' => now()->subMinute(),
    ]);

    // Livewire test on expired token
    $component = Livewire::test(VerifyEmail::class, ['token' => $rawToken])
        ->assertSet('isVerified', false)
        ->assertSee('Verifikasi Gagal')
        ->assertSee('Kirim Ulang Tautan Aktivasi');

    $user->refresh();
    expect($user->email_verified_at)->toBeNull();

    // Test reactive resend verification through the Livewire component
    $component->set('resendEmail', 'expired@acehtimurkab.go.id')
        ->call('resendVerification')
        ->assertHasNoErrors()
        ->assertSee('tautan verifikasi baru telah dikirimkan');

    Mail::assertSent(VerifyEmailNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

    // Verification token exists
    expect(EmailVerification::where('user_id', $user->id)->count())->toBe(1);

    // Resend audit log
    $audit = AuditLog::where('action', 'user.verification_resent')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
});

test('verified user can login successfully and triggers audit log', function () {
    $user = User::factory()->peserta()->create([
        'email' => 'aktif@acehtimurkab.go.id',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->set('identifier', 'aktif@acehtimurkab.go.id')
        ->set('password', 'password123')
        ->call('authenticate')
        ->assertRedirect(route('landing'));

    $this->assertAuthenticatedAs($user);

    // Audit log records login success
    $audit = AuditLog::where('action', 'user.login')
        ->where('auditable_id', $user->id)
        ->first();

    expect($audit)->not->toBeNull();
});

test('livewire resend verification applies anti-enumeration for non-existent emails', function () {
    Livewire::test(VerifyEmail::class, ['token' => 'invalid-token'])
        ->set('resendEmail', 'tidakada@acehtimurkab.go.id')
        ->call('resendVerification')
        ->assertHasNoErrors()
        ->assertSee('Jika alamat email terdaftar dan belum aktif');

    Mail::assertNothingSent();
});
