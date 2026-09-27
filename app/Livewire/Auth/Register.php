<?php

namespace App\Livewire\Auth;

use App\Repositories\AuthRepo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.auth')]
class Register extends Component
{
    /**
     * State form registrasi peserta ASN.
     *
     * @var array<string, string>
     */
    public array $form = [
        'name' => '',
        'nip' => '',
        'email' => '',
        'phone_number' => '',
        'opd_agency' => '',
        'position' => '',
        'rank_class' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    /**
     * Validation rules for user registration.
     */
    public function rules(): array
    {
        return [
            'form.name' => 'required|string|max:255',
            'form.nip' => 'required|numeric|digits:18|unique:users,nip',
            'form.email' => 'required|email|max:255|unique:users,email',
            'form.phone_number' => 'required|string|max:20',
            'form.opd_agency' => 'required|string|max:255',
            'form.position' => 'required|string|max:255',
            'form.rank_class' => 'required|string|max:100',
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
            'form.nip.required' => 'NIP wajib diisi.',
            'form.nip.numeric' => 'NIP harus berupa angka.',
            'form.nip.digits' => 'NIP harus tepat 18 digit.',
            'form.nip.unique' => 'NIP ini sudah terdaftar dalam sistem.',
            'form.email.required' => 'Alamat email wajib diisi.',
            'form.email.email' => 'Format email tidak valid.',
            'form.email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'form.phone_number.required' => 'Nomor telepon/WhatsApp wajib diisi.',
            'form.opd_agency.required' => 'Instansi/OPD wajib diisi.',
            'form.position.required' => 'Jabatan wajib diisi.',
            'form.rank_class.required' => 'Pangkat / Golongan wajib dipilih.',
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
            $this->addError('form.nip', 'Terjadi kesalahan sistem saat mendaftarkan akun. Silakan coba kembali.');

            return;
        }

        session()->flash('success', 'Pendaftaran akun berhasil! Silakan masuk dengan email/NIP dan kata sandi Anda.');

        return $this->redirectRoute('login', navigate: true);
    }

    public function render()
    {
        return view('mods.auth.register');
    }
}
