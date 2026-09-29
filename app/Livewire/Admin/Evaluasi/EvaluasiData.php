<?php

namespace App\Livewire\Admin\Evaluasi;

use App\Models\Course;
use App\Models\Quiz;
use App\Repositories\EvaluasiRepo;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('templates.layouts.app')]
#[Title('Data Evaluasi & Kuis - SIMPEL BKPSDM')]
class EvaluasiData extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'course', history: true)]
    public string $course_id = '';

    #[Url(as: 'type', history: true)]
    public string $type = 'all'; // 'all' | 'chapter' | 'final'

    public int $perPage = 10;

    public ?int $selectedQuizId = null;

    public bool $isDetailOpen = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCourseId(): void
    {
        $this->resetPage();
    }

    public function updatingType(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->course_id = '';
        $this->type = 'all';
        $this->resetPage();
    }

    /**
     * Membuka modal/drawer rincian butir soal kuis.
     */
    public function openDetail(int $quizId): void
    {
        $this->selectedQuizId = $quizId;
        $this->isDetailOpen = true;
    }

    /**
     * Menutup modal/drawer rincian kuis.
     */
    public function closeDetail(): void
    {
        $this->selectedQuizId = null;
        $this->isDetailOpen = false;
    }

    /**
     * Menghubungkan tombol hapus ke modalDelete global SIMPEL.
     */
    public function hookModalDelete(int $id, string $title): void
    {
        $quiz = Quiz::withCount('attempts')->find($id);

        if (! $quiz) {
            return;
        }

        $msg = "Apakah Anda yakin ingin menghapus evaluasi \"{$title}\"?\nSeluruh butir soal dan data evaluasi ini akan dinonaktifkan.";
        $msgBoxClass = '';

        if ($quiz->attempts_count > 0) {
            $msg = "PERHATIAN:\nEvaluasi \"{$title}\" telah dikerjakan oleh {$quiz->attempts_count} peserta.\n\nJika dihapus, kuis ini tidak lagi dapat diakses peserta. Apakah Anda yakin ingin melanjutkan?";
            $msgBoxClass = 'bg-danger-subtle border-danger text-danger';
        }

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Evaluasi Kuis',
            'msg' => $msg,
            'msgBoxClass' => $msgBoxClass,
            'dispatch' => 'EvaluasiData-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Handler penghapusan kuis setelah dikonfirmasi via modal global.
     */
    #[On('EvaluasiData-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;

        if (! $id) {
            return;
        }

        $this->dispatch('closeModal', id: 'modalDelete');

        $success = EvaluasiRepo::delete((int) $id);

        if ($success) {
            if ($this->selectedQuizId === (int) $id) {
                $this->closeDetail();
            }

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Evaluasi kuis berhasil dihapus.',
            ]);
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data evaluasi.',
            ]);
        }
    }

    /**
     * Alternatif penghapusan langsung jika dipanggil via wire:confirm.
     */
    public function deleteQuiz(int $id): void
    {
        $success = EvaluasiRepo::delete($id);

        if ($success) {
            if ($this->selectedQuizId === $id) {
                $this->closeDetail();
            }

            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Evaluasi kuis berhasil dihapus.',
            ]);
        }
    }

    /**
     * Daftar opsi program kelas untuk filter.
     */
    #[Computed]
    public function courses(): Collection
    {
        return Course::orderBy('title', 'asc')->get(['id', 'title']);
    }

    /**
     * Statistik ringkas evaluasi untuk kartu metrik.
     *
     * @return array{
     *     total_quizzes: int,
     *     chapter_quizzes: int,
     *     final_quizzes: int,
     *     total_attempts: int
     * }
     */
    #[Computed]
    public function stats(): array
    {
        return EvaluasiRepo::getStats();
    }

    /**
     * Data kuis yang sedang dipilih untuk ditinjau butir soalnya.
     */
    #[Computed]
    public function selectedQuiz(): ?Quiz
    {
        if (! $this->selectedQuizId) {
            return null;
        }

        return EvaluasiRepo::getByIdWithDetails($this->selectedQuizId);
    }

    public function render()
    {
        $quizzes = EvaluasiRepo::getPaginated(
            search: $this->search,
            courseId: $this->course_id ? (int) $this->course_id : null,
            type: $this->type === 'all' ? null : $this->type,
            perPage: $this->perPage
        );

        return view('mods.admin.evaluasi.evaluasi-data', [
            'quizzes' => $quizzes,
        ]);
    }
}
