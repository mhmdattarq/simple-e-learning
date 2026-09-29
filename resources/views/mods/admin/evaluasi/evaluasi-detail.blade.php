<div>
    {{-- Header & Breadcrumbs --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge bg-secondary-subtle text-secondary py-1 px-2" style="font-size: 11px;">
                    <i class="ri-folder-3-line me-1"></i>{{ $course->category?->name ?? 'Tanpa Kategori' }}
                </span>
                @if ($course->type === 'permanent')
                    <span class="badge bg-success-subtle text-success py-1 px-2" style="font-size: 11px;">
                        <i class="ri-infinity-line me-1"></i>Permanen
                    </span>
                @elseif($course->type === 'paid' || $course->type === 'berbayar')
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle py-1 px-2"
                        style="font-size: 11px;">
                        <i class="ri-money-dollar-circle-line me-1"></i>Berbayar
                    </span>
                @else
                    <span class="badge bg-primary-subtle text-primary py-1 px-2" style="font-size: 11px;">
                        <i class="ri-calendar-line me-1"></i>Batch
                    </span>
                @endif
            </div>
            <h4 class="fw-bold text-dark mb-1">{{ $course->title }}</h4>
            <p class="text-muted mb-0" style="font-size: 13.5px;">Monitoring metrik evaluasi pembelajaran, rincian butir
                soal kurikulum, dan riwayat pengerjaan peserta.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('evaluasi.data') }}"
                class="btn btn btn-danger d-inline-flex align-items-center gap-2 shadow-sm radius-8 px-3" wire:navigate>
                <i class="ri-arrow-left-line"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- 4 Kartu Metrik Monitoring Evaluasi --}}
    <div class="row g-3 mb-24">
        {{-- Card 1: Total Evaluasi --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card-monitoring border-0 shadow-sm radius-16 h-100 p-20">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary fw-semibold"
                        style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px;">Total
                        Evaluasi</span>
                    <div class="stat-icon-wrapper bg-primary-subtle text-primary">
                        <i class="ri-questionnaire-line fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['total_quizzes'] }}</h3>
                    <span class="text-muted" style="font-size: 13px;">Kuis Aktif</span>
                </div>
                <span class="text-muted" style="font-size: 11.5px;">Instrumen penilaian terdaftar</span>
            </div>
        </div>

        {{-- Card 2: Kuis Bab Formatif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card-monitoring border-0 shadow-sm radius-16 h-100 p-20">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary fw-semibold"
                        style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px;">Kuis Bab
                        (Formatif)</span>
                    <div class="stat-icon-wrapper bg-info-subtle text-info">
                        <i class="ri-booklet-line fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['chapter_quizzes'] }}</h3>
                    <span class="text-muted" style="font-size: 13px;">Kuis Bab</span>
                </div>
                <span class="text-muted" style="font-size: 11.5px;">Evaluasi berkala setiap bab materi</span>
            </div>
        </div>

        {{-- Card 3: Ujian Akhir Sumatif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card-monitoring border-0 shadow-sm radius-16 h-100 p-20">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary fw-semibold"
                        style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px;">Ujian Akhir
                        (Sumatif)</span>
                    <div class="stat-icon-wrapper bg-warning-subtle text-warning">
                        <i class="ri-award-line fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    @if ($stats['has_final_quiz'])
                        <h5 class="fw-bold text-success mb-0 d-flex align-items-center gap-1">
                            <i class="ri-checkbox-circle-fill"></i> Tersedia
                        </h5>
                        <span class="text-muted" style="font-size: 12px;">KKM:
                            {{ $stats['final_quiz']->passing_score }}</span>
                    @else
                        <h5 class="fw-bold text-muted mb-0 d-flex align-items-center gap-1">
                            <i class="ri-close-circle-line"></i> Belum Dibuat
                        </h5>
                    @endif
                </div>
                <span class="text-muted" style="font-size: 11.5px;">Syarat kelulusan &amp; penerbitan
                    e-sertifikat</span>
            </div>
        </div>

        {{-- Card 4: Partisipasi & Kelulusan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card stat-card-monitoring border-0 shadow-sm radius-16 h-100 p-20">
                <div class="d-flex align-items-center justify-content-between mb-12">
                    <span class="text-secondary fw-semibold"
                        style="font-size: 12.5px; text-transform: uppercase; letter-spacing: 0.5px;">Partisipasi
                        Peserta</span>
                    <div class="stat-icon-wrapper bg-success-subtle text-success">
                        <i class="ri-user-star-line fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <h3 class="fw-bold text-dark mb-0">{{ $stats['pass_rate'] }}%</h3>
                    <span class="text-muted" style="font-size: 13px;">Kelulusan</span>
                </div>
                <span class="text-muted" style="font-size: 11.5px;">{{ $stats['passed_attempts'] }} dari
                    {{ $stats['total_attempts'] }} pengerjaan lulus (Rerata: {{ $stats['avg_score'] }}%)</span>
            </div>
        </div>
    </div>

    {{-- Section 1: Rincian Kurikulum & Butir Soal Evaluasi --}}
    <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
        <div
            class="card-header bg-white pt-20 pb-16 px-20 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-list-check-3 text-simple fs-5"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Kurikulum Evaluasi &amp; Butir Soal</h6>
                    <small class="text-muted">Daftar instrumen kuis bab dan ujian akhir beserta kunci jawaban</small>
                </div>
            </div>
        </div>

        <div class="card-body p-20">
            @php
                $hasAnyQuizzes = $stats['total_quizzes'] > 0;
            @endphp

            @if (!$hasAnyQuizzes)
                <div class="text-center py-40">
                    <div class="stat-icon-wrapper bg-light text-muted mx-auto mb-3"
                        style="width: 56px; height: 56px; border-radius: 14px; font-size: 26px;">
                        <i class="ri-file-warning-line"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Evaluasi Dibuat</h6>
                    <p class="text-muted mb-3" style="max-width: 440px; margin: 0 auto; font-size: 13px;">
                        Kelas ini belum memiliki instrumen kuis bab maupun ujian akhir kelulusan. Mulai tambahkan
                        evaluasi untuk mengukur capaian belajar peserta.
                    </p>
                    <a href="{{ route('evaluasi.create', ['course_id' => $course->id]) }}"
                        class="btn btn-simple-gold btn-sm d-inline-flex align-items-center gap-2 px-3 radius-8"
                        wire:navigate>
                        <i class="ri-add-line fs-6"></i>
                        <span>Tambah Evaluasi Sekarang</span>
                    </a>
                </div>
            @else
                <div class="d-flex flex-column gap-3">
                    {{-- 1. Kuis Bab (Iterasi berdasarkan Chapter kurikulum) --}}
                    @foreach ($course->chapters as $chapter)
                        @foreach ($chapter->quizzes as $quiz)
                            <div class="quiz-curriculum-card radius-12 border bg-white overflow-hidden shadow-xs"
                                x-data="{ expanded: false }">
                                {{-- Baris Header Kuis --}}
                                <div class="p-16 d-flex align-items-center justify-content-between flex-wrap gap-3 cursor-pointer bg-light-subtle"
                                    @click="expanded = !expanded">
                                    <div class="d-flex align-items-center gap-3">
                                        <button type="button"
                                            class="btn btn-sm btn-light border p-1 rounded-circle d-inline-flex align-items-center justify-content-center"
                                            style="width: 28px; height: 28px;">
                                            <i class="ri-arrow-down-s-line fs-5 transition-transform"
                                                :class="{ 'rotate-180': expanded }"></i>
                                        </button>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <span
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle"
                                                    style="font-size: 11px;">
                                                    <i class="ri-booklet-line me-1"></i>Bab: {{ $chapter->title }}
                                                </span>
                                                <span class="badge bg-secondary-subtle text-secondary"
                                                    style="font-size: 11px;">
                                                    <i class="ri-time-line me-1"></i>{{ $quiz->time_limit_minutes }}
                                                    Menit
                                                </span>
                                                <span class="badge bg-info-subtle text-info" style="font-size: 11px;">
                                                    <i class="ri-checkbox-circle-line me-1"></i>KKM:
                                                    {{ $quiz->passing_score }} Poin
                                                </span>
                                                <span class="badge bg-light text-dark border"
                                                    style="font-size: 11px;">
                                                    <i
                                                        class="ri-question-line me-1"></i>{{ $quiz->questions->count() }}
                                                    Butir Soal (Total Skor: {{ $quiz->total_score }})
                                                </span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 14.5px;">
                                                {{ $quiz->title }}</h6>
                                            @if ($quiz->description)
                                                <p class="text-muted mb-0 mt-1" style="font-size: 12.5px;">
                                                    {{ $quiz->description }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Aksi Kuis --}}
                                    <div class="d-flex align-items-center gap-2" @click.stop>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 radius-8 px-2 py-1"
                                            style="font-size: 12px;" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete"
                                            wire:click="hookModalDelete({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')">
                                            <i class="ri-delete-bin-line"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Bagian Daftar Butir Soal (Collapsible) --}}
                                <div x-show="expanded" x-collapse>
                                    <div class="p-20 border-top bg-white">
                                        @if ($quiz->questions->isEmpty())
                                            <p class="text-muted fst-italic mb-0" style="font-size: 13px;">Belum ada
                                                butir soal di kuis ini.</p>
                                        @else
                                            <div class="d-flex flex-column gap-3">
                                                @foreach ($quiz->questions as $qIndex => $question)
                                                    <div
                                                        class="question-preview-box p-16 radius-10 border bg-light-subtle">
                                                        <div
                                                            class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                            <div class="d-flex align-items-baseline gap-2">
                                                                <span class="badge bg-dark text-white rounded-pill"
                                                                    style="font-size: 11px;">No
                                                                    {{ $qIndex + 1 }}</span>
                                                                <span class="fw-semibold text-dark"
                                                                    style="font-size: 13.5px;">{{ $question->question_text }}</span>
                                                            </div>
                                                            <span
                                                                class="badge bg-primary-subtle text-primary border border-primary-subtle text-nowrap"
                                                                style="font-size: 11px;">
                                                                Bobot: {{ $question->score }} Poin
                                                            </span>
                                                        </div>

                                                        {{-- Opsi Jawaban --}}
                                                        <div class="row g-2 mt-1">
                                                            @foreach ($question->options as $optIndex => $option)
                                                                @php
                                                                    $optLetter = chr(65 + $optIndex);
                                                                    $isCorrect = (bool) $option->is_correct;
                                                                @endphp
                                                                <div class="col-12 col-md-6">
                                                                    <div class="option-item-preview p-2 px-3 radius-8 border d-flex align-items-center justify-content-between gap-2 {{ $isCorrect ? 'bg-success-subtle border-success text-success fw-medium' : 'bg-white text-secondary' }}"
                                                                        style="font-size: 12.5px;">
                                                                        <div class="d-flex align-items-center gap-2">
                                                                            <span
                                                                                class="badge {{ $isCorrect ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                                                                                style="font-size: 10px;">{{ $optLetter }}</span>
                                                                            <span>{{ $option->option_text }}</span>
                                                                        </div>
                                                                        @if ($isCorrect)
                                                                            <span
                                                                                class="badge bg-success text-white d-inline-flex align-items-center gap-1"
                                                                                style="font-size: 10px;">
                                                                                <i class="ri-check-line"></i> Kunci
                                                                                Jawaban
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach

                    {{-- 2. Ujian Akhir (Final Quiz) --}}
                    @if ($course->finalQuiz)
                        @php
                            $finalQuiz = $course->finalQuiz;
                        @endphp
                        <div class="quiz-curriculum-card final-quiz-card radius-12 border border-warning-subtle bg-white overflow-hidden shadow-xs"
                            x-data="{ expanded: false }">
                            {{-- Baris Header Ujian Akhir --}}
                            <div class="p-16 d-flex align-items-center justify-content-between flex-wrap gap-3 cursor-pointer bg-warning-subtle"
                                @click="expanded = !expanded">
                                <div class="d-flex align-items-center gap-3">
                                    <button type="button"
                                        class="btn btn-sm btn-white border p-1 rounded-circle d-inline-flex align-items-center justify-content-center"
                                        style="width: 28px; height: 28px;">
                                        <i class="ri-arrow-down-s-line fs-5 transition-transform"
                                            :class="{ 'rotate-180': expanded }"></i>
                                    </button>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <span class="badge bg-warning text-dark fw-bold border border-warning"
                                                style="font-size: 11px;">
                                                <i class="ri-award-fill me-1 text-dark"></i>Ujian Akhir Kelas (Sumatif)
                                            </span>
                                            <span class="badge bg-white text-secondary border"
                                                style="font-size: 11px;">
                                                <i class="ri-time-line me-1"></i>{{ $finalQuiz->time_limit_minutes }}
                                                Menit
                                            </span>
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle"
                                                style="font-size: 11px;">
                                                <i class="ri-checkbox-circle-line me-1"></i>KKM:
                                                {{ $finalQuiz->passing_score }} Poin
                                            </span>
                                            <span class="badge bg-white text-dark border" style="font-size: 11px;">
                                                <i
                                                    class="ri-question-line me-1"></i>{{ $finalQuiz->questions->count() }}
                                                Butir Soal (Total Skor: {{ $finalQuiz->total_score }})
                                            </span>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 15px;">
                                            {{ $finalQuiz->title }}</h6>
                                        @if ($finalQuiz->description)
                                            <p class="text-muted mb-0 mt-1" style="font-size: 12.5px;">
                                                {{ $finalQuiz->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Aksi Ujian Akhir --}}
                                <div class="d-flex align-items-center gap-2" @click.stop>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 radius-8 px-2 py-1"
                                        style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#modalDelete"
                                        wire:click="hookModalDelete({{ $finalQuiz->id }}, '{{ addslashes($finalQuiz->title) }}')">
                                        <i class="ri-delete-bin-line"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Butir Soal Ujian Akhir (Collapsible) --}}
                            <div x-show="expanded" x-collapse>
                                <div class="p-20 border-top bg-white">
                                    @if ($finalQuiz->questions->isEmpty())
                                        <p class="text-muted fst-italic mb-0" style="font-size: 13px;">Belum ada butir
                                            soal di ujian akhir ini.</p>
                                    @else
                                        <div class="d-flex flex-column gap-3">
                                            @foreach ($finalQuiz->questions as $qIndex => $question)
                                                <div
                                                    class="question-preview-box p-16 radius-10 border bg-light-subtle">
                                                    <div
                                                        class="d-flex align-items-start justify-content-between gap-3 mb-2">
                                                        <div class="d-flex align-items-baseline gap-2">
                                                            <span
                                                                class="badge bg-warning text-dark fw-bold rounded-pill"
                                                                style="font-size: 11px;">No {{ $qIndex + 1 }}</span>
                                                            <span class="fw-semibold text-dark"
                                                                style="font-size: 13.5px;">{{ $question->question_text }}</span>
                                                        </div>
                                                        <span
                                                            class="badge bg-warning-subtle text-dark border border-warning text-nowrap"
                                                            style="font-size: 11px;">
                                                            Bobot: {{ $question->score }} Poin
                                                        </span>
                                                    </div>

                                                    <div class="row g-2 mt-1">
                                                        @foreach ($question->options as $optIndex => $option)
                                                            @php
                                                                $optLetter = chr(65 + $optIndex);
                                                                $isCorrect = (bool) $option->is_correct;
                                                            @endphp
                                                            <div class="col-12 col-md-6">
                                                                <div class="option-item-preview p-2 px-3 radius-8 border d-flex align-items-center justify-content-between gap-2 {{ $isCorrect ? 'bg-success-subtle border-success text-success fw-medium' : 'bg-white text-secondary' }}"
                                                                    style="font-size: 12.5px;">
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <span
                                                                            class="badge {{ $isCorrect ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                                                                            style="font-size: 10px;">{{ $optLetter }}</span>
                                                                        <span>{{ $option->option_text }}</span>
                                                                    </div>
                                                                    @if ($isCorrect)
                                                                        <span
                                                                            class="badge bg-success text-white d-inline-flex align-items-center gap-1"
                                                                            style="font-size: 10px;">
                                                                            <i class="ri-check-line"></i> Kunci Jawaban
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Section 2: Monitoring & Riwayat Pengerjaan Peserta (Yajra DataTables Server-Side) --}}
    <div class="card simpel-card border-0 shadow-sm radius-16">
        <div
            class="card-header bg-white pt-20 pb-0 px-20 border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-history-line text-simple fs-5"></i>
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Riwayat &amp; Hasil Pengerjaan Peserta</h6>
                    <small class="text-muted">Monitoring realtime pengerjaan kuis peserta tanpa reload halaman</small>
                </div>
            </div>
        </div>

        {{-- Kontainer tabel server-side dengan wire:ignore --}}
        <div class="card-body p-20" wire:ignore>
            <div class="table-responsive">
                <table id="tableAttempts" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nama Peserta</th>
                            <th>Kuis / Evaluasi</th>
                            <th class="text-center" style="width: 140px;">Skor Diperoleh</th>
                            <th class="text-center" style="width: 110px;">Nilai Akhir</th>
                            <th class="text-center" style="width: 130px;">Status</th>
                            <th class="text-center" style="width: 160px;">Waktu Submit</th>
                        </tr>
                        {{-- Thead Filter Baris Kedua --}}
                        <tr id="header-filter-attempts" class="bg-light">
                            <th></th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari nama peserta...">
                            </th>
                            <th>
                                <input type="text" class="form-control form-control-sm search-col-dt"
                                    placeholder="Cari kuis...">
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Diisi secara dinamis melalui AJAX Yajra DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Include Script ATC untuk Halaman Detail Evaluasi --}}
    @include('mods.admin.evaluasi.atc.evaluasi-detail-atc')
</div>
