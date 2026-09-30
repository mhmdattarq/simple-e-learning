<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Mail\ResetPasswordNotification;
use App\Models\AuditLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('forgot password page can be rendered for guest', function () {
    $response = $this->get(route('password.request'));

    $response->assertStatus(200);
    $response->assertSee('Lupa Kata Sandi?');
    $response->assertSee('Kirim Tautan Pemulihan');
    $response->assertSee('Kembali ke Halaman Masuk');
});

test('authenticated user cannot access forgot password or reset password pages', function () {
    $user = User::factory()->peserta()->create();

    $this->actingAs($user)
        ->get(route('password.request'))
        ->assertRedirect(route('admin.dashboard'));

    $this->actingAs($user)
        ->get(route('password.reset', ['token' => 'dummy-token']))
        ->assertRedirect(route('admin.dashboard'));
});

test('submitting valid email generates hashed token, queues email, and writes audit log', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'asn.teladan@acehtimurkab.go.id',
    ]);

    Livewire::test(ForgotPassword::class)
        ->set('email', 'asn.teladan@acehtimurkab.go.id')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSee('Jika alamat email Anda terdaftar dalam sistem');

    // 1. Asynchronous Email Queued
    Mail::assertQueued(ResetPasswordNotification::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

    // 2. Token hashed with SHA-256 in password_reset_tokens
    $tokenRecord = DB::table('password_reset_tokens')->where('email', $user->email)->first();
    expect($tokenRecord)->not->toBeNull();
    expect(strlen($tokenRecord->token))->toBe(64); // SHA-256 hex string

    // 3. Immutable AuditLog created
    $auditLog = AuditLog::where('action', 'user.password_reset_requested')
        ->where('user_id', $user->id)
        ->first();
    expect($auditLog)->not->toBeNull();
});

test('anti-user enumeration: unknown email shows success message without sending email or exposing database state', function () {
    Mail::fake();

    Livewire::test(ForgotPassword::class)
        ->set('email', 'tidak.terdaftar@bkpsdm.go.id')
        ->call('sendResetLink')
        ->assertHasNoErrors()
        ->assertSee('Jika alamat email Anda terdaftar dalam sistem');

    Mail::assertNothingQueued();
    $tokenRecord = DB::table('password_reset_tokens')->where('email', 'tidak.terdaftar@bkpsdm.go.id')->first();
    expect($tokenRecord)->toBeNull();
});

test('forgot password route is protected by rate limiting', function () {
    // 3 requests allowed per minute
    for ($i = 0; $i < 3; $i++) {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
    }

    // 4th request must be throttled (429)
    $response = $this->get(route('password.request'));
    $response->assertStatus(429);
});

test('reset password view shows error when token is invalid or missing', function () {
    $response = $this->get(route('password.reset', [
        'token' => 'invalid-token-code',
        'email' => 'user@example.com',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Tautan Tidak Valid');
    $response->assertSee('Minta Tautan Baru');
});

test('reset password view shows expired state when token exceeds 60 minutes lifetime', function () {
    $user = User::factory()->create(['email' => 'kadaluwarsa@simpel.go.id']);
    $rawToken = Str::random(64);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => hash('sha256', $rawToken),
        'created_at' => Carbon::now()->subMinutes(61), // Exceeded 60 mins
    ]);

    $response = $this->get(route('password.reset', [
        'token' => $rawToken,
        'email' => $user->email,
    ]));

    $response->assertStatus(200);
    $response->assertSee('Tautan Tidak Valid');
    $response->assertSee('kedaluwarsa');
});

test('user can reset password with valid token and confirmation', function () {
    $user = User::factory()->create([
        'email' => 'pegawai.negeri@acehtimurkab.go.id',
        'password' => Hash::make('passwordLama123'),
    ]);

    $rawToken = Str::random(64);
    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => hash('sha256', $rawToken),
        'created_at' => now(),
    ]);

    // Simulate active session in other device
    DB::table('sessions')->insert([
        'id' => 'session-device-2',
        'user_id' => $user->id,
        'ip_address' => '192.168.1.1',
        'user_agent' => 'Mozilla/5.0',
        'payload' => 'dummy',
        'last_activity' => time(),
    ]);

    // Validation error when password confirmation mismatch
    Livewire::withQueryParams(['email' => $user->email])
        ->test(ResetPassword::class, ['token' => $rawToken])
        ->set('password', 'SandiBaru2026!')
        ->set('password_confirmation', 'BedaSandi!')
        ->call('resetPassword')
        ->assertHasErrors(['password']);

    // Successful password reset
    Livewire::withQueryParams(['email' => $user->email])
        ->test(ResetPassword::class, ['token' => $rawToken])
        ->set('password', 'SandiBaru2026!')
        ->set('password_confirmation', 'SandiBaru2026!')
        ->call('resetPassword')
        ->assertRedirect(route('login'))
        ->assertSessionHas('success');

    // 1. Password successfully changed
    $user->refresh();
    expect(Hash::check('SandiBaru2026!', $user->password))->toBeTrue();

    // 2. Token destroyed (Single-Use Rule)
    $remainingToken = DB::table('password_reset_tokens')->where('email', $user->email)->first();
    expect($remainingToken)->toBeNull();

    // 3. Other sessions invalidated
    $remainingSessions = DB::table('sessions')->where('user_id', $user->id)->count();
    expect($remainingSessions)->toBe(0);

    // 4. AuditLog records password reset success
    $auditLog = AuditLog::where('action', 'user.password_reset_success')
        ->where('user_id', $user->id)
        ->first();
    expect($auditLog)->not->toBeNull();
});
