<div>
    {{-- Header & Breadcrumbs --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Riwayat Aktivitas &amp; Notifikasi</h5>
            <p class="text-muted mb-0">Catatan historis lengkap seluruh aksi penambahan, perubahan, dan penghapusan yang
                dilakukan oleh akun Anda.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
            <button type="button" wire:click="markAllAsRead" class="btn btn-info d-inline-flex align-items-center w-100">
                <i class="ri-check-double-line"></i> Tandai Semua Sudah Dibaca
            </button>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row row-cols-1 row-cols-md-3 g-3 mb-24">
        <div class="col">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-20 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light fw-medium text-xs text-uppercase d-block mb-1">Total
                            Aktivitas</span>
                        <h4 class="fw-bold text-dark mb-0">{{ number_format($this->stats['total']) }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Keseluruhan aksi tercatat</small>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-history-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-20 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light fw-medium text-xs text-uppercase d-block mb-1">Aktivitas Hari
                            Ini</span>
                        <h4 class="fw-bold text-dark mb-0">{{ number_format($this->stats['today']) }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Aksi dalam 24 jam terakhir</small>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card simpel-card border-0 shadow-sm radius-16 p-20 bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary-light fw-medium text-xs text-uppercase d-block mb-1">7 Hari
                            Terakhir</span>
                        <h4 class="fw-bold text-dark mb-0">{{ number_format($this->stats['this_week']) }}</h4>
                        <small class="text-muted" style="font-size: 11px;">Aktivitas sepekan ke belakang</small>
                    </div>
                    <div
                        class="w-48-px h-48-px rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fs-4">
                        <i class="ri-line-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Filters and Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div class="card-header bg-white pt-20 pb-16 px-20 border-bottom">
            <div class="row g-2 align-items-center justify-content-between">
                <div class="col-md-4 col-12">
                    <div class="position-relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="form-control form-control-sm ps-5 radius-8"
                            placeholder="Cari aksi, modul, catatan, atau IP...">
                        <i
                            class="ri-search-line position-absolute top-50 translate-middle-y start-0 ms-3 text-muted"></i>
                    </div>
                </div>
                <div class="col-md-8 col-12">
                    <div class="d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
                        <div style="min-width: 150px;">
                            <select wire:model.live="module" class="form-select form-select-sm radius-8">
                                <option value="all">Semua Modul</option>
                                <option value="kategori">Kategori</option>
                                <option value="kelas">Kelas &amp; Diklat</option>
                                <option value="materi">Materi &amp; Kurikulum</option>
                                <option value="evaluasi">Evaluasi &amp; Kuis</option>
                            </select>
                        </div>
                        <div style="min-width: 140px;">
                            <input type="date" wire:model.live="date" class="form-control form-control-sm radius-8"
                                title="Filter Tanggal">
                        </div>
                        @if ($search || $module !== 'all' || $date)
                            <button type="button" wire:click="resetFilters"
                                class="btn btn-outline-secondary btn-sm radius-8 d-flex align-items-center gap-1">
                                <i class="ri-refresh-line"></i> Reset
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;" class="text-center">No</th>
                            <th style="min-width: 170px;">Waktu &amp; Tanggal</th>
                            <th style="min-width: 180px;">Modul &amp; Aksi</th>
                            <th style="min-width: 280px;">Keterangan Aktivitas</th>
                            <th style="min-width: 130px;">IP Address</th>
                            <th style="width: 100px;" class="text-center">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $index => $log)
                            @php
                                $actionMeta = $this->formatAction($log->action);
                            @endphp
                            <tr>
                                <td class="text-center text-muted fs-7">
                                    {{ $logs->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-semibold text-dark fs-7">
                                            {{ $log->created_at ? $log->created_at->translatedFormat('d M Y, H:i') : '-' }}
                                        </span>
                                        <small class="text-muted" style="font-size: 11px;">
                                            <i class="ri-time-line"></i>
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : '-' }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center {{ $actionMeta['badge'] }} flex-shrink-0">
                                            <i class="{{ $actionMeta['icon'] }} fs-6"></i>
                                        </span>
                                        <div>
                                            <span
                                                class="badge rounded-pill bg-light text-dark border px-2 py-1 fs-8 mb-1 d-inline-block">
                                                {{ $actionMeta['module'] }}
                                            </span>
                                            <div class="fw-semibold text-dark fs-7">{{ $actionMeta['title'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="mb-0 text-dark fs-7 fw-medium" style="word-break: break-word;">
                                        {{ $log->notes ?? 'Aktivitas sistem dilakukan' }}
                                    </p>
                                    @if ($log->new_values)
                                        <div class="mt-1">
                                            @foreach (array_slice($log->new_values, 0, 2) as $key => $val)
                                                <span
                                                    class="badge bg-neutral-100 text-secondary-light border px-2 py-1 me-1 fs-8 font-monospace">
                                                    {{ $key }}:
                                                    {{ is_array($val) ? json_encode($val) : \Illuminate\Support\Str::limit((string) $val, 25) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-secondary border font-monospace px-2 py-1 fs-8">
                                        <i class="ri-global-line me-1"></i>{{ $log->ip_address ?: '127.0.0.1' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" wire:click="showDetail({{ $log->id }})"
                                        class="btn btn-outline-primary btn-sm radius-8 px-2 py-1 fs-7 d-inline-flex align-items-center gap-1">
                                        <i class="ri-eye-line"></i> Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-40">
                                    <div class="d-flex flex-column align-items-center">
                                        <div
                                            class="w-56-px h-56-px rounded-circle bg-neutral-100 text-muted d-flex align-items-center justify-content-center fs-2 mb-2">
                                            <i class="ri-file-search-line"></i>
                                        </div>
                                        <h6 class="fw-semibold text-dark mb-1">Tidak Ada Riwayat Aktivitas</h6>
                                        <p class="text-muted text-xs mb-0">Belum ada riwayat aktivitas yang sesuai
                                            dengan kriteria filter Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            @if ($logs->hasPages())
                <div class="p-20 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $logs->firstItem() }} sampai {{ $logs->lastItem() }} dari {{ $logs->total() }}
                        entri
                    </small>
                    <div>
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Modal --}}
    @if ($this->selectedLog)
        <div class="modal fade show d-block" tabindex="-1"
            style="background-color: rgba(7, 26, 51, 0.6); z-index: 1070;" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 radius-16 shadow-lg overflow-hidden">
                    <div class="modal-header py-16 px-20 border-bottom" style="background: #071a33; color: #fff;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri-information-line fs-5 text-simple-gold"></i>
                            <h6 class="modal-title text-white fw-bold mb-0">Detail Riwayat Audit
                                #{{ $this->selectedLog->id }}</h6>
                        </div>
                        <button type="button" wire:click="closeDetail" class="btn-close btn-close-white"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-20" style="max-height: calc(85vh - 120px); overflow-y: auto;">
                        @php
                            $selectedMeta = $this->formatAction($this->selectedLog->action);
                        @endphp
                        {{-- Meta Overview --}}
                        <div class="bg-light p-16 radius-12 border mb-20">
                            <div class="row g-3">
                                <div class="col-md-6 col-12">
                                    <span class="text-muted text-xs d-block mb-1">Aksi &amp; Modul</span>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge {{ $selectedMeta['badge'] }} px-2 py-1 fs-7">
                                            <i class="{{ $selectedMeta['icon'] }} me-1"></i>
                                            {{ $selectedMeta['module'] }}
                                        </span>
                                        <span class="fw-bold text-dark fs-7">{{ $selectedMeta['title'] }}</span>
                                    </div>
                                    <small class="text-muted font-monospace mt-1 d-block">Action:
                                        {{ $this->selectedLog->action }}</small>
                                </div>
                                <div class="col-md-6 col-12">
                                    <span class="text-muted text-xs d-block mb-1">Waktu Eksekusi</span>
                                    <div class="fw-semibold text-dark fs-7">
                                        {{ $this->selectedLog->created_at ? $this->selectedLog->created_at->translatedFormat('l, d F Y - H:i:s') : '-' }}
                                    </div>
                                    <small class="text-muted d-block">
                                        ({{ $this->selectedLog->created_at ? $this->selectedLog->created_at->diffForHumans() : '-' }})
                                    </small>
                                </div>
                                <div class="col-md-6 col-12">
                                    <span class="text-muted text-xs d-block mb-1">Pengguna Akun</span>
                                    <div class="fw-semibold text-dark fs-7">
                                        {{ $this->selectedLog->user->name ?? 'Sistem' }}
                                    </div>
                                    <small class="text-muted">{{ $this->selectedLog->user->email ?? '-' }}</small>
                                </div>
                                <div class="col-md-6 col-12">
                                    <span class="text-muted text-xs d-block mb-1">IP Address &amp; Perangkat</span>
                                    <div class="font-monospace text-dark fs-7">
                                        {{ $this->selectedLog->ip_address ?: '127.0.0.1' }}
                                    </div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 300px;"
                                        title="{{ $this->selectedLog->user_agent }}">
                                        {{ $this->selectedLog->user_agent ?: 'Browser / API' }}
                                    </small>
                                </div>
                            </div>
                        </div>

                        {{-- Catatan Lengkap --}}
                        <div class="mb-20">
                            <h6 class="fw-bold text-dark fs-7 mb-2">Catatan Ringkasan Aktivitas:</h6>
                            <div class="p-12 radius-8 bg-neutral-100 border text-dark fs-7">
                                {{ $this->selectedLog->notes ?: 'Tidak ada catatan naratif khusus.' }}
                            </div>
                        </div>

                        {{-- Data Perubahan (Old Values & New Values) --}}
                        <div class="row g-3">
                            @if ($this->selectedLog->old_values)
                                <div class="{{ $this->selectedLog->new_values ? 'col-md-6' : 'col-12' }}">
                                    <div class="border rounded p-12 bg-white">
                                        <div class="fw-bold text-danger fs-7 mb-2 d-flex align-items-center gap-1">
                                            <i class="ri-arrow-left-down-line"></i> Nilai Sebelum (Old Values)
                                        </div>
                                        <pre class="bg-light p-2 rounded text-xs mb-0 border" style="max-height: 250px; overflow-y: auto;"><code>{{ json_encode($this->selectedLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            @endif

                            @if ($this->selectedLog->new_values)
                                <div class="{{ $this->selectedLog->old_values ? 'col-md-6' : 'col-12' }}">
                                    <div class="border rounded p-12 bg-white">
                                        <div class="fw-bold text-success fs-7 mb-2 d-flex align-items-center gap-1">
                                            <i class="ri-arrow-right-up-line"></i> Nilai Sesudah (New Values)
                                        </div>
                                        <pre class="bg-light p-2 rounded text-xs mb-0 border" style="max-height: 250px; overflow-y: auto;"><code>{{ json_encode($this->selectedLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer py-12 px-20 bg-light border-top">
                        <button type="button" wire:click="closeDetail"
                            class="btn btn-secondary btn-sm radius-8 px-3">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
