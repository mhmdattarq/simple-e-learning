<?php

namespace App\Livewire\Admin\Evaluasi;

use App\Models\Course;
use App\Models\Quiz;
use App\Repositories\EvaluasiRepo;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Detail & Monitoring Evaluasi Kelas - SIMPEL BKPSDM')]
class EvaluasiDetail extends Component
{
    public int $courseId;

    public function mount($course_id): void
    {
        $this->courseId = (int) $course_id;
    }

    /**
     * Membuka modal konfirmasi hapus evaluasi (kuis).
     */
    public function hookModalDelete(int $id, string $identity): void
    {
        $quiz = Quiz::withCount('attempts')->find($id);

        if (! $quiz) {
            return;
        }

        $warning = '';
        if ($quiz->attempts_count > 0) {
            $warning = "\n\nPerhatian: Kuis ini telah memiliki {$quiz->attempts_count} riwayat pengerjaan oleh peserta.";
        }

        $dtHook = [
            'id' => $id,
            'title' => 'Konfirmasi Hapus Evaluasi',
            'msg' => 'Apakah Anda yakin ingin menghapus evaluasi "'.$identity.'"?'.$warning,
            'dispatch' => 'EvaluasiDetail-delete',
        ];

        $this->dispatch('modal-delete-setDeleteId', $dtHook);
    }

    /**
     * Handler eksekusi penghapusan kuis dari dispatch modal delete.
     */
    #[On('EvaluasiDetail-delete')]
    public function delete($data): void
    {
        $id = is_array($data) ? ($data['id'] ?? null) : $data;
        $success = EvaluasiRepo::delete((int) $id);

        $this->dispatch('closeModal', id: 'modalDelete');

        if ($success) {
            $this->dispatch('alert-show', data: [
                'type' => 'success',
                'title' => 'Berhasil',
                'message' => 'Data evaluasi kuis berhasil dihapus.',
            ]);
            $this->dispatch('reloadDT');
        } else {
            $this->dispatch('alert-show', data: [
                'type' => 'danger',
                'title' => 'Gagal',
                'message' => 'Terjadi kesalahan sistem saat menghapus data evaluasi.',
            ]);
        }
    }

    public function render()
    {
        $course = Course::with([
            'category',
            'chapters' => function ($q) {
                $q->orderBy('order', 'asc')
                    ->with(['quizzes' => function ($qz) {
                        $qz->with(['questions.options', 'attempts']);
                    }]);
            },
            'finalQuiz.questions.options',
            'finalQuiz.attempts',
        ])->findOrFail($this->courseId);

        $stats = EvaluasiRepo::getCourseStats($this->courseId);

        return view('mods.admin.evaluasi.evaluasi-detail', compact('course', 'stats'));
    }
}
