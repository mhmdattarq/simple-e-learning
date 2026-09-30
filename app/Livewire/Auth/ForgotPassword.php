<?php

namespace App\Livewire\Auth;

use App\Repositories\PasswordResetRepo;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
#[Title('Lupa Kata Sandi - SIMPEL BKPSDM Aceh Timur')]
class ForgotPassword extends Component
{
    public string $email = '';

    public bool $linkSent = false;

    public string $statusMessage = '';

    /**
     * @return array<string, string>
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }

    /**
     * Ajukan permintaan tautan atur ulang kata sandi.
     */
    public function sendResetLink(): void
    {
        $this->validate();

        $normalizedEmail = Str::lower(trim($this->email));

        PasswordResetRepo::sendResetLink(
            email: $normalizedEmail,
            ip: request()->ip(),
            userAgent: request()->userAgent()
        );

        $this->linkSent = true;
        $this->statusMessage = 'Jika alamat email Anda terdaftar dalam sistem, tautan pemulihan kata sandi telah dikirimkan ke kotak masuk Anda. Silakan periksa email Anda (termasuk folder spam).';
        $this->email = '';
    }

    public function render()
    {
        return view('mods.auth.forgot-password');
    }
}
