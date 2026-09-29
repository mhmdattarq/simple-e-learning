<?php

namespace App\Livewire\Admin\Profile;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.app')]
#[Title('Profil Saya - SIMPEL BKPSDM')]
class AdminProfileIndex extends Component
{
    public array $form = [
        'name' => '',
        'email' => '',
        'phone_number' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->form = [
            'name' => (string) ($user->name ?? ''),
            'email' => (string) ($user->email ?? ''),
            'phone_number' => (string) ($user->phone_number ?? ''),
            'password' => '',
            'password_confirmation' => '',
        ];
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'form.phone_number' => ['nullable', 'string', 'max:20'],
            'form.password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required' => 'Nama lengkap wajib diisi.',
            'form.email.required' => 'Alamat email wajib diisi.',
            'form.email.email' => 'Format alamat email tidak valid.',
            'form.email.unique' => 'Alamat email tersebut sudah digunakan oleh akun lain.',
            'form.phone_number.max' => 'Nomor WhatsApp / HP maksimal 20 digit.',
            'form.password.min' => 'Kata sandi baru minimal 8 karakter.',
            'form.password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ];

        $newValues = [
            'name' => trim($this->form['name']),
            'email' => strtolower(trim($this->form['email'])),
            'phone_number' => ! empty($this->form['phone_number']) ? trim($this->form['phone_number']) : null,
        ];

        $passwordUpdated = ! empty($this->form['password']);
        if ($passwordUpdated) {
            $newValues['password'] = Hash::make($this->form['password']);
        }

        $user->update($newValues);

        $auditNewValues = $newValues;
        if (isset($auditNewValues['password'])) {
            $auditNewValues['password'] = '[UPDATED]';
        }

        AuditLog::log(
            action: 'user.profile_updated',
            auditable: $user,
            oldValues: $oldValues,
            newValues: $auditNewValues,
            notes: $passwordUpdated
                ? 'Admin memperbarui data identitas profil dan mengubah kata sandi akun.'
                : 'Admin memperbarui data identitas profil akun.',
            userId: $user->id
        );

        $this->form['password'] = '';
        $this->form['password_confirmation'] = '';

        session()->flash('success', 'Profil admin Anda berhasil disimpan dan diperbarui.');
    }

    public function render(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        return view('mods.admin.profile.admin-profile-index', [
            'user' => $user,
        ]);
    }
}
