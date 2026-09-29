@push('css')
    <style>
        .simpel-card .form-control:focus,
        .simpel-card .form-select:focus {
            border-color: #f3bc42 !important;
            box-shadow: 0 0 0 3px rgba(243, 188, 66, 0.22) !important;
        }

        .question-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #ffffff;
            transition: all 0.2s ease-in-out;
        }

        .question-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }

        .option-item {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }

        .option-item.is-correct {
            background: #f0fdf4;
            border-color: #86efac;
        }

        .option-letter {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            background: #e2e8f0;
            color: #1e293b;
        }

        .option-item.is-correct .option-letter {
            background: #22c55e;
            color: #ffffff;
        }

        .target-type-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 12px;
        }

        .target-type-card {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            user-select: none;
            margin-bottom: 0 !important;
        }

        .target-type-card:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .target-type-card.is-active {
            border-color: #0d6efd;
            background: #f0f7ff;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.08);
        }

        .target-type-card.is-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .target-type-radio {
            width: 18px;
            height: 18px;
            margin: 0 !important;
            cursor: pointer;
            flex-shrink: 0;
            accent-color: #0d6efd;
        }

        .target-type-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
    </style>
@endpush

<div>
    {{-- Header & Breadcrumb Navigation --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <div>
            <h5 class="fw-bold text-dark mb-1">Tambah Evaluasi &amp; Kuis</h5>
            <p class="text-muted mb-0">Rancang kuis per bab atau ujian akhir kelas dengan bobot skor dinamis ala
                Dicoding.</p>
        </div>
    </div>

    {{-- Main Form --}}
    <form wire:submit="formSubmit">
        {{-- Card 1: Target Kelas & Penempatan --}}
        <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Target Kelas &amp; Penempatan Evaluasi</h6>
                    <small class="text-muted">Pilih kelas dan tentukan apakah evaluasi ini untuk Bab tertentu atau Ujian
                        Akhir Kelas.</small>
                </div>
                <span class="simpel-badge simpel-badge-navy">Langkah 1</span>
            </div>

            <div class="card-body p-24 p-md-32">
                <div class="row g-3">
                    {{-- Pilih Kelas --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Pilih Program Kelas <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('course_id') is-invalid @enderror"
                            wire:model.live="course_id">
                            <option value="">-- Pilih Kelas Terdaftar --</option>
                            @foreach ($this->courses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->title }} ({{ ucfirst($course->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Target Penempatan --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-dark mb-2">
                            Tipe Penempatan Evaluasi <span class="text-danger">*</span>
                        </label>
                        <div class="target-type-selector">
                            {{-- Pilihan 1: Kuis Bab Materi --}}
                            <label class="target-type-card {{ $target_type === 'chapter' ? 'is-active' : '' }}"
                                for="target_chapter">
                                <input class="target-type-radio" type="radio" value="chapter" id="target_chapter"
                                    wire:model.live="target_type">
                                <div class="target-type-icon bg-primary-subtle text-primary">
                                    <i class="ri-book-read-line"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark text-xs mb-0">Kuis Bab Materi</div>
                                    <span class="text-muted text-xxs d-block">Evaluasi per bab modul</span>
                                </div>
                            </label>

                            {{-- Pilihan 2: Ujian Akhir Kelas --}}
                            <label
                                class="target-type-card {{ $target_type === 'final' ? 'is-active' : '' }} {{ $this->hasFinalQuiz ? 'is-disabled' : '' }}"
                                for="target_final">
                                <input class="target-type-radio" type="radio" value="final" id="target_final"
                                    wire:model.live="target_type" @disabled($this->hasFinalQuiz)>
                                <div class="target-type-icon bg-warning-subtle text-warning">
                                    <i class="ri-award-line"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-bold text-dark text-xs mb-0">
                                        Ujian Akhir Kelas
                                        @if ($this->hasFinalQuiz)
                                            <span class="badge bg-danger text-white text-xxs ms-1">Sudah Ada</span>
                                        @endif
                                    </div>
                                    <span class="text-muted text-xxs d-block">Final quiz kelulusan kelas</span>
                                </div>
                            </label>
                        </div>
                        @error('target_type')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pilih Bab (Jika Tipe Chapter) --}}
                    @if ($target_type === 'chapter')
                        <div class="col-12">
                            <label class="form-label text-xs fw-semibold text-dark mb-1">
                                Pilih Bab Materi <span class="text-danger">*</span>
                            </label>
                            @if (!$course_id)
                                <div class="form-control text-muted bg-light">Silakan pilih kelas terlebih dahulu untuk
                                    memuat daftar bab.</div>
                            @elseif ($this->chapters->isEmpty())
                                <div class="alert alert-warning py-2 px-3 text-xs mb-0 d-flex align-items-center gap-2">
                                    <i class="ri-alert-line fs-6"></i>
                                    <span>Kelas ini belum memiliki bab silabus kurikulum. Silakan tambahkan bab terlebih
                                        dahulu di modul kelola materi.</span>
                                </div>
                            @else
                                <select class="form-select @error('chapter_id') is-invalid @enderror"
                                    wire:model.live="chapter_id">
                                    <option value="">-- Pilih Bab untuk Kuis Ini --</option>
                                    @foreach ($this->chapters as $ch)
                                        <option value="{{ $ch->id }}" @disabled($ch->quiz !== null)>
                                            Bab {{ $ch->order }}: {{ $ch->title }}
                                            {{ $ch->quiz ? '— (Sudah memiliki kuis)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                            @error('chapter_id')
                                <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    @else
                        <div class="col-12">
                            <div class="alert alert-info py-2 px-3 text-xs mb-0 d-flex align-items-center gap-2">
                                <i class="ri-information-line fs-6"></i>
                                <span><strong>Ujian Akhir Kelas (Final Quiz)</strong> ditempatkan di akhir kurikulum.
                                    Peserta wajib menyelesaikan seluruh materi sebelum dapat mengakses ujian ini.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Informasi Detail Kuis & Aturan --}}
        <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Detail Judul &amp; Parameter Kelulusan</h6>
                    <small class="text-muted">Tentukan judul kuis, batas waktu pengerjaan, dan standar KKM.</small>
                </div>
                <span class="simpel-badge simpel-badge-navy">Langkah 2</span>
            </div>

            <div class="card-body p-24 p-md-32">
                <div class="row g-3">
                    {{-- Judul Evaluasi --}}
                    <div class="col-md-12">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Judul Evaluasi / Kuis <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                            wire:model="title" placeholder="Contoh: Kuis Bab 1: Dasar Tata Naskah Dinas Elektronik">
                        @error('title')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi Petunjuk --}}
                    <div class="col-12">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Petunjuk Pengerjaan (Opsional)
                        </label>
                        <textarea rows="2" class="form-control @error('description') is-invalid @enderror" wire:model="description"
                            placeholder="Tuliskan petunjuk pengerjaan kuis untuk peserta..."></textarea>
                        @error('description')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Batas Waktu (Menit) --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Batas Waktu Pengerjaan (Menit)
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="ri-time-line"></i></span>
                            <input type="number" min="1" max="360"
                                class="form-control @error('time_limit_minutes') is-invalid @enderror"
                                wire:model="time_limit_minutes" placeholder="30">
                            <span class="input-group-text bg-light text-muted">Menit</span>
                        </div>
                        <small class="text-muted d-block mt-1">Kosongkan jika pengerjaan kuis tanpa batasan waktu
                            (fleksibel).</small>
                        @error('time_limit_minutes')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KKM Nilai Kelulusan --}}
                    <div class="col-md-6">
                        <label class="form-label text-xs fw-semibold text-dark mb-1">
                            Batas Nilai Kelulusan (KKM) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="ri-percent-line"></i></span>
                            <input type="number" min="0" max="100"
                                class="form-control @error('passing_score') is-invalid @enderror"
                                wire:model="passing_score" placeholder="70">
                            <span class="input-group-text bg-light text-muted">Poin (0 - 100)</span>
                        </div>
                        <small class="text-muted d-block mt-1">Skor minimum yang harus diraih peserta agar dinyatakan
                            lulus.</small>
                        @error('passing_score')
                            <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Builder Butir Soal & Pilihan Ganda Dinamis --}}
        <div class="card simpel-card border-0 shadow-sm radius-16 mb-24">
            <div
                class="card-header bg-white py-16 px-24 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark mb-0 fs-6">Butir Soal &amp; Pilihan Ganda Dinamis</h6>
                    <small class="text-muted">Setiap soal dapat memiliki bobot skor 1–20 poin dan 2–5 opsi pilihan
                        jawaban.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary text-white px-3 py-2 rounded-pill fs-8">
                        <i class="ri-questionnaire-line me-1"></i>{{ count($questions) }} Butir Soal
                    </span>
                    <span class="badge bg-gold text-navy fw-bold px-3 py-2 rounded-pill fs-8">
                        <i class="ri-trophy-line me-1"></i>Total Skor: {{ $this->totalScore }} Poin
                    </span>
                </div>
            </div>

            <div class="card-body p-24 p-md-32">
                @error('questions')
                    <div class="alert alert-danger py-2 px-3 text-xs mb-3">{{ $message }}</div>
                @enderror

                {{-- Loop Daftar Pertanyaan --}}
                <div class="d-flex flex-column gap-4">
                    @php $letters = ['A', 'B', 'C', 'D', 'E']; @endphp

                    @foreach ($questions as $qIndex => $question)
                        <div class="question-card p-20 p-md-24 shadow-xs" wire:key="question-{{ $qIndex }}">
                            {{-- Header Pertanyaan --}}
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="badge rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                                        style="width: 28px; height: 28px; background: #071a33; color: #f3bc42;">
                                        {{ $qIndex + 1 }}
                                    </span>
                                    <strong class="text-navy fs-6">Pertanyaan Nomor {{ $qIndex + 1 }}</strong>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    {{-- Input Skor Soal (1 - 20) --}}
                                    <div class="d-flex align-items-center gap-1">
                                        <label class="text-xs text-muted mb-0 fw-semibold text-nowrap">Bobot
                                            Skor:</label>
                                        <input type="number" min="1" max="20"
                                            class="form-control form-control-sm text-center fw-bold"
                                            style="width: 70px;"
                                            wire:model.live="questions.{{ $qIndex }}.score">
                                        <span class="text-xs text-muted">Poin</span>
                                    </div>

                                    {{-- Tombol Hapus Pertanyaan --}}
                                    @if (count($questions) > 1)
                                        <button type="button" class="btn btn-outline-danger btn-sm px-2 py-1 ms-2"
                                            wire:click="removeQuestion({{ $qIndex }})"
                                            title="Hapus Pertanyaan Ini">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>

                            @error("questions.{$qIndex}.score")
                                <div class="alert alert-danger py-1 px-2 text-xs mb-2">{{ $message }}</div>
                            @enderror

                            {{-- Teks Pertanyaan --}}
                            <div class="mb-3">
                                <label class="form-label text-xs fw-semibold text-dark mb-1">
                                    Teks Pertanyaan <span class="text-danger">*</span>
                                </label>
                                <textarea rows="3" class="form-control @error("questions.{$qIndex}.question_text") is-invalid @enderror"
                                    wire:model="questions.{{ $qIndex }}.question_text"
                                    placeholder="Tuliskan rumusan butir pertanyaan di sini..."></textarea>
                                @error("questions.{$qIndex}.question_text")
                                    <div class="invalid-feedback d-block text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Opsi Pilihan Jawaban (Dinamis 2-5 opsi) --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label text-xs fw-semibold text-dark mb-0">
                                        Pilihan Jawaban (Centang radio untuk menentukan Kunci Jawaban Benar) <span
                                            class="text-danger">*</span>
                                    </label>
                                    <span class="text-xs text-muted">{{ count($question['options']) }} Opsi</span>
                                </div>

                                @error("questions.{$qIndex}.options")
                                    <div class="alert alert-danger py-1 px-2 text-xs mb-2">{{ $message }}</div>
                                @enderror

                                <div class="d-flex flex-column gap-2">
                                    @foreach ($question['options'] as $oIndex => $option)
                                        <div class="option-item d-flex align-items-center gap-2 {{ !empty($option['is_correct']) ? 'is-correct' : '' }}"
                                            wire:key="q-{{ $qIndex }}-opt-{{ $oIndex }}">
                                            {{-- Radio Button Kunci Jawaban --}}
                                            <input class="form-check-input cursor-pointer flex-shrink-0"
                                                type="radio"
                                                style="width: 18px; height: 18px; margin: 0 !important; accent-color: #198754;"
                                                name="correct_radio_{{ $qIndex }}"
                                                wire:click="setCorrectOption({{ $qIndex }}, {{ $oIndex }})"
                                                @checked(!empty($option['is_correct']))
                                                title="Pilih opsi ini sebagai kunci jawaban yang benar">

                                            {{-- Badge Huruf Opsi (A, B, C, D, E) --}}
                                            <span
                                                class="option-letter flex-shrink-0">{{ $letters[$oIndex] ?? '?' }}</span>

                                            {{-- Input Teks Opsi --}}
                                            <input type="text"
                                                class="form-control form-control-sm border-0 bg-transparent flex-grow-1 @error("questions.{$qIndex}.options.{$oIndex}.option_text") is-invalid @enderror"
                                                wire:model="questions.{{ $qIndex }}.options.{{ $oIndex }}.option_text"
                                                placeholder="Ketik teks pilihan {{ $letters[$oIndex] ?? '' }}...">

                                            {{-- Status Benar --}}
                                            @if (!empty($option['is_correct']))
                                                <span
                                                    class="badge bg-success-subtle text-success text-xs px-2 py-1 flex-shrink-0">
                                                    <i class="ri-check-line me-1"></i>Kunci Benar
                                                </span>
                                            @endif

                                            {{-- Tombol Hapus Opsi (Minimal 2 opsi) --}}
                                            @if (count($question['options']) > 2)
                                                <button type="button" class="btn btn-link text-danger p-0 ms-1"
                                                    wire:click="removeOption({{ $qIndex }}, {{ $oIndex }})"
                                                    title="Hapus Opsi">
                                                    <i class="ri-close-circle-line fs-5"></i>
                                                </button>
                                            @endif
                                        </div>
                                        @error("questions.{$qIndex}.options.{$oIndex}.option_text")
                                            <div class="text-danger text-xs ps-4 ms-3">{{ $message }}</div>
                                        @enderror
                                    @endforeach
                                </div>

                                {{-- Tombol Tambah Opsi (Maksimal 5) --}}
                                @if (count($question['options']) < 5)
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm py-1 px-3 fs-8"
                                            wire:click="addOption({{ $qIndex }})">
                                            <i class="ri-add-line me-1"></i>Tambah Opsi Pilihan
                                            ({{ $letters[count($question['options'])] ?? '' }})
                                        </button>
                                    </div>
                                @endif
                            </div>

                            {{-- Penjelasan / Pembahasan Soal --}}
                            <div>
                                <label class="form-label text-xs fw-semibold text-muted mb-1">
                                    Pembahasan Jawaban (Opsional)
                                </label>
                                <input type="text" class="form-control form-control-sm text-xs"
                                    wire:model="questions.{{ $qIndex }}.explanation"
                                    placeholder="Jelaskan alasan kunci jawaban benar untuk evaluasi pemahaman peserta...">
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tombol Tambah Butir Soal Baru --}}
                <div class="mt-4 pt-3 border-top d-flex justify-content-center">
                    <button type="button"
                        class="btn btn-outline-navy px-4 py-2 fw-semibold d-flex align-items-center gap-2"
                        wire:click="addQuestion">
                        <i class="ri-add-circle-line fs-5 text-gold"></i>
                        <span>Tambah Butir Soal Baru</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Submit Action Bar --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-5">
            <button type="submit" class="btn btn-simple-gold d-flex align-items-center w-100"
                wire:loading.attr="disabled">
                <span wire:loading.remove><i class="ri-save-line"></i> Simpan Evaluasi Kuis</span>
                <span wire:loading><span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
