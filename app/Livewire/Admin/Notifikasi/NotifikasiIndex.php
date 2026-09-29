<?php

namespace App\Livewire\Admin\Notifikasi;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('templates.layouts.app')]
#[Title('Notifikasi & Riwayat Aktivitas - SIMPEL BKPSDM')]
class NotifikasiIndex extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $search = '';

    public string $module = 'all'; // all, kategori, kelas, materi, evaluasi

    public string $date = '';

    public ?int $selectedLogId = null;

    public function mount(): void
    {
        $this->markAllAsRead();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedModule(): void
    {
        $this->resetPage();
    }

    public function updatedDate(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->module = 'all';
        $this->date = '';
        $this->resetPage();
    }

    public function markAllAsRead(): void
    {
        if (! Auth::check()) {
            return;
        }

        $latest = AuditLog::where('user_id', Auth::id())->latest('id')->first();
        if ($latest) {
            cache()->put('admin_read_audit_id_'.Auth::id(), $latest->id, now()->addDays(30));
        }
    }

    public function showDetail(int $id): void
    {
        $this->selectedLogId = $id;
    }

    public function closeDetail(): void
    {
        $this->selectedLogId = null;
    }

    #[Computed]
    public function selectedLog(): ?AuditLog
    {
        if (! $this->selectedLogId) {
            return null;
        }

        return AuditLog::with('user')->find($this->selectedLogId);
    }

    /**
     * @return array{total: int, today: int, this_week: int}
     */
    #[Computed]
    public function stats(): array
    {
        $userId = Auth::id();

        return [
            'total' => AuditLog::where('user_id', $userId)->count(),
            'today' => AuditLog::where('user_id', $userId)->whereDate('created_at', today())->count(),
            'this_week' => AuditLog::where('user_id', $userId)->where('created_at', '>=', now()->subDays(7))->count(),
        ];
    }

    /**
     * @return array{badge: string, icon: string, module: string, title: string}
     */
    public function formatAction(string $action): array
    {
        $map = [
            'category.created' => [
                'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon' => 'ri-folder-add-line',
                'module' => 'Kategori',
                'title' => 'Tambah Kategori',
            ],
            'category.updated' => [
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'ri-folder-settings-line',
                'module' => 'Kategori',
                'title' => 'Ubah Kategori',
            ],
            'category.deleted' => [
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'ri-folder-reduce-line',
                'module' => 'Kategori',
                'title' => 'Hapus Kategori',
            ],
            'course.created' => [
                'badge' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'ri-book-read-line',
                'module' => 'Kelas & Diklat',
                'title' => 'Buat Kelas',
            ],
            'course.updated' => [
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'ri-edit-line',
                'module' => 'Kelas & Diklat',
                'title' => 'Ubah Kelas',
            ],
            'course.deleted' => [
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'ri-delete-bin-line',
                'module' => 'Kelas & Diklat',
                'title' => 'Hapus Kelas',
            ],
            'course.submitted' => [
                'badge' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'icon' => 'ri-send-plane-fill',
                'module' => 'Kelas & Diklat',
                'title' => 'Pengajuan Kelas',
            ],
            'course.archived' => [
                'badge' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                'icon' => 'ri-archive-line',
                'module' => 'Kelas & Diklat',
                'title' => 'Arsip Kelas',
            ],
            'chapter.created' => [
                'badge' => 'bg-primary-subtle text-primary border border-primary-subtle',
                'icon' => 'ri-play-list-add-line',
                'module' => 'Kurikulum',
                'title' => 'Tambah Bab',
            ],
            'chapter.updated' => [
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'ri-pencil-line',
                'module' => 'Kurikulum',
                'title' => 'Ubah Bab',
            ],
            'chapter.deleted' => [
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'ri-delete-bin-line',
                'module' => 'Kurikulum',
                'title' => 'Hapus Bab',
            ],
            'lesson.created' => [
                'badge' => 'bg-success-subtle text-success border border-success-subtle',
                'icon' => 'ri-file-add-line',
                'module' => 'Materi',
                'title' => 'Tambah Materi',
            ],
            'lesson.updated' => [
                'badge' => 'bg-info-subtle text-info border border-info-subtle',
                'icon' => 'ri-file-edit-line',
                'module' => 'Materi',
                'title' => 'Ubah Materi',
            ],
            'lesson.deleted' => [
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'ri-file-reduce-line',
                'module' => 'Materi',
                'title' => 'Hapus Materi',
            ],
            'quiz.created' => [
                'badge' => 'bg-warning-subtle text-warning border border-warning-subtle',
                'icon' => 'ri-questionnaire-line',
                'module' => 'Evaluasi & Kuis',
                'title' => 'Buat Evaluasi',
            ],
            'quiz.deleted' => [
                'badge' => 'bg-danger-subtle text-danger border border-danger-subtle',
                'icon' => 'ri-delete-bin-line',
                'module' => 'Evaluasi & Kuis',
                'title' => 'Hapus Evaluasi',
            ],
        ];

        return $map[$action] ?? [
            'badge' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            'icon' => 'ri-history-line',
            'module' => 'Sistem',
            'title' => str_replace('.', ' ', ucwords($action, '.')),
        ];
    }

    public function render(): View
    {
        $query = AuditLog::query()
            ->where('user_id', Auth::id())
            ->latest('id');

        if (! empty($this->search)) {
            $term = trim($this->search);
            $query->where(function (Builder $q) use ($term) {
                $q->where('action', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%")
                    ->orWhere('ip_address', 'like', "%{$term}%");
            });
        }

        if ($this->module !== 'all') {
            $prefix = match ($this->module) {
                'kategori' => 'category.',
                'kelas' => 'course.',
                'materi' => ['chapter.', 'lesson.'],
                'evaluasi' => 'quiz.',
                default => null,
            };

            if (is_array($prefix)) {
                $query->where(function (Builder $q) use ($prefix) {
                    foreach ($prefix as $p) {
                        $q->orWhere('action', 'like', "{$p}%");
                    }
                });
            } elseif ($prefix) {
                $query->where('action', 'like', "{$prefix}%");
            }
        }

        if (! empty($this->date)) {
            $query->whereDate('created_at', $this->date);
        }

        $logs = $query->paginate(15);

        return view('mods.admin.notifikasi.notifikasi-index', [
            'logs' => $logs,
        ]);
    }
}
