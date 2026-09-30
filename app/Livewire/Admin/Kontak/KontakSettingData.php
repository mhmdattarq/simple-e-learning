<?php

namespace App\Livewire\Admin\Kontak;

use App\Models\AuditLog;
use App\Models\ContactFaq;
use App\Models\ContactSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('templates.layouts.app')]
class KontakSettingData extends Component
{
    public string $activeTab = 'kontak'; // 'kontak' or 'faq'

    // Form Pengaturan Kontak
    public string $office_title = '';

    public string $address = '';

    public string $whatsapp_number = '';

    public string $whatsapp_label = '';

    public string $email = '';

    public string $service_days = '';

    public string $service_hours = '';

    public string $maps_embed_url = '';

    public string $maps_url = '';

    // Form Modal FAQ
    public ?int $faqId = null;

    public string $faqQuestion = '';

    public string $faqAnswer = '';

    public int $faqOrder = 1;

    public bool $faqIsActive = true;

    public function mount(): void
    {
        $setting = ContactSetting::getSettings();

        $this->office_title = (string) ($setting->office_title ?? '');
        $this->address = (string) ($setting->address ?? '');
        $this->whatsapp_number = (string) ($setting->whatsapp_number ?? '');
        $this->whatsapp_label = (string) ($setting->whatsapp_label ?? '');
        $this->email = (string) ($setting->email ?? '');
        $this->service_days = (string) ($setting->service_days ?? '');
        $this->service_hours = (string) ($setting->service_hours ?? '');
        $this->maps_embed_url = (string) ($setting->maps_embed_url ?? '');
        $this->maps_url = (string) ($setting->maps_url ?? '');
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['kontak', 'faq'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function updatedMapsEmbedUrl($value): void
    {
        $sanitized = $this->sanitizeEmbedUrl((string) $value);
        if ($sanitized !== $value) {
            $this->maps_embed_url = $sanitized ?? '';
        }
    }

    protected function sanitizeEmbedUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        // If user pasted full <iframe ... src="URL" ...></iframe> snippet
        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $url, $matches)) {
            return $matches[1];
        }

        return $url;
    }

    public function rules(): array
    {
        return [
            'office_title' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'whatsapp_label' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'service_days' => ['nullable', 'string', 'max:100'],
            'service_hours' => ['nullable', 'string', 'max:100'],
            'maps_embed_url' => ['nullable', 'string', 'max:2000'],
            'maps_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function saveSettings(): void
    {
        $this->maps_embed_url = (string) ($this->sanitizeEmbedUrl($this->maps_embed_url) ?? '');
        $validated = $this->validate();
        $validated['maps_embed_url'] = $this->sanitizeEmbedUrl($validated['maps_embed_url'] ?? null);

        $setting = ContactSetting::getSettings();
        $setting->update($validated);

        AuditLog::log(
            action: 'contact_setting.updated',
            auditable: $setting,
            newValues: $validated,
            notes: 'Admin memperbarui pengaturan informasi kontak & kantor BKPSDM',
            userId: Auth::id()
        );

        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Tersimpan',
            'message' => 'Informasi kontak dan kantor BKPSDM berhasil diperbarui.',
        ]);
    }

    public function openCreateFaqModal(): void
    {
        $this->resetErrorBag();
        $this->faqId = null;
        $this->faqQuestion = '';
        $this->faqAnswer = '';
        $this->faqOrder = (int) (ContactFaq::max('order') ?? 0) + 1;
        $this->faqIsActive = true;

        $this->dispatch('openModal', id: 'modalFaqForm');
    }

    public function openEditFaqModal(int $id): void
    {
        $this->resetErrorBag();
        $faq = ContactFaq::findOrFail($id);

        $this->faqId = $faq->id;
        $this->faqQuestion = $faq->question;
        $this->faqAnswer = $faq->answer;
        $this->faqOrder = $faq->order;
        $this->faqIsActive = (bool) $faq->is_active;

        $this->dispatch('openModal', id: 'modalFaqForm');
    }

    public function saveFaq(): void
    {
        $this->validate([
            'faqQuestion' => ['required', 'string', 'min:5', 'max:255'],
            'faqAnswer' => ['required', 'string', 'min:10', 'max:2000'],
            'faqOrder' => ['required', 'integer', 'min:1', 'max:999'],
            'faqIsActive' => ['boolean'],
        ], [
            'faqQuestion.required' => 'Pertanyaan FAQ wajib diisi.',
            'faqQuestion.min' => 'Pertanyaan minimal 5 karakter.',
            'faqAnswer.required' => 'Jawaban FAQ wajib diisi.',
            'faqAnswer.min' => 'Jawaban minimal 10 karakter.',
            'faqOrder.required' => 'Nomor urutan wajib diisi.',
        ]);

        $data = [
            'question' => trim($this->faqQuestion),
            'answer' => trim($this->faqAnswer),
            'order' => $this->faqOrder,
            'is_active' => $this->faqIsActive,
        ];

        if ($this->faqId) {
            $faq = ContactFaq::findOrFail($this->faqId);
            $faq->update($data);

            AuditLog::log(
                action: 'contact_faq.updated',
                auditable: $faq,
                newValues: $data,
                notes: "Admin memperbarui pertanyaan FAQ: \"{$faq->question}\"",
                userId: Auth::id()
            );

            $msg = 'Pertanyaan FAQ berhasil diperbarui.';
        } else {
            $faq = ContactFaq::create($data);

            AuditLog::log(
                action: 'contact_faq.created',
                auditable: $faq,
                newValues: $data,
                notes: "Admin menambahkan pertanyaan FAQ baru: \"{$faq->question}\"",
                userId: Auth::id()
            );

            $msg = 'Pertanyaan FAQ baru berhasil ditambahkan.';
        }

        $this->dispatch('closeModal', id: 'modalFaqForm');
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Berhasil',
            'message' => $msg,
        ]);
    }

    public function toggleFaqActive(int $id): void
    {
        $faq = ContactFaq::findOrFail($id);
        $faq->update(['is_active' => ! $faq->is_active]);

        $statusLabel = $faq->is_active ? 'diaktifkan' : 'dinonaktifkan';

        $this->dispatch('alert-show', data: [
            'type' => 'info',
            'title' => 'Status Diperbarui',
            'message' => "FAQ \"{$faq->question}\" berhasil {$statusLabel}.",
        ]);
    }

    public function hookModalDeleteFaq(int $id, string $identity): void
    {
        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus FAQ',
            'msg' => 'Apakah Anda yakin ingin menghapus pertanyaan FAQ "'.$identity.'"?',
            'dispatch' => 'KontakSettingData-deleteFaq',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('KontakSettingData-deleteFaq')]
    public function deleteFaq($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $faq = ContactFaq::findOrFail((int) $id);
        $question = $faq->question;

        $faq->delete();

        AuditLog::log(
            action: 'contact_faq.deleted',
            notes: "Admin menghapus pertanyaan FAQ: \"{$question}\"",
            userId: Auth::id()
        );

        $this->dispatch('closeModal', id: 'modalDelete');
        $this->dispatch('alert-show', data: [
            'type' => 'success',
            'title' => 'Terhapus',
            'message' => "FAQ \"{$question}\" berhasil dihapus.",
        ]);
    }

    public function render(): View
    {
        return view('mods.admin.kontak.setting-data', [
            'faqs' => ContactFaq::orderBy('order', 'asc')->get(),
            'setting' => ContactSetting::getSettings(),
        ]);
    }
}
