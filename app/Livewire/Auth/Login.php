<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
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

    /**
     * Handle incoming authentication attempt.
     */
    public function authenticate()
    {
        $this->errorMessage = '';

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
            Str::lower($resolvedIdentifier) . '|' . request()->ip()
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
            RateLimiter::clear($throttleKey);
            session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            // Role-based redirection & welcome toast notification:
            // Internal Management (Admin, Mentor, Verifikator, Pimpinan) -> admin.dashboard with welcome toast
            // Siswa ASN (Peserta) -> landing
            if ($user->isPimpinan()) {
                $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

                session()->flash('alert-show', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'berhasil login selamat datang ' . $roleName,
                ]);

                return redirect()->intended(route('pimpinan.persetujuan.data'));
            }

            if ($user->hasAdminAccess()) {
                $roleName = $user->role instanceof Role ? $user->role->value : (string) $user->role;

                session()->flash('alert-show', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'message' => 'berhasil login selamat datang ' . $roleName,
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
