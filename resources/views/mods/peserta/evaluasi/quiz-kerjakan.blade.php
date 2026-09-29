@push('css')
    <style>
        .quiz-container {
            max-width: 960px;
            margin: 0 auto;
        }

        .option-choice-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .option-choice-card:hover {
            border-color: #94a3b8;
            background: #f8fafc;
        }

        .option-choice-card.is-selected {
            border-color: #0d6efd;
            background: #eff6ff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
        }

        .option-choice-letter {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
            background: #e2e8f0;
            color: #1e293b;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .option-choice-card.is-selected .option-choice-letter {
            background: #0d6efd;
            color: #ffffff;
        }

        .nav-grid-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .nav-grid-btn:hover {
            background: #f1f5f9;
        }

        .nav-grid-btn.is-active {
            border: 2px solid #0d6efd !important;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
        }

        .nav-grid-btn.is-answered {
            background: #198754 !important;
            color: #ffffff !important;
            border-color: #198754 !important;
        }

        .nav-grid-btn.is-answered.is-active {
            border: 2px solid #0d6efd !important;
            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.4);
        }

        .timer-badge {
            background: #0f172a;
            color: #f8fafc;
            border-radius: 8px;
            padding: 6px 14px;
            font-family: 'Courier New', Courier, monospace;
            font-weight: 700;
            font-size: 16px;
            letter-spacing: 1px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .timer-warning {
            background: #dc3545 !important;
            animation: pulse-timer 1s infinite alternate;
        }

        @keyframes pulse-timer {
            from { opacity: 1; }
            to { opacity: 0.7; }
        }
    </style>
@endpush

<div class="py-4" style="background: #f8fafc; min-height: 85vh;">
    <div class="container quiz-container">

        {{-- STATE 1: TERKUNCI (LOCKED PREREQUISITE) --}}
        @if ($quizState === 'locked')
            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden text-center p-4 p-md-5 my-4">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle"
                        style="width: 72px; height: 72px;">
                        <i class="ri-lock-2-fill fs-1"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-2">Evaluasi Belum Dapat Diakses</h4>
                <p class="text-muted mx-auto mb-4" style="max-width: 540px;">
                    {{ $lockedReason }}
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('peserta.materi', $quiz->course_id) }}" class="btn btn-primary px-4 py-2 radius-8">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Ruang Belajar
                    </a>
                </div>
            </div>

        {{-- STATE 2: INTRO / PENGANTAR KUIS --}}
        @elseif ($quizState === 'intro')
            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden my-4">
                {{-- Card Header --}}
                <div class="p-24 border-bottom bg-white">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fs-8 rounded-pill">
                            <i class="ri-book-open-line me-1"></i> {{ $quiz->course?->title ?? 'Program Kelas' }}
                        </span>
                        @if ($quiz->isFinalQuiz())
                            <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-1 fs-8 rounded-pill fw-bold">
                                <i class="ri-award-fill me-1"></i> Ujian Akhir Kelas (Final Quiz)
                            </span>
                        @else
                            <span class="badge bg-info-subtle text-info border border-info px-3 py-1 fs-8 rounded-pill">
                                <i class="ri-booklet-line me-1"></i> Bab: {{ $quiz->chapter?->title ?? '-' }}
                            </span>
                        @endif
                    </div>
                    <h3 class="fw-bold text-dark mb-1 fs-4">{{ $quiz->title }}</h3>
                    @if ($quiz->description)
                        <p class="text-muted fs-7 mb-0">{{ $quiz->description }}</p>
                    @endif
                </div>

                {{-- Card Body --}}
                <div class="card-body p-24 p-md-32">
                    {{-- 4 Parameter Metrik Kuis --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-3 col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">Jumlah Soal</span>
                                <h4 class="fw-bold text-dark mb-0 fs-5">{{ $this->questionsCount }} Soal</h4>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">Total Skor</span>
                                <h4 class="fw-bold text-primary mb-0 fs-5">{{ $quiz->total_score }} Poin</h4>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">Batas Kelulusan (KKM)</span>
                                <h4 class="fw-bold text-success mb-0 fs-5">{{ $quiz->passing_score }}%</h4>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="p-3 bg-light rounded-3 text-center border">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">Durasi Waktu</span>
                                <h4 class="fw-bold text-dark mb-0 fs-5">
                                    {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . ' Menit' : 'Bebas' }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    {{-- Panduan Pengerjaan ala Dicoding --}}
                    <div class="border rounded-3 p-3 p-md-4 mb-4 bg-white">
                        <h6 class="fw-bold text-dark mb-3 fs-7">
                            <i class="ri-information-line text-primary me-1"></i> Ketentuan &amp; Tata Cara Evaluasi:
                        </h6>
                        <ul class="text-muted fs-8 mb-0 ps-3 d-flex flex-column gap-2">
                            <li>Setiap soal berupa <strong>pilihan ganda</strong> dengan opsi jawaban dinamis.</li>
                            <li>Tiap soal memiliki bobot skor (1–20 poin) yang akan dijumlahkan bila jawaban benar.</li>
                            <li>Anda dapat berpindah antar-soal secara bebas menggunakan tombol navigasi maupun nomor soal di panel samping.</li>
                            <li>
                                <strong class="text-danger">Penting (Single Attempt):</strong>
                                Evaluasi ini hanya dapat dikerjakan <strong>1 (satu) kali</strong> tanpa ada kesempatan retake/pengulangan.
                            </li>
                            @if ($quiz->time_limit_minutes)
                                <li>Waktu akan otomatis berjalan mundur saat Anda menekan tombol mulai, dan jawaban akan terkumpul otomatis bila waktu habis.</li>
                            @endif
                        </ul>
                    </div>

                    {{-- Tombol Aksi Mulai --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <a href="{{ route('peserta.materi', $quiz->course_id) }}" class="btn btn-outline-secondary px-4 py-2 radius-8">
                            <i class="ri-arrow-left-line me-1"></i> Batal / Kembali ke Materi
                        </a>
                        <button type="button" class="btn btn-simple-gold px-4 py-2 radius-8 fw-bold d-flex align-items-center gap-2 shadow-sm"
                            wire:click="startQuiz">
                            <span>Mulai Kerjakan Evaluasi</span>
                            <i class="ri-arrow-right-line fs-5"></i>
                        </button>
                    </div>
                </div>
            </div>

        {{-- STATE 3: PLAYING (PENGERJAAN KUIS AKTIF ALA DICODING) --}}
        @elseif ($quizState === 'playing')
            @php
                $q = $this->currentQuestion;
            @endphp

            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden my-4"
                x-data="{
                    remainingSeconds: {{ $timeRemainingSeconds }},
                    timerInterval: null,
                    formatTimer() {
                        let mins = Math.floor(this.remainingSeconds / 60);
                        let secs = this.remainingSeconds % 60;
                        return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                    },
                    init() {
                        if (this.remainingSeconds > 0) {
                            this.timerInterval = setInterval(() => {
                                if (this.remainingSeconds > 0) {
                                    this.remainingSeconds--;
                                } else {
                                    clearInterval(this.timerInterval);
                                    $wire.submitQuiz();
                                }
                            }, 1000);
                        }
                    }
                }">

                {{-- Header Quiz Player --}}
                <div class="p-20 px-md-24 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-navy text-white px-3 py-2 radius-8 fs-8 fw-bold">
                            Soal {{ $currentQuestionIndex + 1 }} dari {{ $this->questionsCount }}
                        </span>
                        <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 radius-8 fs-8 fw-semibold">
                            Bobot: {{ $q?->score ?? 0 }} Poin
                        </span>
                    </div>

                    {{-- Timer Countdown jika kuis berdurasi --}}
                    @if ($quiz->time_limit_minutes)
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-xs d-none d-sm-inline">Sisa Waktu:</span>
                            <div class="timer-badge" :class="{ 'timer-warning': remainingSeconds < 300 }">
                                <i class="ri-time-line"></i>
                                <span x-text="formatTimer()">--:--</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Body Quiz Player: Soal & Opsi Jawaban --}}
                <div class="card-body p-24 p-md-32">
                    <div class="row g-4">
                        {{-- Kolom Kiri: Pertanyaan & Pilihan Jawaban --}}
                        <div class="col-lg-8 col-12">
                            @if ($q)
                                <div class="mb-4">
                                    <h5 class="fw-bold text-dark lh-base fs-6" style="white-space: pre-line;">
                                        {{ $q->question_text }}
                                    </h5>
                                </div>

                                {{-- Daftar Opsi Pilihan Ganda --}}
                                <div class="d-flex flex-column gap-3 mb-4">
                                    @foreach ($q->options as $optIndex => $opt)
                                        @php
                                            $letter = chr(65 + $optIndex);
                                            $isSelected = isset($userAnswers[$q->id]) && $userAnswers[$q->id] === $opt->id;
                                        @endphp
                                        <div class="option-choice-card d-flex align-items-center gap-3 {{ $isSelected ? 'is-selected' : '' }}"
                                            wire:click="selectOption({{ $q->id }}, {{ $opt->id }})"
                                            wire:key="opt-{{ $opt->id }}">
                                            <span class="option-choice-letter">{{ $letter }}</span>
                                            <div class="flex-grow-1 fs-7 fw-medium text-dark">
                                                {{ $opt->option_text }}
                                            </div>
                                            <div>
                                                <input class="form-check-input" type="radio"
                                                    name="q_{{ $q->id }}"
                                                    value="{{ $opt->id }}"
                                                    {{ $isSelected ? 'checked' : '' }}
                                                    style="pointer-events: none;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Tombol Navigasi Soal Bawah --}}
                            <div class="d-flex align-items-center justify-content-between pt-3 border-top flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary px-3 py-2 radius-8 fs-8"
                                    wire:click="prevQuestion"
                                    {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line me-1"></i> Soal Sebelumnya
                                </button>

                                <div class="d-flex align-items-center gap-2">
                                    @if ($currentQuestionIndex < $this->questionsCount - 1)
                                        <button type="button" class="btn btn-primary px-4 py-2 radius-8 fs-8"
                                            wire:click="nextQuestion">
                                            <span>Soal Selanjutnya</span> <i class="ri-arrow-right-line ms-1"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success px-4 py-2 radius-8 fs-8 fw-bold"
                                            wire:click="promptSubmit">
                                            <i class="ri-checkbox-circle-line me-1"></i> Selesaikan Evaluasi
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Grid Navigator Nomor Soal ala Dicoding --}}
                        <div class="col-lg-4 col-12">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <span class="fw-bold text-dark text-xs text-uppercase letter-spacing-1">
                                        Daftar Nomor Soal
                                    </span>
                                    <span class="badge bg-secondary-subtle text-secondary text-xs">
                                        {{ $this->answeredCount }}/{{ $this->questionsCount }} Terjawab
                                    </span>
                                </div>

                                {{-- Grid Buttons --}}
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach ($quiz->questions as $idx => $quest)
                                        @php
                                            $isAnswered = isset($userAnswers[$quest->id]);
                                            $isActive = $idx === $currentQuestionIndex;
                                        @endphp
                                        <button type="button"
                                            class="nav-grid-btn {{ $isAnswered ? 'is-answered' : '' }} {{ $isActive ? 'is-active' : '' }}"
                                            wire:click="jumpToQuestion({{ $idx }})"
                                            title="Buka Soal #{{ $idx + 1 }}">
                                            {{ $idx + 1 }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="border-top pt-2 mt-2">
                                    <div class="d-flex align-items-center gap-2 text-xxs text-muted mb-1">
                                        <span class="d-inline-block rounded bg-success" style="width: 12px; height: 12px;"></span>
                                        <span>Sudah Dijawab</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-xxs text-muted mb-3">
                                        <span class="d-inline-block rounded border bg-white" style="width: 12px; height: 12px;"></span>
                                        <span>Belum Dijawab</span>
                                    </div>

                                    <button type="button" class="btn btn-danger w-100 py-2 radius-8 fs-8 fw-semibold"
                                        wire:click="promptSubmit">
                                        <i class="ri-check-double-line me-1"></i> Kumpulkan Jawaban
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal Konfirmasi Pengumpulan Jawaban --}}
            @if ($showSubmitConfirmation)
                <div class="modal fade show d-block" tabindex="-1" style="background: rgba(15, 23, 42, 0.65); z-index: 1060;">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
                        <div class="modal-content border-0 radius-16 shadow-lg overflow-hidden">
                            <div class="modal-body p-4 text-center">
                                <div class="mb-3">
                                    <div class="d-inline-flex align-items-center justify-content-center bg-warning-subtle text-warning rounded-circle"
                                        style="width: 64px; height: 64px;">
                                        <i class="ri-question-mark fs-2"></i>
                                    </div>
                                </div>

                                <h5 class="fw-bold text-dark mb-2">Konfirmasi Selesai &amp; Kumpulkan</h5>

                                <p class="text-muted fs-8 mb-3">
                                    Anda telah menjawab <strong>{{ $this->answeredCount }}</strong> dari <strong>{{ $this->questionsCount }}</strong> butir pertanyaan.
                                    @if ($this->answeredCount < $this->questionsCount)
                                        <span class="text-danger d-block mt-1 fw-semibold">
                                            Masih ada {{ $this->questionsCount - $this->answeredCount }} soal yang belum Anda jawab!
                                        </span>
                                    @endif
                                </p>

                                <div class="alert alert-warning border-0 p-2 text-xxs text-start mb-4">
                                    <i class="ri-alert-line me-1"></i>
                                    <strong>Perhatian:</strong> Kuis ini menerapkan sistem Single Attempt. Jawaban yang dikumpulkan bersifat final dan tidak dapat diubah kembali.
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-outline-secondary w-50 py-2 radius-8 fs-8"
                                        wire:click="cancelSubmit">
                                        Periksa Lagi
                                    </button>
                                    <button type="button" class="btn btn-success w-50 py-2 radius-8 fs-8 fw-bold"
                                        wire:click="submitQuiz"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove>Ya, Kumpulkan</span>
                                        <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        {{-- STATE 4: RESULT / HASIL EVALUASI (SINGLE ATTEMPT FINAL) --}}
        @elseif ($quizState === 'result' && $savedAttempt)
            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden text-center my-4">
                {{-- Banner Status Kelulusan --}}
                <div class="p-4 p-md-5 {{ $savedAttempt->is_passed ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border-bottom">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle {{ $savedAttempt->is_passed ? 'bg-success text-white' : 'bg-warning text-white' }}"
                            style="width: 80px; height: 80px;">
                            @if ($savedAttempt->is_passed)
                                <i class="ri-checkbox-circle-line" style="font-size: 42px;"></i>
                            @else
                                <i class="ri-error-warning-line" style="font-size: 42px;"></i>
                            @endif
                        </div>
                    </div>

                    @if ($savedAttempt->is_passed)
                        <h3 class="fw-bold text-success mb-1">Selamat! Anda Lulus Evaluasi</h3>
                        <p class="text-success-emphasis fs-7 mb-0">
                            Hasil evaluasi Anda telah melampaui standar batas nilai kelulusan (KKM: {{ $quiz->passing_score }}%).
                        </p>
                    @else
                        <h3 class="fw-bold text-dark mb-1">Evaluasi Telah Selesai</h3>
                        <p class="text-muted fs-7 mb-0">
                            Nilai Anda belum mencapai standar kelulusan (KKM: {{ $quiz->passing_score }}%).
                        </p>
                    @endif
                </div>

                {{-- Rincian Nilai Skor --}}
                <div class="card-body p-24 p-md-40">
                    <div class="row g-3 justify-content-center mb-4">
                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">SKOR AKHIR</span>
                                <h3 class="fw-bold text-dark mb-0 fs-3">
                                    {{ $savedAttempt->total_earned_score }} <span class="text-muted fs-6">/ {{ $savedAttempt->total_possible_score }}</span>
                                </h3>
                                <small class="text-muted text-xxs">Akumulasi Bobot Soal</small>
                            </div>
                        </div>

                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">PERSENTASE NILAI</span>
                                <h3 class="fw-bold {{ $savedAttempt->is_passed ? 'text-success' : 'text-danger' }} mb-0 fs-3">
                                    {{ number_format($savedAttempt->percentage, 1) }}%
                                </h3>
                                <small class="text-muted text-xxs">Standar KKM: {{ $quiz->passing_score }}%</small>
                            </div>
                        </div>

                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">WAKTU SELESAI</span>
                                <h5 class="fw-bold text-dark mb-0 fs-6 pt-2">
                                    {{ $savedAttempt->submitted_at ? $savedAttempt->submitted_at->format('d M Y, H:i') : '-' }} WIB
                                </h5>
                                <small class="text-muted text-xxs">Tercatat Permanen</small>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Single Attempt --}}
                    <div class="alert alert-secondary border-0 p-3 radius-12 text-xs text-muted mb-4 mx-auto" style="max-width: 600px;">
                        <i class="ri-shield-check-line text-primary me-1"></i>
                        Evaluasi ini menerapkan kebijakan <strong>Single Attempt (tanpa retake)</strong> demi menjaga integritas penilaian. Skor di atas telah tercatat permanen pada akun Anda.
                    </div>

                    {{-- Tombol Aksi Lanjut --}}
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('peserta.materi', $quiz->course_id) }}" class="btn btn-primary px-4 py-2 radius-8 fw-semibold">
                            <i class="ri-book-open-line me-1"></i> Kembali ke Ruang Belajar Materi
                        </a>
                        <a href="{{ route('landing.kelas.detail', $quiz->course_id) }}" class="btn btn-outline-secondary px-4 py-2 radius-8">
                            <i class="ri-arrow-left-line me-1"></i> Detail Kelas
                        </a>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
