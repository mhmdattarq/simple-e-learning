<?php

namespace App\Livewire\Peserta\Profile;

use App\Models\AuditLog;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Profil Peserta - SIMPEL BKPSDM')]
class ProfileIndex extends Component
{
    public array $form = [
        'name' => '',
        'nip' => '',
        'email' => '',
        'phone_number' => '',
        'address' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public string $avatarUrl = '';

    public bool $isGoogleUser = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->form = [
            'name' => (string) ($user->name ?? ''),
            'nip' => (string) ($user->nip ?? ''),
            'email' => (string) ($user->email ?? ''),
            'phone_number' => (string) ($user->phone_number ?? ''),
            'address' => (string) ($user->address ?? ''),
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->avatarUrl = (string) ($user->avatar_url ?? '');
        $this->isGoogleUser = $user->isGoogleUser();
    }

    public function rules(): array
    {
        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.nip' => ['nullable', 'numeric', 'digits:18', Rule::unique('users', 'nip')->ignore(Auth::id())],
            'form.phone_number' => ['required', 'string', 'max:20'],
            'form.address' => ['required', 'string', 'max:1000'],
            'form.password' => ['required', 'string', 'min:8', 'confirmed'],
            'form.password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required' => 'Nama lengkap wajib diisi.',
            'form.nip.numeric' => 'NIP harus berupa angka.',
            'form.nip.digits' => 'NIP harus berjumlah 18 digit angka.',
            'form.nip.unique' => 'NIP ini sudah digunakan oleh akun lain.',
            'form.phone_number.required' => 'Nomor WhatsApp / HP wajib diisi.',
            'form.address.required' => 'Alamat lengkap wajib diisi.',
            'form.password.required' => 'Kata sandi wajib diisi.',
            'form.password.min' => 'Kata sandi minimal 8 karakter.',
            'form.password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'form.password_confirmation.required' => 'Konfirmasi kata sandi wajib diisi.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $cleanNip = ! empty($this->form['nip']) ? preg_replace('/[^0-9]/', '', (string) $this->form['nip']) : null;

        $oldValues = [
            'name' => $user->name,
            'nip' => $user->nip,
            'phone_number' => $user->phone_number,
            'address' => $user->address,
        ];

        $newValues = [
            'name' => trim($this->form['name']),
            'nip' => $cleanNip,
            'phone_number' => trim($this->form['phone_number']),
            'address' => trim($this->form['address']),
            'password' => Hash::make($this->form['password']),
        ];

        $user->update($newValues);

        $this->form['password'] = '';
        $this->form['password_confirmation'] = '';

        $auditNewValues = $newValues;
        $auditNewValues['password'] = '[UPDATED]';

        AuditLog::log(
            action: 'user.profile_updated',
            auditable: $user,
            oldValues: $oldValues,
            newValues: $auditNewValues,
            notes: 'Pembaruan data profil dan pengaturan kata sandi oleh pengguna.'
        );

        $this->form['password'] = '';
        $this->form['password_confirmation'] = '';

        session()->flash('success', 'Profil Anda berhasil disimpan dan diperbarui.');
    }

    public function render()
    {
        /** @var User|null $user */
        $user = Auth::user();

        $quizAttempts = $user
            ? QuizAttempt::with(['quiz.course', 'quiz.chapter'])
                ->where('user_id', $user->id)
                ->latest('submitted_at')
                ->get()
            : collect();

        return view('mods.peserta.profile.profile-index', [
            'user' => $user,
            'isComplete' => $user?->isProfileComplete() ?? false,
            'quizAttempts' => $quizAttempts,
        ]);
    }
}
