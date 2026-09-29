<div>
    {{-- Top Header / Breadcrumbs --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <span class="text-uppercase fw-bold text-xs" style="color: #b37a05; letter-spacing: 1.5px;">Sistem
                Manajemen Pelatihan</span>
            <h4 class="fw-bold mb-0 text-dark">
                Beranda Administrator
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="simpel-badge simpel-badge-navy">
                <i class="ri-calendar-line text-sm"></i>
                T.A. 2026
            </span>
            <span class="simpel-badge simpel-badge-success">
                <i class="ri-shield-check-line text-sm"></i>
                Sistem Aktif
            </span>
        </div>
    </div>

    {{-- Hero Banner (matching prototype.html flex layout & proportions) --}}
    <div class="simpel-hero mb-20">
        <div class="simpel-hero-body">
            <div class="simpel-eyebrow mb-1" style="color: #f6ce70;">
                Selamat Datang Kembali
            </div>
            <h2 class="simpel-hero-title">
                Kelola seluruh siklus pelatihan dalam satu sistem
            </h2>
            <p class="simpel-hero-desc">
                Data terintegrasi, real time, transparan, dan akuntabel — BKPSDM Kabupaten Aceh Timur.
            </p>
        </div>
        @if(auth()->user()?->isAdmin())
            <button type="button" class="btn-simpel-gold" data-bs-toggle="modal" data-bs-target="#modalRencanaPelatihan">
                <i class="ri-add-line"></i>
                Buat Rencana Pelatihan
            </button>
        @endif
    </div>

    {{-- 8-Stage Flow / Siklus Pelatihan --}}
    <div class="mb-24">
        <div class="flow-grid">
            <div class="flow-card done">
                <div class="flow-n">
                    <i class="ri-checkbox-circle-fill text-sm"></i>
                </div>
                <span>1. Kelola Kelas</span>
            </div>
            <div class="flow-card done">
                <div class="flow-n">
                    <i class="ri-checkbox-circle-fill text-sm"></i>
                </div>
                <span>2. Pendaftaran</span>
            </div>
            <div class="flow-card current">
                <div class="flow-n">3</div>
                <span>3. Verifikasi</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">4</div>
                <span>4. Penjadwalan</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">5</div>
                <span>5. Absensi</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">6</div>
                <span>6. Materi</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">7</div>
                <span>7. Evaluasi</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">8</div>
                <span>8. Sertifikat</span>
            </div>
        </div>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
        {{-- Stat 1: Pelatihan Aktif --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Pelatihan Aktif</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #fff4d8; color: #9a6700;">
                        <i class="ri-award-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">18</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-arrow-up-line"></i>
                    <span>2 program baru bulan ini</span>
                </div>
            </div>
        </div>

        {{-- Stat 2: Peserta Terdaftar --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Peserta Terdaftar</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #def4e9; color: #16845b;">
                        <i class="ri-team-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">625</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-arrow-up-line"></i>
                    <span>12.4% dari bulan lalu</span>
                </div>
            </div>
        </div>

        {{-- Stat 3: Jadwal Hari Ini --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Jadwal Hari Ini</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #e9eff7; color: #0c3158;">
                        <i class="ri-calendar-event-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">6</h3>
                <div class="d-flex align-items-center gap-1 text-primary-light"
                    style="font-size: 12px; font-weight: 600;">
                    <i class="ri-time-line"></i>
                    <span>3 sesi sedang berlangsung</span>
                </div>
            </div>
        </div>

        {{-- Stat 4: Sertifikat Terbit --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Sertifikat Terbit</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #fff2d4; color: #b37a05;">
                        <i class="ri-medal-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">312</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-qr-code-line"></i>
                    <span>100% dapat diverifikasi QR</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid: Grafik Tren & Agenda Terdekat --}}
    <div class="row g-3 mb-24">
        {{-- Grafik Tren --}}
        <div class="col-lg-8">
            <div class="simpel-card p-24 h-100">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-16">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 fs-6">Tren Peserta Terverifikasi</h5>
                        <p class="text-muted mb-0 small">Perkembangan peserta diklat terverifikasi Januari – Juni
                            2026</p>
                    </div>
                    <select class="form-select form-select-sm w-auto border"
                        style="font-size: 12px; border-radius: 8px;">
                        <option selected>Semester I (Jan - Jun 2026)</option>
                        <option>Tahun 2025</option>
                    </select>
                </div>

                {{-- CSS Interactive Bar Chart --}}
                <div class="simpel-chart">
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 34%;" title="Januari: 34 Peserta"></div>
                        <span>Jan</span>
                    </div>
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 46%;" title="Februari: 46 Peserta"></div>
                        <span>Feb</span>
                    </div>
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 55%;" title="Maret: 55 Peserta"></div>
                        <span>Mar</span>
                    </div>
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 69%;" title="April: 69 Peserta"></div>
                        <span>Apr</span>
                    </div>
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 83%;" title="Mei: 83 Peserta"></div>
                        <span>Mei</span>
                    </div>
                    <div class="simpel-bar-wrap">
                        <div class="simpel-bar" style="height: 94%;" title="Juni: 94 Peserta"></div>
                        <span class="fw-bold text-dark">Jun</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between pt-16 mt-16 border-top gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle" style="background: #f3bc42;"></span>
                        <span class="small text-muted">Peserta Lulus Evaluasi: <strong
                                class="text-dark">89%</strong></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle" style="background: #0c3158;"></span>
                        <span class="small text-muted">Rata-rata Kehadiran: <strong
                                class="text-dark">94.2%</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agenda Terdekat --}}
        <div class="col-lg-4">
            <div class="simpel-card p-24 h-100">
                <div class="d-flex align-items-center justify-content-between mb-16">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 fs-6">Jadwal Terdekat</h5>
                        <p class="text-muted mb-0 small">Agenda tiga hari ke depan</p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 11px;">
                        Lihat Semua
                    </button>
                </div>

                <div class="d-flex flex-column gap-3">
                    {{-- Agenda 1 --}}
                    <div class="d-flex align-items-center gap-3 p-12 rounded-3 border" style="background: #fdfefe;">
                        <div class="text-center p-2 rounded-3" style="background: #edf3f9; min-width: 50px;">
                            <strong class="d-block fw-bold text-dark fs-6" style="line-height: 1;">20</strong>
                            <small class="text-uppercase text-muted"
                                style="font-size: 10px; font-weight: 700;">SEP</small>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-dark fs-6" style="font-size: 13px !important;">Manajemen
                                Administrator</h6>
                            <p class="mb-0 text-muted small" style="font-size: 11px;">08.00 WIB · Aula BKPSDM</p>
                        </div>
                        <span class="simpel-badge simpel-badge-success">Siap</span>
                    </div>

                    {{-- Agenda 2 --}}
                    <div class="d-flex align-items-center gap-3 p-12 rounded-3 border" style="background: #fdfefe;">
                        <div class="text-center p-2 rounded-3" style="background: #edf3f9; min-width: 50px;">
                            <strong class="d-block fw-bold text-dark fs-6" style="line-height: 1;">22</strong>
                            <small class="text-uppercase text-muted"
                                style="font-size: 10px; font-weight: 700;">SEP</small>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-dark fs-6" style="font-size: 13px !important;">
                                Pengelolaan Keuangan</h6>
                            <p class="mb-0 text-muted small" style="font-size: 11px;">09.00 WIB · Ruang Rapat</p>
                        </div>
                        <span class="simpel-badge simpel-badge-warn">Persiapan</span>
                    </div>

                    {{-- Agenda 3 --}}
                    <div class="d-flex align-items-center gap-3 p-12 rounded-3 border" style="background: #fdfefe;">
                        <div class="text-center p-2 rounded-3" style="background: #edf3f9; min-width: 50px;">
                            <strong class="d-block fw-bold text-dark fs-6" style="line-height: 1;">24</strong>
                            <small class="text-uppercase text-muted"
                                style="font-size: 10px; font-weight: 700;">SEP</small>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold text-dark fs-6" style="font-size: 13px !important;">Digital
                                Government</h6>
                            <p class="mb-0 text-muted small" style="font-size: 11px;">08.30 WIB · Hybrid Sesi</p>
                        </div>
                        <span class="simpel-badge simpel-badge-success">Siap</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Queue / Antrian Pemeriksaan (Progress Bars) --}}
    <div class="row row-cols-lg-3 row-cols-1 g-3 mb-24">
        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Verifikasi Pendaftaran</strong>
                    <span class="badge bg-warning text-dark fw-bold">72%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">29 berkas menunggu pemeriksaan
                    berkas</small>
                <div class="queue-progress">
                    <div class="queue-progress-bar" style="width: 72%;"></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Kelengkapan Jadwal</strong>
                    <span class="badge bg-secondary text-white fw-bold">58%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">4 sesi belum menetapkan
                    instruktur/mentor</small>
                <div class="queue-progress">
                    <div class="queue-progress-bar" style="width: 58%; background: #0c3158;"></div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Evaluasi Peserta</strong>
                    <span class="badge bg-success text-white fw-bold">83%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">83% kuesioner & kuis evaluasi
                    terisi</small>
                <div class="queue-progress">
                    <div class="queue-progress-bar" style="width: 83%; background: #16845b;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pendaftaran & Verifikasi Terkini --}}
    <div class="simpel-card p-24 mb-24">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-20">
            <div>
                <h5 class="fw-bold text-dark mb-1 fs-6">Pendaftaran & Verifikasi Terkini</h5>
                <p class="text-muted mb-0 small">Daftar calon peserta yang masuk dalam antrian verifikasi diklat
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 12px;">
                    Filter Status
                </button>
                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1"
                    style="background-color: #071a33; border-color: #071a33; font-size: 12px;">
                    Verifikasi Massal
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead
                    style="background-color: #f8f9fb; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b768a;">
                    <tr>
                        <th class="py-3 px-3">No. Registrasi</th>
                        <th class="py-3 px-3">Nama Calon Peserta</th>
                        <th class="py-3 px-3">Instansi / SKPK</th>
                        <th class="py-3 px-3">Program Pelatihan</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    <tr>
                        <td class="px-3 fw-bold text-dark">REG-260901-018</td>
                        <td class="px-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="background: #edf2f8; color: #071a33; font-size: 12px;">
                                    NA
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Nur Aini, S.STP</strong>
                                    <small class="text-muted" style="font-size: 11px;">NIP: 19920314 201507 2
                                        001</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-3">Sekretariat Daerah</td>
                        <td class="px-3">Pelatihan Manajemen Administrator</td>
                        <td class="px-3">
                            <span class="simpel-badge simpel-badge-gold">Diajukan</span>
                        </td>
                        <td class="px-3 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                style="font-size: 11px;">Periksa</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="px-3 fw-bold text-dark">REG-260901-019</td>
                        <td class="px-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="background: #def4e9; color: #16845b; font-size: 12px;">
                                    FZ
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Fauzan, SE., M.Si</strong>
                                    <small class="text-muted" style="font-size: 11px;">NIP: 19881105 201103 1
                                        002</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-3">BPKD Aceh Timur</td>
                        <td class="px-3">Pengelolaan Keuangan Daerah</td>
                        <td class="px-3">
                            <span class="simpel-badge simpel-badge-success">Diverifikasi</span>
                        </td>
                        <td class="px-3 text-end">
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1"
                                style="font-size: 11px;">Detail</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="px-3 fw-bold text-dark">REG-260901-020</td>
                        <td class="px-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="background: #fff2d4; color: #9a6700; font-size: 12px;">
                                    RW
                                </div>
                                <div>
                                    <strong class="text-dark d-block">Rahmawati, SKM</strong>
                                    <small class="text-muted" style="font-size: 11px;">NIP: 19940621 201903 2
                                        004</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-3">Dinas Kesehatan</td>
                        <td class="px-3">Digital Government</td>
                        <td class="px-3">
                            <span class="simpel-badge simpel-badge-warn">Perlu Perbaikan</span>
                        </td>
                        <td class="px-3 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                style="font-size: 11px;">Periksa</button>
                        </td>
                    </tr>

                    <tr>
                        <td class="px-3 fw-bold text-dark">REG-260901-021</td>
                        <td class="px-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                    style="background: #edf2f8; color: #071a33; font-size: 12px;">
                                    MR
                                </div>
                                <div>
                                    <strong class="text-dark d-block">M. Ridwan, S.Pd</strong>
                                    <small class="text-muted" style="font-size: 11px;">NIP: 19890412 201402 1
                                        003</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-3">Dinas Pendidikan</td>
                        <td class="px-3">Pelatihan Kepemimpinan</td>
                        <td class="px-3">
                            <span class="simpel-badge simpel-badge-gold">Menunggu</span>
                        </td>
                        <td class="px-3 text-end">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                style="font-size: 11px;">Periksa</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Rencana Pelatihan Baru (prototype.html style) --}}
    <div class="modal fade" id="modalRencanaPelatihan" tabindex="-1" aria-labelledby="modalRencanaPelatihanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content"
                style="border-radius: 20px; border: 1px solid #e6eaf0; box-shadow: 0 10px 40px rgba(7, 26, 51, 0.15);">
                <div class="modal-header border-bottom py-20 px-24"
                    style="background-color: #071a33; border-radius: 19px 19px 0 0;">
                    <div>
                        <span class="text-uppercase fw-bold text-xs"
                            style="color: #f3bc42; letter-spacing: 1.5px;">Formulir Kelas</span>
                        <h5 class="modal-title fw-bold text-white mb-0" id="modalRencanaPelatihanLabel">Kelas Pelatihan Baru</h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-24">
                    <p class="text-muted small mb-20">
                        Isi parameter utama pelatihan di bawah. Rencana dapat disimpan sebagai draf sebelum diajukan ke
                        tahap pendaftaran publik.
                    </p>

                    <form>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small">Nama Program Pelatihan</label>
                                <input type="text" class="form-control rounded-3"
                                    placeholder="Contoh: Pelatihan Manajemen Administrator Angkatan II">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Kategori Pelatihan</label>
                                <select class="form-select rounded-3">
                                    <option selected>Pelatihan Kepemimpinan</option>
                                    <option>Pelatihan Teknis Fungsional</option>
                                    <option>Pelatihan Sosial Kultural</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Metode Pelaksanaan</label>
                                <select class="form-select rounded-3">
                                    <option selected>Luring (Tatap Muka di Aula)</option>
                                    <option>Daring (E-Learning Penuh)</option>
                                    <option>Hybrid (Kombinasi)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Tanggal Mulai Pelaksanaan</label>
                                <input type="date" class="form-control rounded-3">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark small">Kuota Peserta</label>
                                <input type="number" class="form-control rounded-3" value="40" min="1">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-top py-16 px-24 d-flex justify-content-end gap-2"
                    style="background-color: #f8f9fb; border-radius: 0 0 19px 19px;">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-simpel-gold px-4" data-bs-dismiss="modal">
                        Simpan Draf Rencana
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
