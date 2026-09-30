<?php

namespace App\Livewire\Auth;

use App\Mail\VerifyEmailNotification;
use App\Models\AuditLog;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
#[Title('Verifikasi Email - SIMPEL BKPSDM Aceh Timur')]
class VerifyEmail extends Component
{
    public string $token = '';

    public bool $isVerified = false;

    public string $statusMessage = '';

    public string $errorMessage = '';

    public string $resendEmail = '';

    public string $resendMessage = '';

    public function mount(string $token = ''): void
    {
        $this->token = trim($token);

        if ($this->token === '') {
            $this->isVerified = false;
            $this->errorMessage = 'Tautan verifikasi tidak valid.';

            return;
        }

        $user = EmailVerification::verify($this->token);

        if ($user) {
            $this->isVerified = true;
            $this->statusMessage = "Selamat, akun dengan email {$user->email} berhasil diaktifkan!";

            AuditLog::log(
                action: 'user.email_verified',
                auditable: $user,
                oldValues: null,
                newValues: ['email_verified_at' => $user->email_verified_at?->toIso8601String()],
                notes: 'Alamat email berhasil diverifikasi melalui tautan aktivasi Livewire.'
            );
        } else {
            $this->isVerified = false;
            $this->errorMessage = 'Tautan verifikasi tidak valid atau masa berlakunya telah berakhir (kedaluwarsa).';
        }
    }

    /**
     * Kirim ulang tautan aktivasi dengan rate limiting dan anti-enumeration.
     */
    public function resendVerification(): void
    {
        $this->resetErrorBag();
        $this->resendMessage = '';

        $email = Str::lower(trim($this->resendEmail));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addError('resendEmail', 'Format alamat email tidak valid.');

            return;
        }

        $throttleKey = 'resend-verify|'.$email.'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->addError('resendEmail', "Terlalu banyak permintaan kirim ulang. Tunggu {$seconds} detik lagi.");

            return;
        }

        RateLimiter::hit($throttleKey, 300);

        $user = User::where('email', $email)->first();

        if ($user && ! $user->email_verified_at) {
            $rawToken = EmailVerification::createTokenFor($user);
            Mail::to($user->email)->queue(new VerifyEmailNotification($user, $rawToken));

            AuditLog::log(
                action: 'user.verification_resent',
                auditable: $user,
                oldValues: null,
                newValues: ['email' => $user->email],
                notes: 'Tautan verifikasi email dikirim ulang dari halaman verifikasi.'
            );
        }

        $this->resendMessage = 'Jika alamat email terdaftar dan belum aktif, tautan verifikasi baru telah dikirimkan ke kotak masuk email Anda.';
        $this->resendEmail = '';
    }

    public function render()
    {
        return view('mods.auth.verify-email');
    }
}
