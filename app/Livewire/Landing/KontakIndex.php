<?php

namespace App\Livewire\Landing;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('templates.layouts.landing')]
#[Title('Hubungi Kami - SIMPEL BKPSDM')]
class KontakIndex extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $subject = '';

    public string $message = '';

    public bool $isSubmitted = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $this->name = (string) ($user->name ?? '');
            $this->email = (string) ($user->email ?? '');
            $this->phone = (string) ($user->phone_number ?? '');
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'min:3', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama lengkap minimal 3 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'subject.required' => 'Subjek atau kategori pertanyaan wajib diisi.',
            'subject.min' => 'Subjek pertanyaan minimal 3 karakter.',
            'message.required' => 'Pesan atau pertanyaan Anda wajib diisi.',
            'message.min' => 'Pesan terlalu pendek, mohon jelaskan minimal 10 karakter.',
            'message.max' => 'Pesan terlalu panjang (maksimal 2000 karakter).',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
        ];
    }

    public function sendMessage(): void
    {
        $this->validate();

        // Optional audit log tracking for messages
        if (Auth::check()) {
            AuditLog::log(
                action: 'contact.message_sent',
                newValues: [
                    'name' => trim($this->name),
                    'email' => trim($this->email),
                    'subject' => trim($this->subject),
                ],
                notes: 'Pengguna mengirimkan pertanyaan melalui halaman Kontak: '.trim($this->subject),
                userId: Auth::id()
            );
        }

        $this->isSubmitted = true;
        session()->flash('contact-success', 'Pesan Anda berhasil dikirim! Tim helpdesk BKPSDM Aceh Timur akan segera meninjau dan merespon pesan Anda.');

        $this->subject = '';
        $this->message = '';
    }

    public function render(): View
    {
        return view('mods.landing.kontak-index');
    }
}
