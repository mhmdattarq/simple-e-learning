<?php

namespace App\Livewire\Peserta\Profile;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Profil Kepegawaian ASN - SIMPEL BKPSDM')]
class ProfileIndex extends Component
{
    public array $form = [
        'name' => '',
        'email' => '',
        'nip' => '',
        'phone_number' => '',
        'opd_agency' => '',
        'position' => '',
        'rank_class' => '',
    ];

    public string $avatarUrl = '';

    public bool $isGoogleUser = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->form = [
            'name' => (string) ($user->name ?? ''),
            'email' => (string) ($user->email ?? ''),
            'nip' => (string) ($user->nip ?? ''),
            'phone_number' => (string) ($user->phone_number ?? ''),
            'opd_agency' => (string) ($user->opd_agency ?? ''),
            'position' => (string) ($user->position ?? ''),
            'rank_class' => (string) ($user->rank_class ?? ''),
        ];

        $this->avatarUrl = (string) ($user->avatar_url ?? '');
        $this->isGoogleUser = $user->isGoogleUser();
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.nip' => [
                'required',
                'numeric',
                'digits:18',
                Rule::unique('users', 'nip')->ignore($userId),
            ],
            'form.phone_number' => ['required', 'string', 'max:20'],
            'form.opd_agency' => ['required', 'string', 'max:255'],
            'form.position' => ['required', 'string', 'max:255'],
            'form.rank_class' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'form.name.required' => 'Nama lengkap dan gelar wajib diisi.',
            'form.nip.required' => 'NIP wajib diisi.',
            'form.nip.numeric' => 'NIP harus berupa angka.',
            'form.nip.digits' => 'NIP harus tepat 18 digit.',
            'form.nip.unique' => 'NIP ini sudah terdaftar oleh pengguna lain.',
            'form.phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'form.opd_agency.required' => 'Instansi / OPD asal wajib diisi.',
            'form.position.required' => 'Jabatan saat ini wajib diisi.',
            'form.rank_class.required' => 'Pangkat / Golongan wajib dipilih.',
        ];
    }

    public function save(): void
    {
        $this->validate();

        /** @var User $user */
        $user = Auth::user();

        $oldValues = [
            'name' => $user->name,
            'nip' => $user->nip,
            'phone_number' => $user->phone_number,
            'opd_agency' => $user->opd_agency,
            'position' => $user->position,
            'rank_class' => $user->rank_class,
        ];

        $newValues = [
            'name' => trim($this->form['name']),
            'nip' => trim($this->form['nip']),
            'phone_number' => trim($this->form['phone_number']),
            'opd_agency' => trim($this->form['opd_agency']),
            'position' => trim($this->form['position']),
            'rank_class' => trim($this->form['rank_class']),
        ];

        $user->update($newValues);

        AuditLog::log(
            action: 'user.profile_updated',
            auditable: $user,
            oldValues: $oldValues,
            newValues: $newValues,
            notes: 'Pembaruan data profil kepegawaian ASN oleh pengguna.'
        );

        session()->flash('success', 'Profil kepegawaian ASN Anda berhasil disimpan dan diperbarui.');
    }

    public function render()
    {
        return view('mods.peserta.profile.profile-index', [
            'user' => Auth::user(),
            'isComplete' => Auth::user()?->isAsnProfileComplete() ?? false,
        ]);
    }
}
