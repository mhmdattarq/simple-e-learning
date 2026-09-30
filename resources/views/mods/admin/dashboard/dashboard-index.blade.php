<div>
    {{-- Top Header / Breadcrumbs --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <span class="text-uppercase fw-bold text-xs" style="color: #b37a05; letter-spacing: 1.5px;">Sistem
                Manajemen Kelas</span>
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
                Kelola seluruh siklus kelas dalam satu sistem
            </h2>
            <p class="simpel-hero-desc">
                Data terintegrasi, real time, transparan, dan akuntabel — BKPSDM Kabupaten Aceh Timur.
            </p>
        </div>
        @if (auth()->user()?->isAdmin())
            <a href="{{ route('kelas.create') }}" class="btn-simpel-gold text-decoration-none">
                <i class="ri-add-line"></i>
                Buat Kelas Baru
            </a>
        @endif
    </div>

    {{-- 4-Stage Flow / Alur Pembelajaran --}}
    <div class="mb-24">
        <div class="flow-grid">
            <div class="flow-card">
                <div class="flow-n">1</div>
                <span>1. Registrasi Akun</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">2</div>
                <span>2. Masuk Menggunakan Akun</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">3</div>
                <span>3. Melakukan Pembelajaran</span>
            </div>
            <div class="flow-card">
                <div class="flow-n">4</div>
                <span>4. Evaluasi</span>
            </div>
        </div>
    </div>

    {{-- 4 Stat Cards --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 mb-24">
        {{-- Stat 1: Kelas Aktif --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Kelas Aktif</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #fff4d8; color: #9a6700;">
                        <i class="ri-award-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">{{ $publishedCoursesCount }}</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-checkbox-circle-line"></i>
                    <span>{{ $totalCoursesCount }} total kelas terdaftar</span>
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
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">{{ $totalParticipantsCount }}</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-user-follow-line"></i>
                    <span>{{ $activeParticipantsCount }} peserta aktif belajar</span>
                </div>
            </div>
        </div>

        {{-- Stat 3: Materi Pembelajaran --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Materi Pembelajaran</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #e9eff7; color: #0c3158;">
                        <i class="ri-book-open-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">{{ $totalLessonsCount }}</h3>
                <div class="d-flex align-items-center gap-1 text-primary-light"
                    style="font-size: 12px; font-weight: 600;">
                    <i class="ri-folders-line"></i>
                    <span>Tersebar di {{ $totalChaptersCount }} bab/silabus</span>
                </div>
            </div>
        </div>

        {{-- Stat 4: Evaluasi Selesai --}}
        <div class="col">
            <div class="simpel-card p-20 h-100">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary-light fw-medium" style="font-size: 13px;">Evaluasi Selesai</span>
                    <div class="w-40-px h-40-px rounded-3 d-flex align-items-center justify-content-center"
                        style="background-color: #fff2d4; color: #b37a05;">
                        <i class="ri-file-list-3-line fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1" style="color: #071a33; font-size: 28px;">{{ $completedEvaluationsCount }}</h3>
                <div class="d-flex align-items-center gap-1" style="color: #16845b; font-size: 12px; font-weight: 600;">
                    <i class="ri-percent-line"></i>
                    <span>{{ $passRate }}% tingkat kelulusan</span>
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
                        <h5 class="fw-bold text-dark mb-1 fs-6">Tren Pendaftaran Peserta</h5>
                        <p class="text-muted mb-0 small">Perkembangan peserta diklat terdaftar 6 bulan terakhir</p>
                    </div>
                    <select class="form-select form-select-sm w-auto border"
                        style="font-size: 12px; border-radius: 8px;">
                        <option selected>Semester Berjalan ({{ now()->year }})</option>
                    </select>
                </div>

                {{-- CSS Interactive Bar Chart --}}
                <div class="simpel-chart">
                    @foreach ($chartData as $month)
                        <div class="simpel-bar-wrap">
                            <div class="simpel-bar" style="height: {{ $month['height_percent'] }}%;"
                                title="{{ $month['full'] }}: {{ $month['count'] }} Peserta"></div>
                            <span
                                class="{{ $month['is_current'] ? 'fw-bold text-dark' : '' }}">{{ $month['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap align-items-center justify-content-between pt-16 mt-16 border-top gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle" style="background: #f3bc42;"></span>
                        <span class="small text-muted">Peserta Lulus Evaluasi: <strong
                                class="text-dark">{{ $passRate }}%</strong></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle" style="background: #0c3158;"></span>
                        <span class="small text-muted">Rata-rata Skor Kuis: <strong
                                class="text-dark">{{ $avgQuizScore }}%</strong></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Agenda / Program Kelas Terkini --}}
        <div class="col-lg-4">
            <div class="simpel-card p-24 h-100">
                <div class="d-flex align-items-center justify-content-between mb-16">
                    <div>
                        <h5 class="fw-bold text-dark mb-1 fs-6">Program Kelas Terbaru</h5>
                        <p class="text-muted mb-0 small">Daftar kelas yang baru ditambahkan</p>
                    </div>
                    <a href="{{ route('kelas.data') }}"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 11px;">
                        Lihat Semua
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse ($recentCourses as $course)
                        <div class="d-flex align-items-center gap-3 p-12 rounded-3 border"
                            style="background: #fdfefe;">
                            <div class="text-center p-2 rounded-3" style="background: #edf3f9; min-width: 50px;">
                                <strong class="d-block fw-bold text-dark fs-6" style="line-height: 1;">
                                    {{ $course->start_date ? $course->start_date->format('d') : $course->created_at->format('d') }}
                                </strong>
                                <small class="text-uppercase text-muted" style="font-size: 10px; font-weight: 700;">
                                    {{ $course->start_date ? $course->start_date->translatedFormat('M') : $course->created_at->translatedFormat('M') }}
                                </small>
                            </div>
                            <div class="flex-grow-1 text-truncate">
                                <h6 class="mb-1 fw-bold text-dark fs-6 text-truncate"
                                    style="font-size: 13px !important;" title="{{ $course->title }}">
                                    {{ $course->title }}
                                </h6>
                                <p class="mb-0 text-muted small text-truncate" style="font-size: 11px;">
                                    {{ $course->category?->name ?? 'Umum' }} · {{ $course->type ?? 'Mandiri' }}
                                </p>
                            </div>
                            <span
                                class="simpel-badge {{ $course->status?->value === 'published' ? 'simpel-badge-success' : 'simpel-badge-warn' }}">
                                {{ $course->status?->label() ?? 'Aktif' }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            Belum ada program kelas yang ditambahkan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Queue / Antrian Pemeriksaan (Progress Bars) --}}
    <div class="row row-cols-lg-3 row-cols-1 g-3 mb-24">
        {{-- Antrian 1: Verifikasi Pendaftaran --}}
        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Verifikasi Pendaftaran</strong>
                    <span class="badge bg-warning text-dark fw-bold">{{ $registrationVerifyRate }}%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">
                    {{ $pendingRegistrationsCount }} berkas menunggu verifikasi berkas
                </small>
                <div class="queue-progress">
                    <div class="queue-progress-bar" style="width: {{ $registrationVerifyRate }}%;"></div>
                </div>
            </div>
        </div>

        {{-- Antrian 2: Kelengkapan Silabus & Modul --}}
        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Kelengkapan Materi Kelas</strong>
                    <span class="badge bg-secondary text-white fw-bold">{{ $syllabusCompletenessRate }}%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">
                    {{ $coursesWithLessonsCount }} dari {{ $totalCoursesCount }} kelas telah dilengkapi modul materi
                </small>
                <div class="queue-progress">
                    <div class="queue-progress-bar"
                        style="width: {{ $syllabusCompletenessRate }}%; background: #0c3158;"></div>
                </div>
            </div>
        </div>

        {{-- Antrian 3: Kelulusan Evaluasi Peserta --}}
        <div class="col">
            <div class="queue-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark fs-6" style="font-size: 14px;">Tingkat Kelulusan Evaluasi</strong>
                    <span class="badge bg-success text-white fw-bold">{{ $passRate }}%</span>
                </div>
                <small class="text-muted d-block mb-3" style="font-size: 12px;">
                    {{ $completedEvaluationsCount }} evaluasi kuis telah disubmit oleh peserta
                </small>
                <div class="queue-progress">
                    <div class="queue-progress-bar" style="width: {{ $passRate }}%; background: #16845b;"></div>
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
            {{-- <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1" style="font-size: 12px;">
                    Filter Status
                </button>
                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1"
                    style="background-color: #071a33; border-color: #071a33; font-size: 12px;">
                    Verifikasi Massal
                </button>
            </div> --}}
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-hover mb-0">
                <thead
                    style="background-color: #f8f9fb; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b768a;">
                    <tr>
                        <th class="py-3 px-3">No. Registrasi</th>
                        <th class="py-3 px-3">Nama Calon Peserta</th>
                        <th class="py-3 px-3">Instansi / SKPK</th>
                        <th class="py-3 px-3">Program Kelas</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    @forelse ($recentRegistrations as $reg)
                        @php
                            $user = $reg->user;
                            $initials = '';
                            if ($user?->name) {
                                $words = explode(' ', trim($user->name));
                                $initials = strtoupper(
                                    substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''),
                                );
                            } else {
                                $initials = 'PS';
                            }

                            $badgeClass = match ($reg->status?->value ?? (string) $reg->status) {
                                'pending' => 'simpel-badge-gold',
                                'verified', 'active', 'completed' => 'simpel-badge-success',
                                'revision_required' => 'simpel-badge-warn',
                                default => 'simpel-badge-navy',
                            };
                        @endphp
                        <tr>
                            <td class="px-3 fw-bold text-dark">
                                {{ $reg->registration_number ?? 'REG-' . str_pad($reg->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="w-32-px h-32-px rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        style="background: #edf2f8; color: #071a33; font-size: 12px;">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block">{{ $user?->name ?? 'Peserta' }}</strong>
                                        <small class="text-muted" style="font-size: 11px;">
                                            {{ $user?->nip ? 'NIP: ' . $user->nip : $user?->email }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3">{{ $user?->address ?: 'Aceh Timur' }}</td>
                            <td class="px-3">{{ $reg->course?->title ?? '-' }}</td>
                            <td class="px-3">
                                <span
                                    class="simpel-badge {{ $badgeClass }}">{{ $reg->status?->label() ?? ucfirst((string) $reg->status) }}</span>
                            </td>
                            <td class="px-3 text-end">
                                <a href="{{ route('kelas.data') }}"
                                    class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1"
                                    style="font-size: 11px;">Periksa</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted small">
                                Belum ada data pendaftaran kelas terkini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
