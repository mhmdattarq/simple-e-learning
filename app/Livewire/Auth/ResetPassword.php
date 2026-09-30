<?php

namespace App\Livewire\Auth;

use App\Repositories\PasswordResetRepo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
#[Title('Atur Ulang Kata Sandi - SIMPEL BKPSDM Aceh Timur')]
class ResetPassword extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $tokenValid = false;

    public ?string $invalidReason = null;

    public ?string $errorMessage = null;

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi kata sandi baru wajib diisi.',
        ];
    }

    public function mount(string $token): void
    {
        $this->token = trim($token);
        $this->email = trim((string) request()->query('email', ''));

        if ($this->token === '' || $this->email === '') {
            $this->tokenValid = false;
            $this->invalidReason = 'Tautan pemulihan kata sandi tidak lengkap atau tidak valid.';

            return;
        }

        $verification = PasswordResetRepo::verifyToken($this->email, $this->token);

        if ($verification['valid']) {
            $this->tokenValid = true;
        } else {
            $this->tokenValid = false;
            $this->invalidReason = $verification['reason'];
        }
    }

    /**
     * Eksekusi atur ulang kata sandi baru.
     */
    public function resetPassword()
    {
        $this->errorMessage = null;
        $this->validate();

        $result = PasswordResetRepo::resetPassword(
            email: $this->email,
            rawToken: $this->token,
            newPassword: $this->password,
            ip: request()->ip()
        );

        if (! $result['success']) {
            $this->tokenValid = false;
            $this->invalidReason = $result['message'];
            $this->errorMessage = $result['message'];

            return;
        }

        session()->flash('success', $result['message']);

        return $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('mods.auth.reset-password');
    }
}
