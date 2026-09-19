<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
#[Title('Masuk ke Portal - SIMPEL E-Learning BKPSDM Aceh Timur')]
class Login extends Component
{
    #[Rule(['required', 'string'], message: [
        'required' => 'Email atau NIP wajib diisi.',
    ])]
    public string $email = '';

    #[Rule(['required', 'string'], message: [
        'required' => 'Kata sandi wajib diisi.',
    ])]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle incoming authentication attempt.
     */
    public function authenticate()
    {
        $this->validate();

        $fieldType = filter_var($this->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (! Auth::attempt([$fieldType => $this->email, 'password' => $this->password], $this->remember)) {
            $this->addError('email', 'Kredensial yang Anda masukkan tidak cocok dengan data akun kami.');

            return;
        }

        session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function render()
    {
        return view('mods.auth.login');
    }
}
