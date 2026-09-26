<div>
    {{-- Breadcrumb & Navigasi Kembali --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span
                    class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-8 fw-semibold">
                    <i class="ri-qr-code-line me-1"></i> Modul Presensi Digital
                </span>
                <h5 class="fw-bold text-dark mb-0">Kelola Absensi Sesi Pelatihan</h5>
            </div>
            <p class="text-muted mb-0">
                Kontrol pembukaan sesi presensi, token & QR code interaktif, serta monitoring daftar kehadiran peserta
                secara realtime.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-success d-flex align-items-center" wire:click="loadAttendanceSheet"
                title="Segarkan Data Presensi">
                <i class="ri-refresh-line"></i>
                <span>Refresh Data</span>
            </button>
            <a href="{{ route('absensi.data') }}" class="btn btn-danger d-flex align-items-center" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- BARIS 1: INFORMASI SESI & KONTROL ABSENSI --}}
    <div class="row g-3 mb-24">
        {{-- 1. KARTU INFORMASI SESI --}}
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm radius-16 h-100 bg-white">
                <div
                    class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary"
                            style="width: 36px; height: 36px;">
                            <i class="ri-information-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Informasi Sesi Pelatihan</h6>
                            <small class="text-muted">Detail jadwal dan lokasi sesi diklat</small>
                        </div>
                    </div>
                    <div>
                        {!! $schedule->getAttendanceStatusBadge() !!}
                    </div>
                </div>
                <div class="card-body p-20">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0 fs-8">
                            <tbody>
                                <tr>
                                    <td class="text-muted text-nowrap py-2" style="width: 130px;">Pelatihan:</td>
                                    <td class="py-2">
                                        @if (!empty($schedule->course?->code))
                                            <span
                                                class="badge bg-light text-secondary border me-1">{{ $schedule->course->code }}</span>
                                        @endif
                                        <span
                                            class="fw-bold text-dark fs-7">{{ $schedule->course?->title ?? '-' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap py-2">Judul Sesi:</td>
                                    <td class="fw-semibold text-dark py-2 fs-7">{{ $schedule->session_title }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap py-2">Mentor:</td>
                                    <td class="py-2">
                                        <span class="fw-semibold text-dark">{{ $schedule->mentor?->name ?? '-' }}</span>
                                        @if (!empty($schedule->mentor?->nip))
                                            <small class="text-muted d-block">NIP: {{ $schedule->mentor->nip }}</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap py-2">Ruangan / Tautan:</td>
                                    <td class="py-2">
                                        @if ($schedule->isOnline())
                                            <a href="{{ $schedule->room_or_link }}" target="_blank"
                                                class="text-primary text-break fw-semibold d-inline-flex align-items-center gap-1">
                                                <i class="ri-external-link-line"></i> {{ $schedule->room_or_link }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1">
                                                <i
                                                    class="ri-map-pin-line text-danger me-1"></i>{{ $schedule->room_or_link }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-nowrap py-2">Waktu Sesi:</td>
                                    <td class="py-2">
                                        <div class="fw-semibold text-dark">
                                            <i class="ri-calendar-line text-muted me-1"></i>
                                            {{ $schedule->session_date ? $schedule->session_date->format('d/m/Y') : '-' }}
                                        </div>
                                        <small class="text-muted">
                                            <i class="ri-time-line me-1"></i>
                                            {{ substr($schedule->start_time, 0, 5) }} -
                                            {{ substr($schedule->end_time, 0, 5) }} WIB
                                        </small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. KARTU KONTROL ABSENSI --}}
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm radius-16 h-100 bg-white">
                <div
                    class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning"
                            style="width: 36px; height: 36px;">
                            <i class="ri-shield-keyhole-line fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 fs-6">Kontrol Absensi & Token/QR</h6>
                            <small class="text-muted">Pengaturan masa aktif presensi digital peserta</small>
                        </div>
                    </div>
                    <div>
                        @if ($isTokenActive && $activeToken)
                            <span class="badge bg-success text-white px-2 py-1">
                                <i class="ri-broadcast-line me-1"></i>Sedang Dibuka
                            </span>
                        @else
                            <span class="badge bg-secondary text-white px-2 py-1">
                                <i class="ri-lock-line me-1"></i>Ditutup / Belum Dibuka
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-20">
                    <input type="hidden" id="current_active_token" value="{{ $activeToken ?? '' }}">

                    @if ($isTokenActive && $activeToken)
                        {{-- STATUS AKTIF: TOKEN & QR CODE --}}
                        <div class="row align-items-center g-3">
                            <div class="col-sm-5 text-center">
                                <div class="p-2 border rounded-3 bg-white d-inline-block shadow-sm">
                                    <div id="attendance_qr_canvas"
                                        class="d-flex align-items-center justify-content-center"
                                        style="width: 140px; height: 140px;"></div>
                                </div>
                                <div class="mt-2">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary py-1 px-3 fs-8 rounded-pill"
                                        onclick="openEnlargedQR('{{ $activeToken }}', '{{ addslashes($schedule->session_title) }}')">
                                        <i class="ri-fullscreen-line me-1"></i> Mode Proyektor Layar Penuh
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-7">
                                <small class="text-muted fs-8 text-uppercase tracking-wider fw-bold d-block">Kode Token
                                    6 Digit:</small>
                                <div class="display-5 fw-bold text-success font-monospace my-1"
                                    style="letter-spacing: 5px;">{{ $activeToken }}</div>

                                <div class="d-flex flex-column gap-1 text-muted fs-8 my-2">
                                    <div><i class="ri-time-line text-primary me-1"></i>Waktu Buka:
                                        <strong>{{ $schedule->token_opened_at?->format('H:i') ?? '-' }} WIB</strong>
                                    </div>
                                    <div><i class="ri-alarm-warning-line text-danger me-1"></i>Kedaluwarsa:
                                        <strong>{{ $schedule->token_expires_at?->format('H:i') ?? '-' }} WIB</strong>
                                    </div>
                                    <div><i class="ri-hourglass-line text-warning me-1"></i>Batas Terlambat:
                                        <strong>{{ $schedule->late_threshold_minutes ?? 15 }} Menit</strong>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap gap-2 pt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-3 fs-8"
                                        onclick="navigator.clipboard.writeText('{{ $activeToken }}'); Livewire.dispatch('alert-show', { data: { type: 'success', message: 'Token absensi disalin ke clipboard.' } });">
                                        <i class="ri-file-copy-line"></i> Salin Token
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-danger py-1 px-3 fs-8 d-inline-flex align-items-center gap-1"
                                        wire:click="closeToken" wire:loading.attr="disabled">
                                        <span wire:loading.remove><i class="ri-close-circle-line"></i> Tutup
                                            Absensi</span>
                                        <span wire:loading><i class="ri-loader-4-line ri-spin"></i> Menutup...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- STATUS BELUM DIBUKA / DITUTUP: FORM BUKA ABSENSI --}}
                        <div class="alert alert-secondary py-2 px-3 fs-8 mb-3 d-flex align-items-center gap-2">
                            <i class="ri-information-line fs-5 text-muted"></i>
                            <span>Presensi sesi ini belum dibuka atau sudah ditutup. Masukkan durasi token di bawah
                                untuk membuka sesi absensi.</span>
                        </div>

                        <form wire:submit.prevent="submitOpenToken">
                            <div class="row g-2 mb-3">
                                <div class="col-sm-6">
                                    <label class="form-label fs-8 text-muted mb-1">Durasi Token (Menit): <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control @error('tokenValidityMinutes') is-invalid @enderror"
                                            wire:model="tokenValidityMinutes" min="5" max="180">
                                        <span class="input-group-text text-muted">Menit</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Default: 15 menit (rentang
                                        5-180 mnt)</small>
                                    @error('tokenValidityMinutes')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label fs-8 text-muted mb-1">Batas Keterlambatan: <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-sm">
                                        <input type="number"
                                            class="form-control @error('lateThresholdMinutes') is-invalid @enderror"
                                            wire:model="lateThresholdMinutes" min="1" max="180">
                                        <span class="input-group-text text-muted">Menit</span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;">Setelah lewat dihitung
                                        terlambat</small>
                                    @error('lateThresholdMinutes')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-simple-gold w-100 d-inline-flex align-items-center justify-content-center gap-1 py-2 fw-semibold radius-8"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="ri-broadcast-line"></i> Buka Absensi & Generate
                                    Token/QR</span>
                                <span wire:loading><i class="ri-loader-4-line ri-spin"></i> Membuka Sesi...</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- BARIS 2: KARTU RINGKASAN REKAPITULASI KEHADIRAN --}}
    <div class="row g-3 mb-24">
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-muted d-block fs-8 mb-1">Total Peserta</small>
                <h4 class="fw-bold text-dark mb-0">{{ $attendanceSheet['total_enrolled'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-success d-block fs-8 mb-1">Hadir</small>
                <h4 class="fw-bold text-success mb-0">{{ $attendanceSheet['hadir_count'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-warning d-block fs-8 mb-1">Terlambat</small>
                <h4 class="fw-bold text-warning mb-0">{{ $attendanceSheet['terlambat_count'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-info d-block fs-8 mb-1">Izin / Sakit</small>
                <h4 class="fw-bold text-info mb-0">
                    {{ ($attendanceSheet['izin_count'] ?? 0) + ($attendanceSheet['sakit_count'] ?? 0) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-danger d-block fs-8 mb-1">Alpa</small>
                <h4 class="fw-bold text-danger mb-0">{{ $attendanceSheet['alpa_count'] ?? 0 }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-2">
            <div class="card border-0 shadow-sm radius-12 p-3 bg-white text-center">
                <small class="text-secondary d-block fs-8 mb-1">Belum Absen</small>
                <h4 class="fw-bold text-muted mb-0">{{ $attendanceSheet['belum_absen_count'] ?? 0 }}</h4>
            </div>
        </div>
    </div>

    {{-- BARIS 3: DAFTAR KEHADIRAN PESERTA --}}
    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-24">
        <div
            class="card-header bg-white py-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success"
                    style="width: 36px; height: 36px;">
                    <i class="ri-user-follow-line fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Daftar Kehadiran Peserta Terverifikasi</h6>
                    <small class="text-muted">Daftar presensi seluruh peserta yang berhak mengikuti sesi diklat</small>
                </div>
            </div>
            <div>
                <span class="badge bg-light text-dark border px-3 py-2 fs-7">
                    Persentase Hadir: <strong
                        class="text-primary">{{ $attendanceSheet['attendance_percentage'] ?? 0 }}%</strong>
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light fs-8">
                        <tr>
                            <th style="width: 50px;" class="text-center">No</th>
                            <th>Nama Peserta ASN</th>
                            <th>NIP & Instansi</th>
                            <th style="width: 150px;">Waktu Presensi</th>
                            <th class="text-center" style="width: 130px;">Status</th>
                            <th class="text-center" style="width: 130px;">Metode</th>
                            <th>Catatan / Alasan Koreksi</th>
                            <th class="text-center" style="width: 110px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fs-8">
                        @if (!empty($attendanceSheet['participants']))
                            @forelse ($attendanceSheet['participants'] as $index => $p)
                                <tr>
                                    <td class="text-center text-muted fw-medium">{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-bold text-dark d-block fs-7">{{ $p['name'] }}</span>
                                        <small class="text-muted">{{ $p['email'] }}</small>
                                    </td>
                                    <td>
                                        <span class="d-block text-dark fw-medium">{{ $p['nip'] }}</span>
                                        <small class="text-muted">{{ $p['agency'] }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $p['check_in_at'] }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($p['status'] === 'hadir')
                                            <span class="badge bg-success text-white px-2 py-1"><i
                                                    class="ri-checkbox-circle-line me-1"></i>Hadir</span>
                                        @elseif ($p['status'] === 'terlambat')
                                            <span class="badge bg-warning text-dark px-2 py-1"><i
                                                    class="ri-time-line me-1"></i>Terlambat</span>
                                        @elseif ($p['status'] === 'izin')
                                            <span class="badge bg-info text-white px-2 py-1"><i
                                                    class="ri-information-line me-1"></i>Izin</span>
                                        @elseif ($p['status'] === 'sakit')
                                            <span class="badge bg-secondary text-white px-2 py-1"><i
                                                    class="ri-first-aid-kit-line me-1"></i>Sakit</span>
                                        @elseif ($p['status'] === 'alpa')
                                            <span class="badge bg-danger text-white px-2 py-1"><i
                                                    class="ri-close-circle-line me-1"></i>Alpa</span>
                                        @else
                                            <span class="badge bg-light text-muted px-2 py-1 border">Belum Absen</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($p['status'] !== 'belum_absen')
                                            {!! $p['method_badge'] !!}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($p['is_manual_correction'])
                                            <small
                                                class="text-dark d-block fw-semibold"><em>"{{ $p['correction_reason'] }}"</em></small>
                                            <small class="text-muted" style="font-size: 11px;">Oleh:
                                                {{ $p['corrected_by_name'] ?? 'Admin/Mentor' }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-warning py-1 px-2 fs-8 rounded"
                                            wire:click="hookModalCorrection({{ $p['user_id'] }}, '{{ addslashes($p['name']) }}', '{{ $p['status'] }}')"
                                            title="Ubah Status Kehadiran (Koreksi Manual)">
                                            <i class="ri-edit-line"></i> Koreksi
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        Belum ada peserta yang terdaftar resmi pada pelatihan ini.
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Memuat daftar peserta...
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL LAYAR PENUH (PROYEKTOR) QR CODE & TOKEN --}}
    <div class="modal fade" id="modalEnlargeQR" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg radius-16 text-center p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="ri-qr-code-line text-primary me-1"></i> Pindai
                        Presensi Kehadiran</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="enlarged_qr_title" class="text-muted fs-8 mb-2">{{ $schedule->session_title }}</div>
                <div class="d-flex justify-content-center p-3 bg-white rounded border my-2">
                    <div id="enlarged_qr_canvas"></div>
                </div>
                <div class="my-2">
                    <small class="text-muted d-block fs-8 text-uppercase">Atau Masukkan Kode Token 6 Digit:</small>
                    <div id="enlarged_qr_token" class="display-4 fw-bold text-success font-monospace"
                        style="letter-spacing: 6px;">{{ $activeToken }}</div>
                    <div class="text-primary fs-8 mt-2">
                        <i class="ri-links-line me-1"></i> Tautan Presensi: <code id="enlarged_qr_url"
                            class="text-primary bg-light px-2 py-1 rounded"></code>
                    </div>
                </div>
                <p class="text-muted fs-8 mb-0">Peserta dapat memindai QR Code di layar proyektor ini dengan kamera
                    ponsel atau membuka tautan di atas.</p>
            </div>
        </div>
    </div>

    {{-- MODAL KOREKSI MANUAL KEHADIRAN --}}
    <div wire:ignore.self class="modal fade" id="modalCorrection" tabindex="-1"
        aria-labelledby="modalCorrectionLabel" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow radius-16 overflow-hidden">
                <div class="modal-header bg-navy text-white px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-edit-circle-line fs-5 text-warning"></i>
                        <h6 class="modal-title fw-bold text-white mb-0" id="modalCorrectionLabel">Koreksi Manual
                            Kehadiran Peserta</h6>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="submitCorrection">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="text-muted fs-8 d-block mb-1">Nama Peserta ASN:</label>
                            <h6 class="fw-bold text-dark mb-0">{{ $correctionUserName }}</h6>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-8">Status Kehadiran Baru <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('correctionStatus') is-invalid @enderror"
                                wire:model="correctionStatus">
                                <option value="hadir">Hadir (Tepat Waktu)</option>
                                <option value="terlambat">Terlambat</option>
                                <option value="izin">Izin Resmi</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpa">Alpa (Tanpa Keterangan)</option>
                            </select>
                            @error('correctionStatus')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark fs-8">Alasan / Catatan Koreksi <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('correctionReason') is-invalid @enderror" wire:model="correctionReason"
                                rows="3" placeholder="Tuliskan alasan koreksi kehadiran (wajib diisi untuk audit pelaporan)..."></textarea>
                            <small class="text-muted fs-8">Alasan koreksi dicatat dalam rekam jejak audit dan dapat
                                dilihat oleh Pimpinan.</small>
                            @error('correctionReason')
                                <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit"
                            class="btn btn-warning text-dark fw-semibold d-inline-flex align-items-center gap-1"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="ri-save-line"></i> Simpan Koreksi</span>
                            <span wire:loading><i class="ri-loader-4-line ri-spin"></i> Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js-stack')
    <script src="{{ asset('admin/assets/js/lib/qrcode.min.js') }}"></script>
    <script>
        function getCheckInUrl(token) {
            var baseUrl = "{{ url('/presensi') }}";
            return baseUrl + "?token=" + encodeURIComponent(token);
        }

        function renderAttendanceQRCode(token) {
            var qrContainer = document.getElementById('attendance_qr_canvas');
            if (!qrContainer) return;
            qrContainer.innerHTML = '';
            if (!token) return;

            var targetUrl = getCheckInUrl(token);

            if (typeof QRCode !== 'undefined') {
                new QRCode(qrContainer, {
                    text: targetUrl,
                    width: 130,
                    height: 130,
                    colorDark: "#102a43",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        }

        window.openEnlargedQR = function(token, title) {
            $('#enlarged_qr_title').text(title || 'Sesi Diklat');
            $('#enlarged_qr_token').text(token);
            var targetUrl = getCheckInUrl(token);
            $('#enlarged_qr_url').text(targetUrl);
            var canvasEl = document.getElementById('enlarged_qr_canvas');
            if (canvasEl) {
                canvasEl.innerHTML = '';
                if (typeof QRCode !== 'undefined') {
                    new QRCode(canvasEl, {
                        text: targetUrl,
                        width: 250,
                        height: 250,
                        colorDark: "#102a43",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.M
                    });
                }
            }
            var modalEl = document.getElementById('modalEnlargeQR');
            if (modalEl && typeof bootstrap !== 'undefined') {
                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        };

        function initKelolaAbsensi() {
            let token = $('#current_active_token').val();
            if (token) {
                renderAttendanceQRCode(token);
            }
        }

        document.addEventListener('DOMContentLoaded', initKelolaAbsensi);
        document.addEventListener('livewire:navigated', initKelolaAbsensi);

        document.addEventListener('livewire:init', () => {
            Livewire.on('render-qr', (event) => {
                let data = Array.isArray(event) ? event[0] : event;
                let token = typeof data === 'object' && data !== null && data.token ? data.token : data;
                setTimeout(() => {
                    renderAttendanceQRCode(token);
                }, 150);
            });

            Livewire.on('open-modal-correction', () => {
                var modalEl = document.getElementById('modalCorrection');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('close-modal-correction', () => {
                var modalEl = document.getElementById('modalCorrection');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modalInstance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal
                        .getOrCreateInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });
        });
    </script>
@endpush
