<div class="py-4" style="background: #f8fafc; min-height: 85vh;">
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

        .quiz-radio-indicator {
            width: 22px;
            height: 22px;
            min-width: 22px;
            border-radius: 50%;
            border: 2px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .option-choice-card.is-selected .quiz-radio-indicator,
        .quiz-radio-indicator.is-selected {
            border-color: #0d6efd;
            background: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
        }

        .quiz-radio-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ffffff;
            opacity: 0;
            transform: scale(0.4);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .option-choice-card.is-selected .quiz-radio-dot,
        .quiz-radio-indicator.is-selected .quiz-radio-dot {
            opacity: 1;
            transform: scale(1);
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
            from {
                opacity: 1;
            }

            to {
                opacity: 0.7;
            }
        }
    </style>
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
                    <a href="{{ route('peserta.materi', $quiz->course_id) }}"
                        class="btn btn-primary px-4 py-2 radius-8">
                        <i class="ri-arrow-left-line me-1"></i> Kembali ke Ruang Belajar
                    </a>
                </div>
            </div>

            {{-- STATE 2: INTRO / PENGANTAR KUIS --}}
        @elseif ($quizState === 'intro')
            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden my-4">
                {{-- Card Header --}}
                <div class="p-4 p-md-5 border-bottom bg-white">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 fs-8 rounded-pill">
                            <i class="ri-book-open-line me-1"></i> {{ $quiz->course?->title ?? 'Program Kelas' }}
                        </span>
                        @if ($quiz->isFinalQuiz())
                            <span
                                class="badge bg-warning-subtle text-warning border border-warning px-3 py-1 fs-8 rounded-pill fw-bold">
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
                <div class="card-body p-4 p-md-5">
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
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">Batas Kelulusan
                                    (KKM)</span>
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

                    {{-- Panduan Pengerjaan --}}
                    <div class="border rounded-3 p-3 p-md-4 mb-4 bg-white">
                        <h6 class="fw-bold text-dark mb-3 fs-7">
                            <i class="ri-information-line text-primary me-1"></i> Ketentuan &amp; Tata Cara Evaluasi:
                        </h6>
                        <ul class="text-muted fs-8 mb-0 ps-3 d-flex flex-column gap-2">
                            <li>Setiap soal berupa <strong>pilihan ganda</strong> dengan opsi jawaban dinamis.</li>
                            <li>Tiap soal memiliki bobot skor (1–20 poin) yang akan dijumlahkan bila jawaban benar.</li>
                            <li>Anda dapat berpindah antar-soal secara bebas menggunakan tombol navigasi maupun nomor
                                soal di panel samping.</li>
                            <li>
                                <strong class="text-danger">Penting (Single Attempt):</strong>
                                Evaluasi ini hanya dapat dikerjakan <strong>1 (satu) kali</strong> tanpa ada kesempatan
                                retake/pengulangan.
                            </li>
                            @if ($quiz->time_limit_minutes)
                                <li>Waktu akan otomatis berjalan mundur saat Anda menekan tombol mulai, dan jawaban akan
                                    terkumpul otomatis bila waktu habis.</li>
                            @endif
                        </ul>
                    </div>

                    {{-- Tombol Aksi Mulai --}}
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <a href="{{ route('peserta.materi', $quiz->course ?? $quiz->course_id) }}"
                            class="btn btn-outline-danger px-4 py-2 radius-8">
                            <i class="ri-arrow-left-line me-1"></i> Batal / Kembali ke Materi
                        </a>
                        <button type="button"
                            class="btn btn-simple-gold px-4 py-2 radius-8 fw-bold d-flex align-items-center gap-2 shadow-sm"
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

            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden my-4" x-data="{
                remainingSeconds: {{ $timeRemainingSeconds }},
                timerInterval: null,
                isSubmitting: false,
                formatTimer() {
                    let mins = Math.floor(this.remainingSeconds / 60);
                    let secs = this.remainingSeconds % 60;
                    return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                },
                init() {
                    window.__quizSubmitting = false;

                    if (this.timerInterval) {
                        clearInterval(this.timerInterval);
                    }
                    if (this.remainingSeconds > 0) {
                        this.timerInterval = setInterval(() => {
                            if (this.remainingSeconds > 0) {
                                this.remainingSeconds--;
                            } else {
                                clearInterval(this.timerInterval);
                                this.isSubmitting = true;
                                window.__quizSubmitting = true;
                                $wire.submitQuiz();
                            }
                        }, 1000);
                    }

                    // 1. Browser tab close / reload protection
                    const beforeUnloadHandler = (e) => {
                        if (this.isSubmitting || window.__quizSubmitting) return;
                        e.preventDefault();
                        e.returnValue = '';
                        return '';
                    };
                    window.addEventListener('beforeunload', beforeUnloadHandler);

                    // 2. Intercept navigation clicks outside the quiz player (navbar links, logo, etc.)
                    const clickHandler = (e) => {
                        if (this.isSubmitting || window.__quizSubmitting) return;

                        const link = e.target.closest('a');
                        if (!link) return;

                        const href = link.getAttribute('href');
                        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) {
                            return;
                        }

                        // Don't intercept clicks inside the quiz player card
                        if (this.$el.contains(link)) {
                            return;
                        }

                        const confirmed = confirm('Peringatan: Evaluasi kuis Anda sedang berlangsung dan timer waktu terus berjalan. Apakah Anda yakin ingin meninggalkan halaman evaluasi?');
                        if (!confirmed) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }

                        // User confirmed leaving: allow navigation without second prompt
                        this.isSubmitting = true;
                        window.__quizSubmitting = true;
                    };
                    document.addEventListener('click', clickHandler, true);

                    // 3. Intercept logout modal form submission
                    const formSubmitHandler = (e) => {
                        if (this.isSubmitting || window.__quizSubmitting) return;

                        const form = e.target;
                        if (form && (form.id === 'logout-form' || form.getAttribute('action')?.includes('logout') || form.classList.contains('simpel-modal-form'))) {
                            const confirmed = confirm('Peringatan: Evaluasi kuis Anda sedang berlangsung. Jika Anda keluar (logout), sesi kuis Anda akan dihentikan dan waktu akan terus berjalan di server. Apakah Anda yakin ingin keluar?');
                            if (!confirmed) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            }
                            this.isSubmitting = true;
                            window.__quizSubmitting = true;
                        }
                    };
                    document.addEventListener('submit', formSubmitHandler, true);

                    // 4. Browser history (back/forward button) guard
                    history.pushState(null, '', window.location.href);
                    const popstateHandler = () => {
                        if (this.isSubmitting || window.__quizSubmitting) return;

                        const confirmed = confirm('Peringatan: Evaluasi kuis Anda sedang berlangsung dan timer waktu terus berjalan. Apakah Anda yakin ingin meninggalkan halaman evaluasi?');
                        if (!confirmed) {
                            history.pushState(null, '', window.location.href);
                        } else {
                            this.isSubmitting = true;
                            window.__quizSubmitting = true;
                            history.back();
                        }
                    };
                    window.addEventListener('popstate', popstateHandler);

                    // 5. Custom event listener for smooth submission without dialog
                    const submittingHandler = () => {
                        this.isSubmitting = true;
                        window.__quizSubmitting = true;
                    };
                    window.addEventListener('quiz-submitting', submittingHandler);

                    // 6. Cleanup when component is unmounted / destroyed
                    this.$cleanup(() => {
                        if (this.timerInterval) {
                            clearInterval(this.timerInterval);
                        }
                        window.removeEventListener('beforeunload', beforeUnloadHandler);
                        document.removeEventListener('click', clickHandler, true);
                        document.removeEventListener('submit', formSubmitHandler, true);
                        window.removeEventListener('popstate', popstateHandler);
                        window.removeEventListener('quiz-submitting', submittingHandler);
                    });
                }
            }">

                {{-- Header Quiz Player --}}
                <div
                    class="p-20 px-md-24 border-bottom bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-navy text-white px-3 py-2 radius-8 fs-8 fw-bold">
                            Soal {{ $currentQuestionIndex + 1 }} dari {{ $this->questionsCount }}
                        </span>
                        <span
                            class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 radius-8 fs-8 fw-semibold">
                            Bobot: {{ $q?->score ?? 0 }} Poin
                        </span>
                    </div>

                    {{-- Timer Countdown jika kuis berdurasi --}}
                    @if ($quiz->time_limit_minutes)
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted text-xs d-none d-sm-inline">Sisa Waktu:</span>
                            <div class="timer-badge" :class="{ 'timer-warning': remainingSeconds < 300 }" wire:ignore>
                                <i class="ri-time-line"></i>
                                <span x-text="formatTimer()">--:--</span>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Body Quiz Player: Soal & Opsi Jawaban --}}
                <div class="card-body p-4 p-md-5">
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
                                            $chosenOptId = $userAnswers[$q->id] ?? ($userAnswers[(string) $q->id] ?? null);
                                            $isSelected = $chosenOptId !== null && (int) $chosenOptId === (int) $opt->id;
                                        @endphp
                                        <div class="option-choice-card d-flex align-items-center gap-3 {{ $isSelected ? 'is-selected' : '' }}"
                                            wire:click="selectOption({{ $q->id }}, {{ $opt->id }})"
                                            wire:key="opt-card-{{ $q->id }}-{{ $opt->id }}-{{ $isSelected ? '1' : '0' }}"
                                            style="border: 2px solid {{ $isSelected ? '#0d6efd' : '#e2e8f0' }}; border-radius: 12px; background: {{ $isSelected ? '#eff6ff' : '#ffffff' }}; padding: 14px 18px; cursor: pointer; user-select: none;">
                                            <span class="option-choice-letter"
                                                style="width: 32px; height: 32px; min-width: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; background: {{ $isSelected ? '#0d6efd' : '#e2e8f0' }}; color: {{ $isSelected ? '#ffffff' : '#1e293b' }}; flex-shrink: 0;">
                                                {{ $letter }}
                                            </span>
                                            <div class="flex-grow-1 fs-7 fw-medium text-dark">
                                                {{ $opt->option_text }}
                                            </div>
                                            <div class="flex-shrink-0 d-flex align-items-center">
                                                <div class="quiz-radio-indicator {{ $isSelected ? 'is-selected' : '' }}"
                                                    style="width: 22px; height: 22px; min-width: 22px; border-radius: 50%; border: 2px solid {{ $isSelected ? '#0d6efd' : '#cbd5e1' }}; background: {{ $isSelected ? '#0d6efd' : '#ffffff' }}; display: inline-flex; align-items: center; justify-content: center; box-shadow: {{ $isSelected ? '0 0 0 3px rgba(13, 110, 253, 0.2)' : 'none' }};">
                                                    <div class="quiz-radio-dot"
                                                        style="width: 8px; height: 8px; border-radius: 50%; background: #ffffff; opacity: {{ $isSelected ? '1' : '0' }}; transform: scale({{ $isSelected ? '1' : '0.4' }});"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Tombol Navigasi Soal Bawah --}}
                            <div
                                class="d-flex align-items-center justify-content-between pt-3 border-top flex-wrap gap-2">
                                <button type="button" class="btn btn-outline-secondary px-3 py-2 radius-8 fs-8"
                                    wire:click="prevQuestion" {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                                    <i class="ri-arrow-left-line me-1"></i> Soal Sebelumnya
                                </button>

                                <div class="d-flex align-items-center gap-2">
                                    @if ($currentQuestionIndex < $this->questionsCount - 1)
                                        <button type="button" class="btn btn-primary px-4 py-2 radius-8 fs-8"
                                            wire:click="nextQuestion">
                                            <span>Soal Selanjutnya</span> <i class="ri-arrow-right-line ms-1"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-success px-4 py-2 radius-8 fs-8 fw-bold d-inline-flex align-items-center gap-1"
                                            wire:click="promptSubmit" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="promptSubmit">
                                                <i class="ri-checkbox-circle-line me-1"></i> Selesaikan Evaluasi
                                            </span>
                                            <span wire:loading wire:target="promptSubmit">
                                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Membuka Konfirmasi...
                                            </span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Grid Navigator Nomor Soal --}}
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
                                            $isAnswered = array_key_exists($quest->id, $userAnswers) || array_key_exists((string) $quest->id, $userAnswers);
                                            $isActive = $idx === $currentQuestionIndex;
                                        @endphp
                                        <button type="button"
                                            class="nav-grid-btn {{ $isAnswered ? 'is-answered' : '' }} {{ $isActive ? 'is-active' : '' }}"
                                            wire:click="jumpToQuestion({{ $idx }})"
                                            wire:key="nav-btn-{{ $quest->id }}-{{ $isAnswered ? '1' : '0' }}-{{ $isActive ? '1' : '0' }}"
                                            title="Buka Soal #{{ $idx + 1 }}">
                                            {{ $idx + 1 }}
                                        </button>
                                    @endforeach
                                </div>

                                <div class="border-top pt-2 mt-2">
                                    <div class="d-flex align-items-center gap-2 text-xxs text-muted mb-1">
                                        <span class="d-inline-block rounded bg-success"
                                            style="width: 12px; height: 12px;"></span>
                                        <span>Sudah Dijawab</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-xxs text-muted">
                                        <span class="d-inline-block rounded border bg-white"
                                            style="width: 12px; height: 12px;"></span>
                                        <span>Belum Dijawab</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STATE 4: RESULT / HASIL EVALUASI (SINGLE ATTEMPT FINAL) --}}
        @elseif ($quizState === 'result' && $savedAttempt)
            <div class="card border-0 shadow-sm radius-16 bg-white overflow-hidden text-center my-4">
                {{-- Banner Status Kelulusan --}}
                <div
                    class="p-4 p-md-5 {{ $savedAttempt->is_passed ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} border-bottom">
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
                            Hasil evaluasi Anda telah melampaui standar batas nilai kelulusan (KKM:
                            {{ $quiz->passing_score }}%).
                        </p>
                    @else
                        <h3 class="fw-bold text-dark mb-1">Evaluasi Telah Selesai</h3>
                        <p class="text-muted fs-7 mb-0">
                            Nilai Anda belum mencapai standar kelulusan (KKM: {{ $quiz->passing_score }}%).
                        </p>
                    @endif
                </div>

                {{-- Rincian Nilai Skor --}}
                <div class="card-body p-4 p-md-5">
                    <div class="row g-3 justify-content-center mb-4">
                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">SKOR AKHIR</span>
                                <h3 class="fw-bold text-dark mb-0 fs-3">
                                    {{ $savedAttempt->total_earned_score }} <span class="text-muted fs-6">/
                                        {{ $savedAttempt->total_possible_score }}</span>
                                </h3>
                                <small class="text-muted text-xxs">Akumulasi Bobot Soal</small>
                            </div>
                        </div>

                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">PERSENTASE
                                    NILAI</span>
                                <h3
                                    class="fw-bold {{ $savedAttempt->is_passed ? 'text-success' : 'text-danger' }} mb-0 fs-3">
                                    {{ number_format($savedAttempt->percentage, 1) }}%
                                </h3>
                                <small class="text-muted text-xxs">Standar KKM: {{ $quiz->passing_score }}%</small>
                            </div>
                        </div>

                        <div class="col-sm-4 col-12">
                            <div class="p-3 bg-light rounded-3 border text-center">
                                <span class="text-muted text-xxs text-uppercase fw-semibold d-block">WAKTU
                                    SELESAI</span>
                                <h5 class="fw-bold text-dark mb-0 fs-6 pt-2">
                                    {{ $savedAttempt->submitted_at ? $savedAttempt->submitted_at->format('d M Y, H:i') : '-' }}
                                    WIB
                                </h5>
                                <small class="text-muted text-xxs">Tercatat Permanen</small>
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Single Attempt --}}
                    <div class="alert alert-secondary border-0 p-3 radius-12 text-xs text-muted mb-4 mx-auto"
                        style="max-width: 600px;">
                        <i class="ri-shield-check-line text-primary me-1"></i>
                        Evaluasi ini menerapkan kebijakan <strong>Single Attempt (tanpa retake)</strong> demi menjaga
                        integritas penilaian. Skor di atas telah tercatat permanen pada akun Anda.
                    </div>

                    {{-- Tombol Aksi Lanjut --}}
                    <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                        <a href="{{ route('peserta.materi', $quiz->course ?? $quiz->course_id) }}"
                            class="btn btn-primary px-4 py-2 radius-8 fw-semibold">
                            <i class="ri-book-open-line me-1"></i> Kembali ke Ruang Belajar Materi
                        </a>
                        <a href="{{ route('landing.kelas.detail', $quiz->course ?? $quiz->course_id) }}"
                            class="btn btn-outline-secondary px-4 py-2 radius-8">
                            <i class="ri-arrow-left-line me-1"></i> Detail Kelas
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Konfirmasi Selesai & Kumpulkan Kuis --}}
    @if ($showSubmitConfirmation)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(15, 23, 42, 0.6); z-index: 1060;"
            role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;" role="document">
                <div class="modal-content border-0 radius-20 shadow-lg overflow-hidden bg-white position-relative">
                    {{-- Close Button --}}
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                        wire:click="cancelSubmit" aria-label="Close"
                        style="z-index: 10; font-size: 11px; cursor: pointer;"></button>

                    {{-- Modal Body --}}
                    <div class="modal-body p-4 text-center">
                        {{-- Icon Badge --}}
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 64px; height: 64px; background-color: #fef3c7; color: #d97706; font-size: 28px; box-shadow: 0 8px 24px rgba(217, 119, 6, 0.2);">
                            <i class="ri-question-mark"></i>
                        </div>

                        {{-- Title --}}
                        <h5 class="fw-bold text-dark mb-2" style="font-size: 18px;">
                            Konfirmasi Selesai &amp; Kumpulkan
                        </h5>

                        {{-- Message with soft highlight box --}}
                        @php
                            $unanswered = $this->questionsCount - $this->answeredCount;
                        @endphp
                        <div class="bg-light p-3 rounded-12 border mb-4 text-start {{ $unanswered > 0 ? 'bg-warning-subtle text-dark border-warning' : '' }}">
                            <p class="text-muted fs-8 mb-2 line-height-base text-dark">
                                Anda telah menjawab <strong>{{ $this->answeredCount }} dari {{ $this->questionsCount }}</strong> butir pertanyaan.
                            </p>
                            @if ($unanswered > 0)
                                <div class="text-danger fw-semibold fs-8 mb-2">
                                    <i class="ri-error-warning-line me-1"></i> Masih ada <strong>{{ $unanswered }}</strong> soal yang belum Anda jawab!
                                </div>
                            @endif
                            <div class="pt-2 border-top text-muted" style="font-size: 11.5px; line-height: 1.45;">
                                <strong>Perhatian:</strong> Kuis ini menerapkan sistem <em>Single Attempt</em>. Jawaban yang dikumpulkan bersifat final dan tidak dapat diubah kembali.
                            </div>
                        </div>

                        {{-- Action Buttons (50/50 Balanced) --}}
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="btn btn-outline-secondary w-50 py-2 radius-10 fw-semibold fs-8"
                                wire:click="cancelSubmit">
                                Periksa Lagi
                            </button>
                            <button type="button" class="btn btn-success w-50 py-2 radius-10 fw-semibold fs-8 d-inline-flex align-items-center justify-content-center gap-1 shadow-sm"
                                wire:click="submitQuiz"
                                @click="window.dispatchEvent(new CustomEvent('quiz-submitting'))"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitQuiz">
                                    <i class="ri-check-line"></i> Ya, Kumpulkan
                                </span>
                                <span wire:loading wire:target="submitQuiz">
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
