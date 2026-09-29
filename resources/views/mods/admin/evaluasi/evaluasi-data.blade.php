@push('css')
    <style>
        .stat-card-quiz {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .stat-card-quiz:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.05);
        }

        .stat-icon-quiz {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .table-quiz thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 11.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 14px;
        }

        .table-quiz tbody td {
            padding: 14px 14px;
            vertical-align: middle;
            font-size: 13.5px;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-type-chapter {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .badge-type-final {
            background-color: #fefce8;
            color: #854d0e;
            border: 1px solid #fef08a;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .score-pill-badge {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .modal-detail-quiz .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
        }

        .preview-question-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 16px;
        }

        .preview-opt-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .preview-opt-item.is-correct-answer {
            background: #f0fdf4;
            border-color: #86efac;
            color: #166534;
            font-weight: 600;
        }

        .opt-letter-circle {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: #e2e8f0;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
        }

        .preview-opt-item.is-correct-answer .opt-letter-circle {
            background: #22c55e;
            color: #ffffff;
        }
    </style>
@endpush

<div>
    {{-- Header & Aksi Tambah --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Data Evaluasi &amp; Kuis</h5>
            <p class="text-muted mb-0">Manajemen katalog seluruh evaluasi per bab dan ujian akhir (final quiz) kelas.</p>
        </div>
    </div>

    {{-- Kartu Metrik Ringkasan --}}
    <div class="row g-3 mb-24">
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-quiz p-16 d-flex align-items-center gap-3">
                <div class="stat-icon-quiz bg-primary-subtle text-primary">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ number_format($this->stats['total_quizzes']) }}</h5>
                    <span class="text-muted text-xs">Total Evaluasi / Kuis</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-quiz p-16 d-flex align-items-center gap-3">
                <div class="stat-icon-quiz bg-info-subtle text-info">
                    <i class="ri-book-read-line"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ number_format($this->stats['chapter_quizzes']) }}</h5>
                    <span class="text-muted text-xs">Kuis Bab Pembelajaran</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-quiz p-16 d-flex align-items-center gap-3">
                <div class="stat-icon-quiz bg-warning-subtle text-warning">
                    <i class="ri-award-line"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ number_format($this->stats['final_quizzes']) }}</h5>
                    <span class="text-muted text-xs">Ujian Akhir (Final Quiz)</span>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-card-quiz p-16 d-flex align-items-center gap-3">
                <div class="stat-icon-quiz bg-success-subtle text-success">
                    <i class="ri-user-star-line"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0">{{ number_format($this->stats['total_attempts']) }}</h5>
                    <span class="text-muted text-xs">Pengerjaan Peserta</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
        {{-- Card Header: Toolbar Filter & Search --}}
        <div class="card-header bg-white py-16 px-20 border-bottom">
            <div class="row g-2 align-items-center justify-content-between">
                {{-- Input Pencarian --}}
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0 text-muted">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" class="form-control form-control-sm bg-light border-start-0"
                            wire:model.live.debounce.300ms="search" placeholder="Cari judul kuis, bab, atau kelas...">
                        @if ($search)
                            <button class="btn btn-outline-secondary btn-sm" wire:click="$set('search', '')">
                                <i class="ri-close-line"></i>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Filter Kelas, Tipe, & Reset --}}
                <div class="col-md-8 d-flex align-items-center justify-content-md-end flex-wrap gap-2">
                    {{-- Filter Program Kelas --}}
                    <div style="min-width: 180px;">
                        <select class="form-select form-select-sm" wire:model.live="course_id">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($this->courses as $c)
                                <option value="{{ $c->id }}">{{ Str::limit($c->title, 28) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Tipe Evaluasi --}}
                    <div>
                        <select class="form-select form-select-sm" wire:model.live="type">
                            <option value="all">Semua Tipe</option>
                            <option value="chapter">Kuis Bab</option>
                            <option value="final">Ujian Akhir</option>
                        </select>
                    </div>

                    {{-- Per Page --}}
                    <div>
                        <select class="form-select form-select-sm" wire:model.live="perPage">
                            <option value="10">10 / hal</option>
                            <option value="25">25 / hal</option>
                            <option value="50">50 / hal</option>
                        </select>
                    </div>

                    @if ($search || $course_id || $type !== 'all')
                        <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1"
                            wire:click="resetFilters" title="Reset Semua Filter">
                            <i class="ri-refresh-line"></i>
                            <span>Reset</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card Body: Tabel Data Kuis --}}
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-quiz table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                            <th style="min-width: 240px;">Judul Evaluasi &amp; Kuis</th>
                            <th style="min-width: 200px;">Program Kelas &amp; Penempatan</th>
                            <th class="text-center" style="width: 150px;">Soal &amp; Skor</th>
                            <th class="text-center" style="width: 120px;">Partisipasi</th>
                            <th class="text-center" style="width: 120px;">Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($quizzes as $index => $quiz)
                            <tr wire:key="quiz-row-{{ $quiz->id }}">
                                {{-- Kolom 1: No --}}
                                <td class="text-center text-muted fw-semibold">
                                    {{ $quizzes->firstItem() + $index }}
                                </td>

                                {{-- Kolom 2: Aksi --}}
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        {{-- Tombol Detail Soal --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary p-1 px-2"
                                            wire:click="openDetail({{ $quiz->id }})" title="Lihat Butir Soal Kuis">
                                            <i class="ri-eye-line"></i>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2"
                                            wire:click="hookModalDelete({{ $quiz->id }}, '{{ addslashes($quiz->title) }}')"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete" title="Hapus Kuis">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </td>

                                {{-- Kolom 3: Judul Evaluasi & Kuis --}}
                                <td>
                                    <div class="fw-bold text-dark mb-1">
                                        <a href="javascript:void(0)" wire:click="openDetail({{ $quiz->id }})"
                                            class="text-dark text-decoration-none hover-primary">
                                            {{ $quiz->title }}
                                        </a>
                                    </div>
                                    @if ($quiz->description)
                                        <p class="text-muted text-xs mb-1 text-truncate" style="max-width: 320px;">
                                            {{ $quiz->description }}
                                        </p>
                                    @endif
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        @if ($quiz->time_limit_minutes)
                                            <span class="score-pill-badge">
                                                <i class="ri-time-line text-primary"></i>
                                                {{ $quiz->time_limit_minutes }} Menit
                                            </span>
                                        @else
                                            <span class="score-pill-badge text-muted">
                                                <i class="ri-time-line"></i> Tanpa Batas Waktu
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kolom 4: Program Kelas & Penempatan --}}
                                <td>
                                    <div class="fw-semibold text-dark text-xs mb-1">
                                        {{ $quiz->course?->title ?? '-' }}
                                    </div>
                                    @if ($quiz->isFinalQuiz())
                                        <span class="badge-type-final d-inline-flex align-items-center gap-1">
                                            <i class="ri-award-fill"></i> Ujian Akhir Kelas
                                        </span>
                                    @else
                                        <span class="badge-type-chapter d-inline-flex align-items-center gap-1">
                                            <i class="ri-booklet-line"></i> Bab:
                                            {{ Str::limit($quiz->chapter?->title ?? '-', 24) }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom 5: Soal & Skor --}}
                                <td class="text-center">
                                    <div class="fw-bold text-dark text-xs mb-1">
                                        {{ $quiz->questions_count }} Butir Soal
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-1 flex-wrap">
                                        <span class="badge bg-secondary-subtle text-secondary border text-xxs">
                                            Skor: {{ $quiz->total_score }}
                                        </span>
                                        <span class="badge bg-success-subtle text-success border text-xxs">
                                            KKM: {{ $quiz->passing_score }}%
                                        </span>
                                    </div>
                                </td>

                                {{-- Kolom 6: Partisipasi Peserta --}}
                                <td class="text-center">
                                    <div class="d-inline-flex align-items-center gap-1 fw-bold text-dark text-xs">
                                        <i class="ri-user-follow-line text-primary"></i>
                                        <span>{{ $quiz->attempts_count }}</span>
                                    </div>
                                    <div class="text-muted text-xxs">Peserta</div>
                                </td>

                                {{-- Kolom 7: Tanggal Dibuat --}}
                                <td class="text-center text-muted text-xs">
                                    {{ $quiz->created_at ? $quiz->created_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="py-4">
                                        <div class="mb-3">
                                            <i class="ri-draft-line text-muted" style="font-size: 48px;"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">Belum Ada Data Evaluasi</h6>
                                        <p class="text-muted text-xs mb-3">
                                            @if ($search || $course_id || $type !== 'all')
                                                Tidak ada kuis yang cocok dengan filter pencarian Anda.
                                            @else
                                                Mulai buat kuis bab atau ujian akhir kelas untuk menguji pemahaman
                                                peserta.
                                            @endif
                                        </p>
                                        <a href="{{ route('evaluasi.create') }}"
                                            class="btn btn-simple-gold btn-sm px-3" wire:navigate>
                                            <i class="ri-add-line me-1"></i> Tambah Evaluasi Baru
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Card Footer: Pagination --}}
        @if ($quizzes->hasPages())
            <div
                class="card-footer bg-white py-16 px-20 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                <span class="text-muted text-xs">
                    Menampilkan {{ $quizzes->firstItem() }} sampai {{ $quizzes->lastItem() }} dari
                    {{ $quizzes->total() }} evaluasi
                </span>
                <div>
                    {{ $quizzes->links() }}
                </div>
            </div>
        @endif
    </div>

    {{-- Detail Modal Butir Soal --}}
    @if ($isDetailOpen && $this->selectedQuiz)
        <div class="modal fade show d-block modal-detail-quiz" tabindex="-1"
            style="background: rgba(15, 23, 42, 0.6); z-index: 1055;">
            <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-light py-16 px-24 border-bottom">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h6 class="modal-title fw-bold text-dark mb-0 fs-6">
                                    {{ $this->selectedQuiz->title }}
                                </h6>
                                @if ($this->selectedQuiz->isFinalQuiz())
                                    <span class="badge-type-final">Ujian Akhir Kelas</span>
                                @else
                                    <span class="badge-type-chapter">Kuis Bab</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                Program: <strong>{{ $this->selectedQuiz->course?->title ?? '-' }}</strong>
                                @if ($this->selectedQuiz->chapter)
                                    &bull; Bab: {{ $this->selectedQuiz->chapter->title }}
                                @endif
                            </small>
                        </div>
                        <button type="button" class="btn-close" wire:click="closeDetail"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-24">
                        {{-- Ringkasan Parameter Kuis --}}
                        <div class="row g-2 mb-4 p-3 bg-light rounded-3">
                            <div class="col-sm-3 col-6 text-center border-end">
                                <span class="text-muted text-xxs d-block">TOTAL SOAL</span>
                                <strong class="text-dark fs-6">{{ $this->selectedQuiz->questions->count() }}</strong>
                            </div>
                            <div class="col-sm-3 col-6 text-center border-end">
                                <span class="text-muted text-xxs d-block">TOTAL SKOR</span>
                                <strong class="text-dark fs-6 text-gold">{{ $this->selectedQuiz->total_score }}
                                    Poin</strong>
                            </div>
                            <div class="col-sm-3 col-6 text-center border-end">
                                <span class="text-muted text-xxs d-block">KKM KELULUSAN</span>
                                <strong
                                    class="text-dark fs-6 text-success">{{ $this->selectedQuiz->passing_score }}%</strong>
                            </div>
                            <div class="col-sm-3 col-6 text-center">
                                <span class="text-muted text-xxs d-block">BATAS WAKTU</span>
                                <strong class="text-dark fs-6">
                                    {{ $this->selectedQuiz->time_limit_minutes ? $this->selectedQuiz->time_limit_minutes . ' Menit' : 'Bebas' }}
                                </strong>
                            </div>
                        </div>

                        {{-- Daftar Butir Pertanyaan & Kunci Jawaban --}}
                        <div class="mb-3">
                            <h6 class="fw-bold text-dark text-xs mb-3 text-uppercase letter-spacing-1">
                                Rincian Butir Soal ({{ $this->selectedQuiz->questions->count() }})
                            </h6>

                            @foreach ($this->selectedQuiz->questions as $qIndex => $question)
                                <div class="preview-question-card" wire:key="preview-q-{{ $question->id }}">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-navy text-white text-xxs fw-semibold px-2 py-1 radius-4">
                                            Soal #{{ $qIndex + 1 }}
                                        </span>
                                        <span
                                            class="badge bg-warning-subtle text-warning border border-warning text-xxs fw-bold px-2 py-1">
                                            Bobot: {{ $question->score }} Poin
                                        </span>
                                    </div>

                                    <div class="fw-semibold text-dark mb-3 fs-7">
                                        {{ $question->question_text }}
                                    </div>

                                    {{-- Daftar Opsi --}}
                                    <div class="d-flex flex-column gap-2 mb-3">
                                        @foreach ($question->options as $optIndex => $opt)
                                            @php
                                                $letter = chr(65 + $optIndex);
                                            @endphp
                                            <div
                                                class="preview-opt-item {{ $opt->is_correct ? 'is-correct-answer' : '' }}">
                                                <span
                                                    class="opt-letter-circle flex-shrink-0">{{ $letter }}</span>
                                                <span class="flex-grow-1">{{ $opt->option_text }}</span>
                                                @if ($opt->is_correct)
                                                    <span
                                                        class="badge bg-success text-white text-xxs d-flex align-items-center gap-1">
                                                        <i class="ri-check-line"></i> Kunci Jawaban
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Pembahasan Jawaban jika ada --}}
                                    @if ($question->explanation)
                                        <div
                                            class="p-2 px-3 bg-light border-start border-3 border-info rounded-end text-xs text-muted">
                                            <strong class="text-dark d-block mb-1"><i
                                                    class="ri-information-line text-info me-1"></i>
                                                Pembahasan:</strong>
                                            {{ $question->explanation }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Riwayat Pengerjaan Peserta Terakhir jika ada --}}
                        @if ($this->selectedQuiz->attempts->isNotEmpty())
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold text-dark text-xs mb-3 text-uppercase letter-spacing-1">
                                    Riwayat Peserta Terakhir ({{ $this->selectedQuiz->attempts->count() }})
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle text-xs mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Peserta</th>
                                                <th class="text-center">Nilai Skor</th>
                                                <th class="text-center">Persentase</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Waktu Pengerjaan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($this->selectedQuiz->attempts as $att)
                                                <tr>
                                                    <td>{{ $att->user?->name ?? 'Peserta' }}</td>
                                                    <td class="text-center fw-semibold">{{ $att->total_earned_score }}
                                                        / {{ $att->total_possible_score }}</td>
                                                    <td class="text-center">{{ number_format($att->percentage, 1) }}%
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($att->is_passed)
                                                            <span
                                                                class="badge bg-success-subtle text-success">Lulus</span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger">Tidak
                                                                Lulus</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center text-muted">
                                                        {{ $att->submitted_at ? $att->submitted_at->format('d/m/Y H:i') : '-' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light py-12 px-24 border-top">
                        <button type="button" class="btn btn-secondary btn-sm px-4" wire:click="closeDetail">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
