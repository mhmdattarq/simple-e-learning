<?php

namespace App\Livewire\Admin\Pendaftaran;

use App\Enums\Role;
use App\Models\Course;
use App\Repositories\PendaftaranRepo;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class PendaftaranData extends Component
{
    public ?int $selectedCourseId = null;

    public ?string $registration_open_at = '';

    public ?string $registration_close_at = '';

    public ?array $courseStats = null;

    public ?array $selectedDetail = null;

    public function mount(): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, [Role::Admin, Role::Verifikator, Role::Pimpinan], true)) {
            abort(403, 'Akses tidak diizinkan untuk peran ini.');
        }
    }

    public function updatedSelectedCourseId($value): void
    {
        $courseId = ! empty($value) ? (int) $value : null;
        $this->selectedCourseId = $courseId;

        if ($courseId) {
            $this->loadCourseStats($courseId);
        } else {
            $this->courseStats = null;
            $this->registration_open_at = '';
            $this->registration_close_at = '';
        }

        $this->resetErrorBag();
    }

    public function loadCourseStats(int $courseId): void
    {
        $this->courseStats = PendaftaranRepo::getCourseRegistrationStats($courseId);

        if ($this->courseStats) {
            $this->registration_open_at = $this->courseStats['registration_open_at'] ?? '';
            $this->registration_close_at = $this->courseStats['registration_close_at'] ?? '';
        }
    }

    public function openPeriod(): void
    {
        if (! $this->selectedCourseId) {
            $this->addError('selectedCourseId', 'Pilih pelatihan terlebih dahulu.');

            return;
        }

        $course = Course::findOrFail($this->selectedCourseId);
        $this->authorize('manageRegistration', $course);

        try {
            $result = PendaftaranRepo::openRegistrationPeriod(
                $this->selectedCourseId,
                $this->registration_open_at,
                $this->registration_close_at
            );

            $this->selectedCourseId = null;
            $this->courseStats = null;
            $this->registration_open_at = '';
            $this->registration_close_at = '';

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Pendaftaran Dibuka',
                'message' => $result['message'],
            ]);

            $this->dispatch('reloadDT', data: 'dtTable');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat membuka pendaftaran.',
            ]);
        }
    }

    public function closePeriod(): void
    {
        if (! $this->selectedCourseId) {
            $this->addError('selectedCourseId', 'Pilih pelatihan terlebih dahulu.');

            return;
        }

        $course = Course::findOrFail($this->selectedCourseId);
        $this->authorize('manageRegistration', $course);

        try {
            $result = PendaftaranRepo::closeRegistrationPeriod($this->selectedCourseId);

            $this->loadCourseStats($this->selectedCourseId);

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Pendaftaran Ditutup',
                'message' => $result['message'],
            ]);

            $this->dispatch('reloadDT', data: 'dtTable');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $key => $messages) {
                foreach ($messages as $message) {
                    $this->addError($key, $message);
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => $e->getMessage() ?: 'Terjadi kesalahan saat menutup pendaftaran.',
            ]);
        }
    }

    public function hookModalDelete($id, $identity): void
    {
        $reg = PendaftaranRepo::getById($id);
        $this->authorize('delete', $reg);

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Pendaftaran',
            'msg' => 'Apakah Anda yakin ingin menghapus data pendaftaran '.$identity.'? Berkas surat rekomendasi terkait akan dihapus secara permanen.',
            'dispatch' => 'PendaftaranData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    #[On('PendaftaranData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $reg = PendaftaranRepo::getById($id);
        $this->authorize('delete', $reg);

        $process = PendaftaranRepo::delete($id);

        if ($process) {
            $this->dispatch('closeModal', id: 'modalDelete');
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data Pendaftaran Berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT', data: 'dtTable');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data pendaftaran.',
            ]);
        }
    }

    public function showDetail($id): void
    {
        try {
            $reg = PendaftaranRepo::getById($id);
            $this->authorize('view', $reg);

            $this->selectedDetail = [
                'id' => $reg->id,
                'registration_number' => $reg->registration_number,
                'status_label' => $reg->status->label(),
                'status_badge' => $reg->status->badgeClass(),
                'enrolled_at' => $reg->enrolled_at?->format('d M Y, H:i').' WIB',
                'notes' => $reg->notes,
                'recommendation_letter_url' => $reg->recommendation_letter_path
                    ? asset('storage/'.$reg->recommendation_letter_path)
                    : null,
                // User Details
                'user_name' => $reg->user?->name ?? '-',
                'user_nip' => $reg->user?->nip ?? '-',
                'user_email' => $reg->user?->email ?? '-',
                'user_phone' => $reg->user?->phone_number ?? '-',
                'user_opd' => $reg->user?->opd_agency ?? '-',
                'user_position' => $reg->user?->position ?? '-',
                'user_rank' => $reg->user?->rank_class ?? '-',
                // Course Details
                'course_title' => $reg->course?->title ?? '-',
                'course_code' => $reg->course?->code ?? '-',
                'course_category' => $reg->course?->category?->name ?? '-',
                'course_method' => ucfirst((string) $reg->course?->method),
                'course_type' => $reg->course?->isPermanent() ? 'Mandiri (Buka Terus)' : 'Batch Terjadwal',
                // Verifier Details
                'verifier_name' => $reg->verifier?->name ?? 'Belum ada verifikator',
                'verified_at' => $reg->verified_at ? $reg->verified_at->format('d M Y, H:i').' WIB' : '-',
                'verification_notes' => $reg->verification_notes ?? '-',
            ];

            $this->dispatch('openModal', id: 'modalDetailPendaftaran');
        } catch (AuthorizationException $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Akses Ditolak',
                'message' => 'Anda tidak memiliki hak akses untuk melihat pendaftaran ini.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Tidak dapat memuat detail pendaftaran.',
            ]);
        }
    }

    public function render()
    {
        $settingCourses = PendaftaranRepo::getCoursesForRegistrationSetting();

        return view('mods.admin.pendaftaran.pendaftaran-data', [
            'settingCourses' => $settingCourses,
            'canManageRegistration' => auth()->user()?->can('manageRegistration', Course::class) ?? false,
        ]);
    }
}
