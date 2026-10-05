<?php

use App\Models\AuditLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component {
    public function getAuditLogsProperty(): Collection
    {
        if (!Auth::check()) {
            return collect();
        }

        return AuditLog::where('user_id', Auth::id())->latest('id')->limit(5)->get();
    }

    public function getUnreadCountProperty(): int
    {
        if (!Auth::check()) {
            return 0;
        }

        $lastReadId = (int) cache()->get('admin_read_audit_id_' . Auth::id(), 0);

        return AuditLog::where('user_id', Auth::id())->where('id', '>', $lastReadId)->count();
    }

    public function markAsRead(): void
    {
        if (!Auth::check()) {
            return;
        }

        $latestLog = AuditLog::where('user_id', Auth::id())->latest('id')->first();
        if ($latestLog) {
            cache()->put('admin_read_audit_id_' . Auth::id(), $latestLog->id, now()->addDays(30));
        }
    }

    /**
     * @return array{icon: string, bg: string, title: string, desc: string, time: string}
     */
    public function formatNotification(AuditLog $log): array
    {
        $action = $log->action;
        $notes = $log->notes;
        $time = $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja';

        $map = [
            'category.created' => [
                'icon' => 'ri-folder-add-line',
                'bg' => 'bg-primary-subtle text-primary',
                'title' => 'Kategori Ditambahkan',
                'desc' => $log->new_values['name'] ?? ($notes ?? 'Menambahkan kategori baru'),
            ],
            'category.updated' => [
                'icon' => 'ri-folder-settings-line',
                'bg' => 'bg-info-subtle text-info',
                'title' => 'Kategori Diperbarui',
                'desc' => $log->new_values['name'] ?? ($notes ?? 'Memperbarui data kategori'),
            ],
            'category.deleted' => [
                'icon' => 'ri-folder-reduce-line',
                'bg' => 'bg-danger-subtle text-danger',
                'title' => 'Kategori Dihapus',
                'desc' => $log->old_values['name'] ?? ($notes ?? 'Menghapus data kategori'),
            ],
            'course.created' => [
                'icon' => 'ri-book-read-line',
                'bg' => 'bg-success-subtle text-success',
                'title' => 'Kelas Baru Dibuat',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Menambahkan kelas baru'),
            ],
            'course.updated' => [
                'icon' => 'ri-edit-line',
                'bg' => 'bg-info-subtle text-info',
                'title' => 'Kelas Diperbarui',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Memperbarui data kelas'),
            ],
            'course.deleted' => [
                'icon' => 'ri-delete-bin-line',
                'bg' => 'bg-danger-subtle text-danger',
                'title' => 'Kelas Dihapus',
                'desc' => $log->old_values['title'] ?? ($notes ?? 'Menghapus data kelas'),
            ],
            'course.submitted' => [
                'icon' => 'ri-send-plane-fill',
                'bg' => 'bg-warning-subtle text-warning',
                'title' => 'Kelas Diajukan',
                'desc' => $notes ?? 'Mengajukan persetujuan kelas ke pimpinan',
            ],
            'course.archived' => [
                'icon' => 'ri-archive-line',
                'bg' => 'bg-secondary-subtle text-secondary',
                'title' => 'Kelas Diarsipkan',
                'desc' => $notes ?? 'Mengarsipkan kelas',
            ],
            'chapter.created' => [
                'icon' => 'ri-play-list-add-line',
                'bg' => 'bg-primary-subtle text-primary',
                'title' => 'Bab Materi Dibuat',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Menambahkan bab kurikulum'),
            ],
            'chapter.updated' => [
                'icon' => 'ri-pencil-line',
                'bg' => 'bg-info-subtle text-info',
                'title' => 'Bab Materi Diperbarui',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Memperbarui bab kurikulum'),
            ],
            'chapter.deleted' => [
                'icon' => 'ri-delete-bin-line',
                'bg' => 'bg-danger-subtle text-danger',
                'title' => 'Bab Materi Dihapus',
                'desc' => $log->old_values['title'] ?? ($notes ?? 'Menghapus bab kurikulum'),
            ],
            'lesson.created' => [
                'icon' => 'ri-file-add-line',
                'bg' => 'bg-success-subtle text-success',
                'title' => 'Materi Pelajaran Dibuat',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Menambahkan materi pelajaran'),
            ],
            'lesson.updated' => [
                'icon' => 'ri-file-edit-line',
                'bg' => 'bg-info-subtle text-info',
                'title' => 'Materi Pelajaran Diperbarui',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Memperbarui materi pelajaran'),
            ],
            'lesson.deleted' => [
                'icon' => 'ri-file-reduce-line',
                'bg' => 'bg-danger-subtle text-danger',
                'title' => 'Materi Pelajaran Dihapus',
                'desc' => $log->old_values['title'] ?? ($notes ?? 'Menghapus materi pelajaran'),
            ],
            'quiz.created' => [
                'icon' => 'ri-questionnaire-line',
                'bg' => 'bg-warning-subtle text-warning',
                'title' => 'Evaluasi Kuis Dibuat',
                'desc' => $log->new_values['title'] ?? ($notes ?? 'Menambahkan evaluasi kuis'),
            ],
            'quiz.deleted' => [
                'icon' => 'ri-delete-bin-line',
                'bg' => 'bg-danger-subtle text-danger',
                'title' => 'Evaluasi Kuis Dihapus',
                'desc' => $log->old_values['title'] ?? ($notes ?? 'Menghapus evaluasi kuis'),
            ],
        ];

        $data = $map[$action] ?? [
            'icon' => 'ri-history-line',
            'bg' => 'bg-primary-subtle text-primary',
            'title' => 'Aktivitas: ' . str_replace('.', ' ', ucwords($action, '.')),
            'desc' => $notes ?? 'Aktivitas sistem tersimpan',
        ];
        $data['time'] = $time;

        return $data;
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
};
?>

<div class="admin-header-sticky-wrapper" style="position: sticky; top: 0; z-index: 1040;">
    <div class="navbar-header">
        <div class="row align-items-center justify-content-between">
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <button type="button" class="sidebar-mobile-toggle border-0 bg-transparent p-0 d-inline-flex align-items-center justify-content-center text-dark fs-4" aria-label="Buka Menu Navigasi" style="width: 36px; height: 36px; cursor: pointer;">
                        <i class="ri-menu-2-line"></i>
                    </button>
                    <div class="d-flex d-lg-none align-items-center gap-2">
                        <img src="{{ asset('mine/logo_aceh_timur.webp') }}" alt="Logo" style="width: 28px; height: 28px; object-fit: contain;">
                        <span class="fw-bold text-dark fs-7">SIMPEL</span>
                    </div>
                    <div class="d-none d-md-flex align-items-center gap-2">
                        <span class="text-simple-gold medium d-none d-lg-inline">Sistem Informasi Manajemen
                            Pembelajaran</span>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    {{-- Notifikasi --}}
                    <div class="dropdown position-relative" x-data="{ open: false }" @click.outside="open = false"
                        wire:poll.60s>
                        <button
                            class="has-indicator w-40-px h-40-px bg-neutral-100 rounded-circle d-flex justify-content-center align-items-center border-0 position-relative"
                            type="button" @click="open = !open" :aria-expanded="open.toString()">
                            <i class="ri-notification-3-line icon text-xl text-primary-light"></i>
                            @if ($this->unreadCount > 0)
                                <span
                                    class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"
                                    style="width: 10px; height: 10px; margin-top: 6px; margin-left: -6px;">
                                    <span class="visually-hidden">Notifikasi Baru</span>
                                </span>
                            @endif
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 shadow-lg notification-dropdown-menu" x-show="open"
                            x-cloak :class="{ 'show': open }">
                            <div class="py-12 px-16 border-bottom d-flex align-items-center justify-content-between"
                                style="background-color: #071a33;">
                                <div class="d-flex align-items-center gap-2">
                                    <h6 class="text-white fw-semibold mb-0 fs-6">Aktivitas Saya</h6>
                                    @if ($this->unreadCount > 0)
                                        <span class="badge bg-warning text-dark">{{ $this->unreadCount }} Baru</span>
                                    @else
                                        <span class="badge bg-secondary text-white">{{ $this->auditLogs->count() }}
                                            Aktivitas</span>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($this->unreadCount > 0)
                                        <button type="button" wire:click="markAsRead"
                                            class="btn btn-link text-white-50 p-0 text-decoration-none fs-7 hover-text-white d-flex align-items-center gap-1"
                                            title="Tandai semua sudah dibaca">
                                            <i class="ri-check-double-line"></i> Dibaca
                                        </button>
                                    @endif
                                    <button type="button" @click="open = false"
                                        class="d-flex d-md-none btn btn-link text-white-50 p-0 text-decoration-none hover-text-white ms-1"
                                        aria-label="Tutup Notifikasi" style="font-size: 18px; line-height: 1;">
                                        <i class="ri-close-line"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="p-2" style="max-height: min(380px, calc(100vh - 160px)); overflow-y: auto;">
                                @forelse ($this->auditLogs as $log)
                                    @php
                                        $notif = $this->formatNotification($log);
                                    @endphp
                                    <a href="{{ Route::has('admin.notifikasi') ? route('admin.notifikasi') : url('/admin/notifikasi') }}"
                                        class="dropdown-item p-10 rounded d-flex gap-2 border-bottom align-items-start text-wrap">
                                        <span
                                            class="w-36-px h-36-px rounded-circle d-flex justify-content-center align-items-center {{ $notif['bg'] }} flex-shrink-0 mt-1">
                                            <i class="{{ $notif['icon'] }} fs-5"></i>
                                        </span>
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <p class="mb-0 fw-semibold text-xs text-dark text-truncate">
                                                {{ $notif['title'] }}</p>
                                            <p class="mb-0 text-secondary-light text-xs text-truncate"
                                                style="font-size: 11px;">{{ $notif['desc'] }}</p>
                                            <small class="text-muted d-block mt-1"
                                                style="font-size: 10px;">{{ $notif['time'] }}</small>
                                        </div>
                                    </a>
                                @empty
                                    <div class="py-4 text-center text-muted">
                                        <i
                                            class="ri-notification-off-line text-2xl d-block mb-1 text-secondary-light"></i>
                                        <span class="text-xs">Belum ada riwayat aktivitas terbaru</span>
                                    </div>
                                @endforelse
                            </div>
                            <div class="text-center py-2 border-top bg-light">
                                <a href="{{ route('admin.notifikasi') }}" wire:navigate
                                    class="fw-semibold text-xs text-primary d-inline-flex align-items-center gap-1">
                                    Lihat Detail Notifikasi & Riwayat <i class="ri-arrow-right-s-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- User Profile --}}
                    <div class="dropdown position-relative" x-data="{ open: false }" @click.outside="open = false">
                        <button class="d-flex align-items-center gap-2 border-0 bg-transparent p-0" type="button"
                            @click="open = !open" :aria-expanded="open.toString()">
                            <div class="seal sidebar-brand-seal"
                                style="width: 38px !important; height: 38px !important; font-size: 15px !important; border-radius: 10px !important;">
                                {{ auth()->user() ? strtoupper(substr(auth()->user()->name, 0, 2)) : 'MS' }}
                            </div>
                            <div class="d-none d-lg-flex flex-column text-start">
                                <span class="fw-bold text-dark fs-6"
                                    style="line-height: 1.2;">{{ auth()->user()->name ?? 'Administrator' }}</span>
                                <small class="text-secondary-light"
                                    style="font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Admin Diklat' }}</small>
                            </div>
                            <i class="ri-arrow-down-s-line text-secondary-light d-none d-lg-block"
                                :style="open ? 'transform: rotate(180deg); transition: transform 0.2s;' :
                                    'transition: transform 0.2s;'"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow-lg p-0 profile-dropdown-menu" x-show="open" x-cloak
                            :class="{ 'show': open }">
                            <div class="py-16 px-16" style="background: #071a33; color: #fff;">
                                <h6 class="text-white fw-semibold mb-1"
                                    style="font-size: 14px; line-height: 1.3; word-break: break-word;">
                                    {{ auth()->user()->name ?? 'Administrator' }}</h6>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="badge"
                                        style="background: #f3bc42; color: #071a33; font-weight: 700; font-size: 11px;">{{ auth()->user()?->role?->label() ?? 'Admin Diklat' }}</span>
                                    @if (auth()->user()?->nip)
                                        <small class="text-white-50" style="font-size: 11px;">·
                                            {{ auth()->user()->nip }}</small>
                                    @endif
                                </div>
                            </div>
                            <ul class="to-top-list list-unstyled p-2 m-0">
                                <li>
                                    <a class="dropdown-item text-black px-12 py-8 hover-text-primary d-flex align-items-center gap-2 rounded"
                                        href="{{ route('admin.profil') }}" wire:navigate>
                                        <i class="ri-user-line icon text-lg"></i>
                                        Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider my-1">
                                </li>
                                <li>
                                    <button type="button"
                                        class="dropdown-item text-danger px-12 py-8 hover-text-danger d-flex align-items-center gap-2 rounded border-0 bg-transparent w-100 text-start"
                                        data-bs-toggle="modal" data-bs-target="#modalLogout">
                                        <i class="ri-logout-box-r-line icon text-lg"></i> Keluar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
