<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Mail\VerifyEmailNotification;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
     * Handle incoming authentication attempt.
     */
    public function authenticate()
    {
        $this->errorMessage = '';
        $this->unverifiedEmail = '';

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

        // Autentikasi fleksibel via Email atau NIP ASN
        // Jika angka saja -> NIP (18 digit ASN), selain itu -> Email
        $field = is_numeric($resolvedIdentifier) ? 'nip' : 'email';

        $throttleKey = Str::transliterate(
            Str::lower($resolvedIdentifier).'|'.request()->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->errorMessage = "Terlalu banyak percobaan masuk. Silakan tunggu {$seconds} detik lagi.";
            $this->addError('identifier', $this->errorMessage);
            $this->addError('email', $this->errorMessage);

            return;
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
            session()->regenerate();

            // Catat log autentikasi berhasil
            AuditLog::log(
                action: 'user.login',
                auditable: $user,
                oldValues: null,
                newValues: ['role' => $user->role?->value ?? (string) $user->role],
                notes: 'Pengguna berhasil masuk ke sistem.'
            );

            // Role-based redirection & welcome toast notification:
            // Internal Management (Admin, Mentor, Verifikator, Pimpinan) -> admin.dashboard with welcome toast
            // Siswa ASN (Peserta) -> landing
            if ($user->isPimpinan()) {
                $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

                session()->flash('alert-show', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'berhasil login selamat datang '.$roleName,
                ]);

                return redirect()->intended(route('pimpinan.persetujuan.data'));
            }

            if ($user->hasAdminAccess()) {
                $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

                session()->flash('alert-show', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'berhasil login selamat datang '.$roleName,
                ]);

                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('landing'));
        }

        RateLimiter::hit($throttleKey, 60);

        $this->errorMessage = 'Email/NIP atau kata sandi yang Anda masukkan salah.';
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
