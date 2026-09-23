<div>
    {{-- Breadcrumb & Judul Modul --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tahap 5: Absensi Elektronik</h5>
            <p class="text-muted mb-0">Manajemen presensi digital sesi diklat, pembuatan token absensi 6 digit, pemantauan kehadiran realtime, dan koreksi status kehadiran ASN.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($isMentor)
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-7 fw-semibold">
                    <i class="ri-user-star-line me-1"></i> Mode Mentor Pengampu Sesi
                </span>
            @else
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 fs-7 fw-semibold">
                    <i class="ri-shield-user-line me-1"></i> Mode Admin: Monitoring & Audit Diklat
                </span>
            @endif
        </div>
    </div>

    {{-- Kartu Ringkasan Statistik & Indikator Sesi --}}
    <div class="row g-3 mb-24">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="ri-calendar-check-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Total Sesi Pelatihan Hari Ini</h6>
                    <h4 class="fw-bold text-dark mb-0">{{ $totalSessionsToday }} <span class="fs-8 fw-normal text-muted">Sesi</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="ri-radar-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Sesi Token Aktif Berjalan</h6>
                    <h4 class="fw-bold text-success mb-0">{{ $activeSessionsNow }} <span class="fs-8 fw-normal text-muted">Sesi Terbuka</span></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white d-flex flex-row align-items-center gap-3">
                <div class="p-3 bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="ri-user-star-line fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted fs-8 mb-1">Masa Berlaku Standar Token</h6>
                    <h4 class="fw-bold text-dark mb-0">15 <span class="fs-8 fw-normal text-muted">Menit per Sesi</span></h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Table --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-qr-code-line text-simple fs-5"></i>
                <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Sesi & Presensi Pelatihan</h6>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-refresh-absensi" title="Muat Ulang Data">
                    <i class="ri-refresh-line"></i> Segarkan
                </button>
            </div>
        </div>

        {{-- Kontainer tabel dengan wire:ignore agar render DOM DataTables tidak terganggu siklus Livewire --}}
        <div class="card-body p-20" wire:ignore>
            {{-- Filter & Search Header Bar --}}
            <div class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fs-8 text-muted mb-1">Filter Pelatihan:</label>
                    <select id="filter_course_id" class="form-select form-select-sm">
                        <option value="">-- Semua Pelatihan --</option>
                        @foreach ($courses as $c)
                            <option value="{{ $c->id }}">[{{ $c->code }}] {{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-8 text-muted mb-1">Filter Tanggal:</label>
                    <input type="date" id="filter_date" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label fs-8 text-muted mb-1">Status Token:</label>
                    <select id="filter_token_status" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="active">Token Sedang Aktif</option>
                        <option value="unopened">Belum Dibuka</option>
                        <option value="expired">Kedaluwarsa</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fs-8 text-muted mb-1">Cari Data Sesi:</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="ri-search-line"></i></span>
                        <input type="text" id="custom_search_dt" class="form-control form-control-sm border-start-0" placeholder="Ketik judul sesi, mentor, token...">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" id="btn-reset-filters" class="btn btn-sm btn-light border w-100" title="Reset Semua Filter">
                        <i class="ri-refresh-line"></i> Reset
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table id="tableAbsensi" class="table table-hover align-middle mb-0 w-100">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">
                                <input class="form-check-input check-data-all" type="checkbox">
                            </th>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Judul Sesi / Agenda</th>
                            <th>Pelatihan</th>
                            <th style="width: 170px;">Tanggal & Waktu</th>
                            <th>Mentor Pengampu</th>
                            <th style="width: 170px;">Status & Token Sesi</th>
                            <th style="width: 150px;">Rekap Presensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- AJAX DataTables Content --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL 1: BUKA & ATUR TOKEN SESI --}}
    <div wire:ignore.self class="modal fade" id="modalToken" tabindex="-1" aria-labelledby="modalTokenLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow radius-16 overflow-hidden">
                <div class="modal-header bg-navy text-white px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-key-2-line fs-5 text-warning"></i>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalTokenLabel">Pengaturan Token Absensi Sesi</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="text-muted fs-8 d-block mb-1">Pelatihan & Sesi:</label>
                        <h6 class="fw-bold text-dark mb-0">{{ $selectedScheduleTitle }}</h6>
                        <small class="text-muted">{{ $selectedCourseTitle }}</small>
                    </div>

                    @if ($isTokenActive && $activeToken)
                        <div class="text-center p-3 my-3 bg-light border border-success-subtle rounded-3">
                            <span class="text-muted fs-8 text-uppercase tracking-wider fw-semibold d-block mb-1">Kode Token Aktif</span>
                            <div class="display-4 fw-bold text-success my-1 font-monospace" style="letter-spacing: 6px;">{{ $activeToken }}</div>
                            <p class="text-muted fs-8 mb-2">
                                <i class="ri-time-line text-warning me-1"></i> Aktif sampai pukul <strong>{{ $activeTokenExpires }} WIB</strong>
                            </p>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success" onclick="navigator.clipboard.writeText('{{ $activeToken }}'); alert('Kode token disalin!');">
                                    <i class="ri-file-copy-line"></i> Salin Token
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="closeToken({{ $selectedScheduleId }})">
                                    <i class="ri-close-circle-line"></i> Tutup Sekarang
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary py-2 px-3 fs-8 mb-3 d-flex align-items-center gap-2">
                            <i class="ri-information-line fs-6"></i>
                            <span>Sesi token absensi saat ini belum dibuka atau telah kedaluwarsa.</span>
                        </div>
                    @endif

                    <form wire:submit.prevent="submitOpenToken">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-8">Masa Berlaku Token (Menit) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('tokenValidityMinutes') is-invalid @enderror" wire:model="tokenValidityMinutes" min="5" max="180">
                                <span class="input-group-text fs-8 text-muted">Menit</span>
                            </div>
                            <small class="text-muted fs-8">Durasi standar: 15 menit. Anda dapat mengatur rentang 5 hingga 180 menit.</small>
                            @error('tokenValidityMinutes')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-simple-gold d-inline-flex align-items-center gap-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="ri-shield-keyhole-line"></i> {{ $isTokenActive ? 'Perpanjang / Buat Baru' : 'Buka Sesi Token' }}</span>
                                <span wire:loading><i class="ri-loader-4-line ri-spin"></i> Memproses...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 2: REKAPITULASI PRESENSI & RIWAYAT HADIR PESERTA --}}
    <div wire:ignore.self class="modal fade" id="modalAttendanceSheet" tabindex="-1" aria-labelledby="modalSheetLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow radius-16 overflow-hidden">
                <div class="modal-header bg-navy text-white px-4 py-3">
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="modalSheetLabel">
                            <i class="ri-file-user-line text-warning me-1"></i> Rekapitulasi Presensi Sesi Pelatihan
                        </h6>
                        <small class="text-white-50">
                            {{ $attendanceSheet['schedule']->session_title ?? '-' }}
                        </small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    @if ($attendanceSheet)
                        {{-- Kartu Ringkasan Rekap --}}
                        <div class="row g-2 mb-3">
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-muted d-block fs-8">Total Peserta</small>
                                    <h5 class="fw-bold text-dark mb-0">{{ $attendanceSheet['total_enrolled'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-success d-block fs-8">Hadir</small>
                                    <h5 class="fw-bold text-success mb-0">{{ $attendanceSheet['hadir_count'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-warning d-block fs-8">Terlambat</small>
                                    <h5 class="fw-bold text-warning mb-0">{{ $attendanceSheet['terlambat_count'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-info d-block fs-8">Izin / Sakit</small>
                                    <h5 class="fw-bold text-info mb-0">{{ $attendanceSheet['izin_count'] + $attendanceSheet['sakit_count'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-danger d-block fs-8">Alpa</small>
                                    <h5 class="fw-bold text-danger mb-0">{{ $attendanceSheet['alpa_count'] }}</h5>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="bg-white p-2 rounded border text-center">
                                    <small class="text-secondary d-block fs-8">Belum Absen</small>
                                    <h5 class="fw-bold text-muted mb-0">{{ $attendanceSheet['belum_absen_count'] }}</h5>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Peserta --}}
                        <div class="card border-0 shadow-sm radius-12 overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;" class="text-center">No</th>
                                            <th>Nama Peserta ASN</th>
                                            <th>NIP & Instansi</th>
                                            <th>Waktu Check-in</th>
                                            <th class="text-center">Status Kehadiran</th>
                                            <th>Catatan / Alasan Koreksi</th>
                                            <th class="text-center" style="width: 120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendanceSheet['participants'] as $index => $p)
                                            <tr>
                                                <td class="text-center text-muted fs-8">{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="fw-bold text-dark d-block">{{ $p['name'] }}</span>
                                                    <small class="text-muted">{{ $p['email'] }}</small>
                                                </td>
                                                <td>
                                                    <span class="d-block text-dark">{{ $p['nip'] }}</span>
                                                    <small class="text-muted">{{ $p['agency'] }}</small>
                                                </td>
                                                <td>
                                                    <span class="text-dark fs-8">{{ $p['check_in_at'] }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if ($p['status'] === 'hadir')
                                                        <span class="badge bg-success text-white px-2 py-1"><i class="ri-checkbox-circle-line me-1"></i>Hadir</span>
                                                    @elseif ($p['status'] === 'terlambat')
                                                        <span class="badge bg-warning text-dark px-2 py-1"><i class="ri-time-line me-1"></i>Terlambat</span>
                                                    @elseif ($p['status'] === 'izin')
                                                        <span class="badge bg-info text-white px-2 py-1"><i class="ri-information-line me-1"></i>Izin</span>
                                                    @elseif ($p['status'] === 'sakit')
                                                        <span class="badge bg-secondary text-white px-2 py-1"><i class="ri-first-aid-kit-line me-1"></i>Sakit</span>
                                                    @elseif ($p['status'] === 'alpa')
                                                        <span class="badge bg-danger text-white px-2 py-1"><i class="ri-close-circle-line me-1"></i>Alpa</span>
                                                    @else
                                                        <span class="badge bg-light text-muted px-2 py-1 border">Belum Absen</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($p['is_manual_correction'])
                                                        <small class="text-dark d-block"><em>"{{ $p['correction_reason'] }}"</em></small>
                                                        <small class="text-muted" style="font-size: 11px;">Oleh: {{ $p['corrected_by_name'] ?? 'Admin/Mentor' }}</small>
                                                    @else
                                                        <span class="text-muted fs-8">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2 fs-8"
                                                        wire:click="hookModalCorrection({{ $p['user_id'] }}, '{{ addslashes($p['name']) }}', '{{ $p['status'] }}')">
                                                        <i class="ri-edit-line"></i> Koreksi
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    Belum ada peserta yang terdaftar resmi pada pelatihan ini.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL 3: KOREKSI MANUAL KEHADIRAN (ADMIN / MENTOR) --}}
    <div wire:ignore.self class="modal fade" id="modalCorrection" tabindex="-1" aria-labelledby="modalCorrectionLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow radius-16 overflow-hidden">
                <div class="modal-header bg-navy text-white px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-edit-2-line fs-5 text-warning"></i>
                        <h6 class="modal-title fw-bold mb-0 text-white" id="modalCorrectionLabel">Koreksi Manual Status Kehadiran</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="text-muted fs-8 d-block mb-1">Nama Peserta:</label>
                        <h6 class="fw-bold text-dark mb-0">{{ $correctionUserName }}</h6>
                    </div>

                    <form wire:submit.prevent="submitCorrection">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-8">Status Kehadiran <span class="text-danger">*</span></label>
                            <select class="form-select @error('correctionStatus') is-invalid @enderror" wire:model="correctionStatus">
                                <option value="hadir">Hadir</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa</option>
                            </select>
                            @error('correctionStatus')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-8">Alasan Pengubahan Status (Wajib untuk Rekam Audit) <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('correctionReason') is-invalid @enderror" wire:model="correctionReason" rows="3" placeholder="Contoh: Peserta hadir namun kendala jaringan saat input token; telah dikonfirmasi di kelas."></textarea>
                            @error('correctionReason')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-simple-gold d-inline-flex align-items-center gap-1" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="ri-check-line"></i> Simpan Koreksi</span>
                                <span wire:loading><i class="ri-loader-4-line ri-spin"></i> Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    </div>
</div>

@include('mods.admin.absensi.atc.absensi-data-atc')
