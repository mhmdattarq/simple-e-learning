<?php

namespace App\Livewire\Pimpinan\Persetujuan;

use App\Enums\Role;
use App\Models\Course;
use App\Repositories\PimpinanRepo;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PersetujuanData extends Component
{
    public ?array $selectedCourse = null;

    public string $decisionNotes = '';

    public array $summary = [];

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, [Role::Pimpinan, Role::Admin], true)) {
            abort(403, 'Akses terbatas hanya untuk peran Pimpinan dan Eksekutif.');
        }

        $this->summary = PimpinanRepo::getPersetujuanSummary();
    }

    public function tinjau($id): void
    {
        try {
            $course = PimpinanRepo::getById($id);

            $this->selectedCourse = [
                'id' => $course->id,
                'code' => $course->code,
                'title' => $course->title,
                'category_name' => $course->category?->name ?? '-',
                'type_label' => $course->isPermanent() ? 'Mandiri (Buka Terus)' : 'Batch Terjadwal',
                'method_label' => ucfirst((string) $course->method),
                'location' => $course->location ?: 'Daring / Online',
                'quota' => (int) $course->quota,
                'target_audience' => $course->target_audience ?: 'Aparatur Sipil Negara (ASN)',
                'budget_source' => $course->budget_source ?: 'APBD Kabupaten Aceh Timur',
                'description' => $course->description ?: 'Tidak ada deskripsi rinci.',
                'competencies' => $course->competencies ?: 'Tidak ada spesifikasi kompetensi.',
                'tor_url' => $course->tor_file ? asset('storage/'.$course->tor_file) : null,
                'start_date' => $course->start_date?->format('d M Y') ?? 'Fleksibel',
                'end_date' => $course->end_date?->format('d M Y') ?? 'Fleksibel',
                'creator_name' => $course->creator?->name ?? 'Admin Diklat',
                'submitted_at' => $course->updated_at?->format('d M Y, H:i').' WIB',
                'approval_notes' => $course->approval_notes,
            ];

            $this->decisionNotes = '';
            $this->resetErrorBag();
            $this->dispatch('showModal', id: 'modalTinjauRencana');
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Tidak dapat memuat detail usulan pelatihan.',
            ]);
        }
    }

    public function approve(): void
    {
        if (! $this->selectedCourse) {
            return;
        }

        $course = Course::findOrFail($this->selectedCourse['id']);
        $this->authorize('approve', $course);

        try {
            $result = PimpinanRepo::approvePlan($course->id, $this->decisionNotes);

            $this->summary = PimpinanRepo::getPersetujuanSummary();
            $this->dispatch('closeModal', id: 'modalTinjauRencana');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Rencana Disetujui',
                'message' => $result['message'],
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($key, $msg);
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem saat menyetujui rencana.',
            ]);
        }
    }

    public function reject(): void
    {
        if (! $this->selectedCourse) {
            return;
        }

        $course = Course::findOrFail($this->selectedCourse['id']);
        $this->authorize('approve', $course);

        $this->validate([
            'decisionNotes' => 'required|min:5',
        ], [
            'decisionNotes.required' => 'Catatan revisi wajib diisi agar Admin Diklat mengetahui poin yang perlu diperbaiki.',
            'decisionNotes.min' => 'Catatan revisi minimal berisi 5 karakter penjelasan.',
        ]);

        try {
            $result = PimpinanRepo::rejectPlan($course->id, $this->decisionNotes);

            $this->summary = PimpinanRepo::getPersetujuanSummary();
            $this->dispatch('closeModal', id: 'modalTinjauRencana');
            $this->dispatch('alert-show', data: [
                'type' => 'warning',
                'title' => 'Dikembalikan untuk Revisi',
                'message' => $result['message'],
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $msg) {
                    $this->addError($key, $msg);
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan sistem saat mengembalikan rencana.',
            ]);
        }
    }

    public function render()
    {
        return view('mods.pimpinan.persetujuan.persetujuan-data');
    }
}
