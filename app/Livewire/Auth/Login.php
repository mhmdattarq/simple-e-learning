<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Mail\VerifyEmailNotification;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
class Login extends Component
{
    public string $identifier = '';

    public string $email = '';

    public string $password = '';

    public bool $remember = false;

    public string $errorMessage = '';

    /**
     * Keep identifier and email in sync for backward compatibility.
     */
    public function updatedEmail($value): void
    {
        $this->identifier = (string) $value;
        $this->errorMessage = '';
        $this->resetErrorBag(['identifier', 'email']);
    }

    public function updatedIdentifier($value): void
    {
        $this->email = (string) $value;
        $this->errorMessage = '';
        $this->resetErrorBag(['identifier', 'email']);
    }

    public function updatedPassword($value): void
    {
        $this->errorMessage = '';
        $this->resetErrorBag(['password']);
    }

    public function rules(): array
    {
        return [
            'identifier' => 'required_without:email|string',
            'email' => 'required_without:identifier|string',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required_without' => 'Email atau NIP wajib diisi.',
            'email.required_without' => 'Email atau NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    public string $unverifiedEmail = '';

    public int $lockoutSeconds = 0;

    /**
     * Resend email verification link with rate limiting.
     */
    public function resendVerification(): void
    {
        if (empty($this->unverifiedEmail)) {
            return;
        }

        $throttleKey = 'resend-verify|'.Str::lower($this->unverifiedEmail).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak permintaan kirim ulang. Silakan tunggu {$seconds} detik lagi.";

            return;
        }

        RateLimiter::hit($throttleKey, 300);

        $user = User::where('email', Str::lower($this->unverifiedEmail))->first();
        if ($user && ! $user->email_verified_at) {
            $rawToken = EmailVerification::createTokenFor($user);
            Mail::to($user->email)->send(new VerifyEmailNotification($user, $rawToken));

            AuditLog::log(
                action: 'user.verification_resent',
                auditable: $user,
                oldValues: null,
                newValues: ['email' => $user->email],
                notes: 'Tautan verifikasi email dikirim ulang dari portal masuk.'
            );
        }

        session()->flash('success', 'Tautan aktivasi baru telah dikirimkan ke email Anda. Silakan periksa kotak masuk atau spam.');
        $this->errorMessage = '';
    }

    /**
     * Handle incoming authentication attempt with enterprise security hardening.
     */
    public function authenticate()
    {
        $this->errorMessage = '';
        $this->unverifiedEmail = '';
        $this->lockoutSeconds = 0;

        $resolvedIdentifier = trim($this->identifier !== '' ? $this->identifier : $this->email);

        if ($resolvedIdentifier === '') {
            $this->addError('identifier', 'Email atau NIP wajib diisi.');
            $this->addError('email', 'Email atau NIP wajib diisi.');

            if ($this->password === '') {
                $this->addError('password', 'Kata sandi wajib diisi.');
            }

            return;
        }

        if ($this->password === '') {
            $this->addError('password', 'Kata sandi wajib diisi.');

            return;
        }

        // 1. Dual-Key Rate Limiting (Global IP + Targeted Identity)
        $ipThrottleKey = 'login-ip|'.request()->ip();
        if (RateLimiter::tooManyAttempts($ipThrottleKey, 30)) {
            $this->lockoutSeconds = RateLimiter::availableIn($ipThrottleKey);
            $this->errorMessage = "Terlalu banyak aktivitas dari IP Anda. Silakan tunggu {$this->lockoutSeconds} detik lagi.";
            $this->addError('identifier', $this->errorMessage);
            $this->addError('email', $this->errorMessage);

            return;
        }

        $throttleKey = Str::transliterate(
            Str::lower($resolvedIdentifier).'|'.request()->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->lockoutSeconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak percobaan masuk. Silakan tunggu {$this->lockoutSeconds} detik lagi.";
            $this->addError('identifier', $this->errorMessage);
            $this->addError('email', $this->errorMessage);

            return;
        }

        // 2. Autentikasi Fleksibel (NIP atau Email) & Pencegahan Timing Attack
        $field = is_numeric($resolvedIdentifier) ? 'nip' : 'email';
        $targetUser = User::where($field, $resolvedIdentifier)->first();

        if (! $targetUser) {
            // Constant-time dummy comparison to defeat side-channel timing enumeration attacks
            Hash::check($this->password, '$2y$12$K1q6F0qB.vXQ9cQ7sH6z8K1vfeh7zN5cQ9cQ7sH6z8K1vfeh7zN5c');
        }

        $credentials = [
            $field => $resolvedIdentifier,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials, $this->remember)) {
            /** @var User $user */
            $user = Auth::user();

            // Proteksi email belum diverifikasi
            if (! $user->email_verified_at) {
                $unverified = $user->email;
                Auth::logout();
                $this->unverifiedEmail = $unverified;
                $this->errorMessage = 'Alamat email akun Anda belum diverifikasi. Silakan periksa kotak masuk atau spam email Anda.';
                $this->addError('identifier', $this->errorMessage);
                $this->addError('email', $this->errorMessage);

                return;
            }

            RateLimiter::clear($throttleKey);
            RateLimiter::clear($ipThrottleKey);
            session()->regenerate();

            // 3. Pelacakan Login & Perangkat (Last Login Metadata)
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
                'last_login_user_agent' => request()->userAgent(),
            ])->save();

            // 4. Catat Log Autentikasi Berhasil
            AuditLog::log(
                action: 'user.login',
                auditable: $user,
                oldValues: null,
                newValues: [
                    'role' => $user->role?->value ?? (string) $user->role,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ],
                notes: 'Pengguna berhasil masuk ke sistem.'
            );

            // Role-based redirection & welcome toast notification:
            $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

            session()->flash('alert-show', [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'berhasil login selamat datang '.$roleName,
            ]);

            if ($user->isPimpinan()) {
                return redirect()->intended(route('pimpinan.persetujuan.data'));
            }

            if ($user->hasAdminAccess()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('landing'));
        }

        // Catat kegagalan login dan increment attempt
        RateLimiter::hit($throttleKey, 60);
        RateLimiter::hit($ipThrottleKey, 60);

        AuditLog::log(
            action: 'user.login_failed',
            auditable: $targetUser,
            oldValues: null,
            newValues: [
                'identifier' => $resolvedIdentifier,
                'ip' => request()->ip(),
            ],
            notes: 'Percobaan masuk gagal: Kredensial tidak cocok.'
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->lockoutSeconds = RateLimiter::availableIn($throttleKey);

            AuditLog::log(
                action: 'user.login_lockout',
                auditable: $targetUser,
                oldValues: null,
                newValues: [
                    'identifier' => $resolvedIdentifier,
                    'ip' => request()->ip(),
                    'lockout_seconds' => $this->lockoutSeconds,
                ],
                notes: 'Batas percobaan masuk gagal terlampaui. Akun/IP dikunci sementara.'
            );

            $this->errorMessage = "Terlalu banyak percobaan masuk. Silakan tunggu {$this->lockoutSeconds} detik lagi.";
        } else {
            $this->errorMessage = 'Email/NIP atau kata sandi yang Anda masukkan salah.';
        }

        $this->addError('identifier', $this->errorMessage);
        $this->addError('email', $this->errorMessage);
    }

    /**
     * Alias method authenticate() untuk kompatibilitas pemanggilan form login.
     */
    public function login()
    {
        return $this->authenticate();
    }

    public function render()
    {
        return view('mods.auth.login');
    }
}
