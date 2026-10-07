<?php

namespace App\Livewire\Auth;

use App\Repositories\AuthRepo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
class Register extends Component
{
    #[Url(as: 'redirect')]
    public ?string $redirectTo = null;

    /**
     * State form registrasi peserta.
     *
     * @var array<string, string>
     */
    public array $form = [
        'name' => '',
        'nip' => '',
        'email' => '',
        'phone_number' => '',
        'address' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public function mount(?string $redirect = null): void
    {
        $target = $redirect ?: request()->query('redirect');
        if ($target && is_string($target)) {
            $this->redirectTo = $target;
            session()->put('url.intended', $target);
        }
    }

    /**
     * Validation rules for user registration.
     */
    public function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.nip' => 'nullable|numeric|digits:18|unique:users,nip',
            'form.email' => 'required|email|max:255|unique:users,email',
            'form.phone_number' => 'required|string|max:20',
            'form.address' => 'required|string|max:1000',
            'form.password' => 'required|string|min:6|confirmed',
        ];
    }

    /**
     * Validation error messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'form.name.required' => 'Nama lengkap wajib diisi.',
            'form.nip.numeric' => 'NIP harus berupa angka.',
            'form.nip.digits' => 'NIP harus berjumlah 18 digit angka.',
            'form.nip.unique' => 'NIP ini sudah terdaftar dalam sistem.',
            'form.email.required' => 'Alamat email wajib diisi.',
            'form.email.email' => 'Format email tidak valid.',
            'form.email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'form.phone_number.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'form.address.required' => 'Alamat domisili wajib diisi.',
            'form.password.required' => 'Kata sandi wajib diisi.',
            'form.password.min' => 'Kata sandi minimal 6 karakter.',
            'form.password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }

    /**
     * Handle participant registration.
     */
    public function register()
    {
        $this->validate();

        $user = AuthRepo::registerPeserta($this->form);

        if (! $user) {
            $this->addError('form.email', 'Terjadi kesalahan sistem saat mendaftarkan akun. Silakan coba kembali.');

            return;
        }

        session()->flash('alert-show', [
            'type' => 'success',
            'title' => 'Pendaftaran Berhasil',
            'message' => 'Pendaftaran akun berhasil! Tautan aktivasi telah dikirimkan ke email '.$user->email.'.',
        ]);

        session()->flash('success', 'Pendaftaran akun berhasil! Tautan aktivasi telah dikirimkan ke email '.$user->email.'. Silakan verifikasi email Anda sebelum masuk.');

        $params = $this->redirectTo ? ['redirect' => $this->redirectTo] : [];

        return $this->redirectRoute('login', $params, navigate: true);
    }

    public function render()
    {
        return view('mods.auth.register');
    }
}
