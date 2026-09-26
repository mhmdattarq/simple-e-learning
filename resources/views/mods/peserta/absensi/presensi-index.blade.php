<div>
    {{-- Page Header Section --}}
    <section class="page-header py-4" style="background: linear-gradient(135deg, #071a33 0%, #102a43 100%);">
        <div class="container">
            <div class="page-header__inner d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <span class="badge px-3 py-2 text-white border border-light-subtle rounded-pill mb-2 fs-8"
                        style="background: rgba(255, 255, 255, 0.1);">
                        <i class="ri-qr-code-line text-warning me-1"></i> Modul Presensi Peserta
                    </span>
                    <h2 class="text-white fw-bold mb-1 fs-3">Presensi Elektronik Pelatihan ASN</h2>
                    <p class="text-white-50 mb-0 fs-7">
                        Sistem Informasi Manajemen Pembelajaran Elektronik (SIMPEL) BKPSDM Aceh Timur
                    </p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('pelatihan.index') }}" class="btn btn-outline-light btn-sm radius-8 px-3 py-2 fs-8">
                        <i class="ri-book-read-line me-1"></i> Katalog Pelatihan
                    </a>
                    <a href="{{ route('landing') }}" class="btn btn-warning btn-sm text-dark fw-bold radius-8 px-3 py-2 fs-8">
                        <i class="ri-home-4-line me-1"></i> Beranda
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-5" style="background: #f8fafc; min-height: 80vh;">
        <div class="container">
            <div class="row g-4">
                {{-- KOLOM KIRI: FORM PRESENSI TOKEN / QR --}}
                <div class="col-lg-7 col-12">
                    {{-- 1. Kartu Input Token --}}
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4">
                        <div class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary"
                                    style="width: 36px; height: 36px;">
                                    <i class="ri-key-2-line fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0 fs-6">Pencatatan Kehadiran Sesi</h6>
                                    <small class="text-muted">Masukkan token 6 digit atau gunakan tautan QR Code</small>
                                </div>
                            </div>
                            <div>
                                @if($method === 'qr')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-8 rounded-pill">
                                        <i class="ri-qr-scan-2-line me-1"></i> Scan QR Code Terdeteksi
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 fs-8 rounded-pill">
                                        <i class="ri-keyboard-line me-1"></i> Input Token Manual
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body p-24">
                            {{-- Notifikasi Sukses Baru Saja Check-in --}}
                            @if($lastCheckInResult)
                                <div class="alert alert-success border-0 shadow-sm radius-12 p-20 mb-24 d-flex align-items-start gap-3">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 44px; height: 44px;">
                                        <i class="ri-checkbox-circle-fill fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold text-success mb-1 fs-6">Presensi Berhasil Dicatat!</h6>
                                        <p class="mb-2 text-dark fs-8">{{ $lastCheckInResult['message'] }}</p>
                                        <div class="d-flex flex-wrap align-items-center gap-3 fs-8 text-muted pt-1 border-top">
                                            <span><i class="ri-time-line text-primary me-1"></i><strong>Waktu:</strong> {{ $lastCheckInResult['check_in_at'] }} WIB</span>
                                            <span><i class="ri-shield-check-line text-success me-1"></i><strong>Status:</strong> <span class="badge bg-success">{{ $lastCheckInResult['status_label'] }}</span></span>
                                            <span><i class="ri-qr-code-line text-info me-1"></i><strong>Metode:</strong> {{ $lastCheckInResult['method'] }}</span>
                                        </div>
                                        @if($previewCourseId)
                                            <div class="mt-3 pt-2 border-top">
                                                <a href="{{ route('peserta.materi', $previewCourseId) }}" class="btn btn-success btn-sm fw-bold radius-8 text-white px-3 py-2 fs-8 d-inline-flex align-items-center gap-1 shadow-sm">
                                                    <i class="ri-book-open-line"></i>
                                                    <span>Masuk ke Halaman Materi</span>
                                                    <i class="ri-arrow-right-line"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- Pesan Error Validasi Ramah --}}
                            @if($errorMessage)
                                <div class="alert alert-danger border-0 shadow-sm radius-12 p-16 mb-20 d-flex align-items-start gap-2">
                                    <i class="ri-error-warning-fill fs-5 text-danger flex-shrink-0 mt-1"></i>
                                    <div class="fs-8">
                                        <strong class="d-block mb-1">Gagal Mencatat Kehadiran:</strong>
                                        <span>{{ $errorMessage }}</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Form Input Token 6 Digit --}}
                            <form wire:submit.prevent="submitPresensi">
                                <div class="mb-24 text-center">
                                    <label class="form-label fw-bold text-dark fs-7 d-block mb-2">
                                        Kode Token 6 Digit
                                    </label>
                                    <div class="d-flex justify-content-center mb-2">
                                        <input type="text"
                                            class="form-control form-control-lg text-center fw-bold shadow-sm @error('token') is-invalid @enderror"
                                            wire:model.live.debounce.300ms="token"
                                            placeholder="······"
                                            maxlength="6"
                                            autocomplete="off"
                                            style="font-family: 'Courier New', Courier, monospace; letter-spacing: 12px; font-size: 32px; max-width: 280px; border-radius: 12px; border: 2px solid #0d6efd;">
                                    </div>
                                    <small class="text-muted fs-8">
                                        Kode token ditampilkan oleh Mentor atau Administrator pada layar presentasi/proyektor di depan kelas.
                                    </small>
                                    @error('token')
                                        <div class="text-danger fs-8 mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- PREVIEW SESI SAAT TOKEN DITEMUKAN --}}
                                @if($previewScheduleId)
                                    <div class="card border border-primary border-opacity-25 bg-primary bg-opacity-10 radius-12 p-20 mb-24">
                                        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                            <span class="badge bg-primary px-3 py-1 fs-8">
                                                <i class="ri-live-line me-1"></i> Sesi Aktif Ditemukan
                                            </span>
                                            <span class="badge bg-warning text-dark border px-2 py-1 fs-8">
                                                <i class="ri-timer-line me-1"></i> Berlaku s.d {{ $previewExpiresAt }} WIB
                                            </span>
                                        </div>

                                        <h6 class="fw-bold text-dark mb-1 fs-6">{{ $previewSessionTitle }}</h6>
                                        <p class="text-muted fs-8 mb-3">{{ $previewCourseTitle }}</p>

                                        <div class="row g-2 fs-8 text-secondary mb-3">
                                            <div class="col-sm-6">
                                                <i class="ri-user-star-line text-primary me-1"></i><strong>Mentor:</strong> {{ $previewMentorName }}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="ri-map-pin-line text-danger me-1"></i><strong>Ruangan/Link:</strong> {{ $previewRoomOrLink }}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="ri-calendar-line text-info me-1"></i><strong>Tanggal:</strong> {{ $previewDate }}
                                            </div>
                                            <div class="col-sm-6">
                                                <i class="ri-time-line text-success me-1"></i><strong>Waktu Sesi:</strong> {{ $previewTime }}
                                            </div>
                                        </div>

                                        {{-- Indikator Otorisasi Peserta --}}
                                        @if(!$isVerifiedForCourse)
                                            <div class="alert alert-warning border-0 p-2 mb-0 fs-8 d-flex align-items-center gap-2">
                                                <i class="ri-alert-line text-warning fs-6"></i>
                                                <span>Anda belum terdaftar atau belum diverifikasi resmi sebagai peserta pada diklat ini.</span>
                                            </div>
                                        @elseif($existingAttendance)
                                            <div class="alert alert-info border-0 p-2 mb-0 fs-8 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="ri-information-line fs-6 text-info"></i>
                                                    <span>Anda telah melakukan presensi pada sesi ini. Status: <strong>{{ ucfirst($existingAttendance->status) }}</strong></span>
                                                </div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-info text-white">{{ $existingAttendance->check_in_at?->format('H:i') ?? '-' }} WIB</span>
                                                    @if($previewCourseId)
                                                        <a href="{{ route('peserta.materi', $previewCourseId) }}" class="btn btn-sm btn-primary fw-bold text-white radius-8 px-2 py-1 fs-8 d-inline-flex align-items-center gap-1">
                                                            <i class="ri-book-open-line"></i> Buka Materi
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-success border-0 p-2 mb-0 fs-8 d-flex align-items-center gap-2">
                                                <i class="ri-checkbox-circle-line text-success fs-6"></i>
                                                <span>Peserta terverifikasi. Silakan klik tombol di bawah untuk mencatat kehadiran.</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Tombol Submit Presensi / Masuk Materi --}}
                                <div class="d-grid">
                                    @if($existingAttendance && $previewCourseId)
                                        <a href="{{ route('peserta.materi', $previewCourseId) }}"
                                            class="btn btn-primary btn-lg fw-bold radius-12 d-flex align-items-center justify-content-center gap-2 py-3 shadow-sm text-decoration-none">
                                            <i class="ri-book-open-line fs-5"></i>
                                            <span>Masuk ke Materi Pembelajaran</span>
                                            <i class="ri-arrow-right-line fs-5"></i>
                                        </a>
                                    @else
                                        <button type="submit"
                                            class="btn btn-primary btn-lg fw-bold radius-12 d-flex align-items-center justify-content-center gap-2 py-3"
                                            @if(!$previewScheduleId || !$isVerifiedForCourse || $existingAttendance) disabled @endif
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove>
                                                <i class="ri-send-plane-fill fs-5"></i>
                                                @if($existingAttendance)
                                                    Sudah Melakukan Presensi
                                                @elseif(!$previewScheduleId)
                                                    Masukkan Token 6 Digit di Atas
                                                @elseif(!$isVerifiedForCourse)
                                                    Bukan Peserta Terverifikasi
                                                @else
                                                    Kirim Presensi Kehadiran Sekarang
                                                @endif
                                            </span>
                                            <span wire:loading>
                                                <i class="ri-loader-4-line ri-spin fs-5"></i> Memproses Presensi...
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Panduan Alur Presensi --}}
                    <div class="card border-0 shadow-sm radius-16 bg-white p-20">
                        <h6 class="fw-bold text-dark fs-7 mb-3">
                            <i class="ri-questionnaire-line text-primary me-1"></i> Petunjuk Presensi Elektronik
                        </h6>
                        <div class="row g-3 fs-8 text-muted">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light h-100">
                                    <strong class="d-block text-dark mb-1">1. Dapatkan Token / QR</strong>
                                    Perhatikan layar proyektor atau papan informasi saat Mentor/Admin membuka sesi absensi.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light h-100">
                                    <strong class="d-block text-dark mb-1">2. Waktu Berlaku Token</strong>
                                    Token aktif selama durasi yang ditentukan (misal 15-30 menit). Hadir setelah batas toleransi akan dicatat Terlambat.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-3 bg-light h-100">
                                    <strong class="d-block text-dark mb-1">3. Konfirmasi Tercatat</strong>
                                    Sistem hanya mengizinkan 1 kali check-in per sesi untuk setiap ASN yang telah terverifikasi.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: PROFIL & DAFTAR SESI PELATIHAN PESERTA --}}
                <div class="col-lg-5 col-12">
                    {{-- 1. Kartu Ringkasan Akun ASN --}}
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4">
                        <div class="p-20 border-bottom d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                style="width: 48px; height: 48px; background: linear-gradient(135deg, #0d6efd, #0b5ed7); font-size: 18px;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="fw-bold text-dark mb-0 text-truncate fs-7">{{ auth()->user()->name }}</h6>
                                <div class="d-flex align-items-center gap-2 flex-wrap fs-8 text-muted mt-1">
                                    <span>NIP: <strong>{{ auth()->user()->nip ?? '-' }}</strong></span>
                                    <span>•</span>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">
                                        {{ auth()->user()->role?->label() ?? 'Peserta' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="px-20 py-2 bg-light d-flex align-items-center justify-content-between fs-8 text-muted">
                            <span>Instansi / OPD:</span>
                            <span class="fw-semibold text-dark text-truncate" style="max-width: 200px;">
                                {{ auth()->user()->agency ?? 'Pemerintah Kabupaten Aceh Timur' }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Jadwal Sesi Pelatihan Terdaftar Anda --}}
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden mb-4">
                        <div class="card-header bg-white py-14 px-20 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-dark mb-0 fs-7">
                                <i class="ri-calendar-event-line text-primary me-1"></i> Sesi Pelatihan Anda
                            </h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-8">
                                Terverifikasi
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @forelse($todaySchedules as $sch)
                                <div class="p-3 border-bottom d-flex align-items-start justify-content-between gap-2 {{ $sch->isAttendanceActive() ? 'bg-warning bg-opacity-10' : '' }}">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            @if($sch->isAttendanceActive())
                                                <span class="badge bg-success animate-pulse px-2 py-1 fs-8">
                                                    <i class="ri-broadcast-line me-1"></i> Sedang Dibuka
                                                </span>
                                            @elseif($sch->is_attendance_open)
                                                <span class="badge bg-danger px-2 py-1 fs-8">Kedaluwarsa</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1 fs-8">Belum Dibuka</span>
                                            @endif
                                            <span class="text-muted fs-8">{{ $sch->session_date?->format('d/m/Y') }}</span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0 fs-8">{{ $sch->session_title }}</h6>
                                        <small class="text-muted d-block">{{ $sch->course?->title ?? '-' }}</small>
                                        <div class="d-flex align-items-center gap-3 fs-8 text-secondary mt-1">
                                            <span><i class="ri-time-line me-1"></i>{{ substr($sch->start_time ?? '', 0, 5) }} - {{ substr($sch->end_time ?? '', 0, 5) }} WIB</span>
                                            <span><i class="ri-map-pin-line me-1"></i>{{ $sch->room_or_link ?? '-' }}</span>
                                        </div>
                                    </div>
                                    @if($sch->isAttendanceActive() && $sch->attendance_token)
                                        <button type="button" class="btn btn-sm btn-outline-primary radius-8 fs-8 flex-shrink-0"
                                            wire:click="fillToken('{{ $sch->attendance_token }}')">
                                            Isi Token
                                        </button>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted fs-8">
                                    <i class="ri-calendar-todo-line fs-2 text-secondary mb-2 d-block"></i>
                                    Belum ada jadwal sesi pelatihan untuk diklat yang Anda ikuti.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- 3. Riwayat Kehadiran Anda --}}
                    <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden">
                        <div class="card-header bg-white py-14 px-20 border-bottom d-flex align-items-center justify-content-between">
                            <h6 class="fw-bold text-dark mb-0 fs-7">
                                <i class="ri-history-line text-primary me-1"></i> Riwayat Presensi Anda
                            </h6>
                            <small class="text-muted fs-8">Catatan Resmi</small>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0 fs-8">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="py-2 px-3">Sesi / Diklat</th>
                                            <th class="py-2 px-3">Waktu</th>
                                            <th class="py-2 px-3 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentAttendances as $att)
                                            <tr>
                                                <td class="py-2 px-3">
                                                    <span class="fw-semibold text-dark d-block text-truncate" style="max-width: 170px;">
                                                        {{ $att->schedule?->session_title ?? 'Sesi Diklat' }}
                                                    </span>
                                                    <small class="text-muted d-block text-truncate" style="max-width: 170px;">
                                                        {{ $att->schedule?->course?->title ?? '-' }}
                                                    </small>
                                                </td>
                                                <td class="py-2 px-3 text-nowrap text-muted">
                                                    {{ $att->check_in_at?->format('H:i') ?? '-' }} WIB
                                                    <small class="d-block">{{ $att->check_in_at?->format('d/m/Y') ?? '' }}</small>
                                                </td>
                                                <td class="py-2 px-3 text-center">
                                                    @if($att->status === 'hadir')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Hadir</span>
                                                    @elseif($att->status === 'terlambat')
                                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">Terlambat</span>
                                                    @elseif($att->status === 'izin')
                                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Izin</span>
                                                    @elseif($att->status === 'sakit')
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Sakit</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Alpa</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4 text-muted">
                                                    Belum ada riwayat presensi yang tercatat.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
