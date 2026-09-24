<div>
    {{-- Breadcrumb & Info Pelatihan --}}
    <div class="d-flex align-items-center justify-content-between mb-20 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary-subtle text-secondary font-monospace fs-8">{{ $course->code }}</span>
            <span class="text-muted fs-8">·</span>
            <span class="text-dark fw-semibold fs-8">{{ $course->title }}</span>
        </div>

        @if ($isFrozen)
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 radius-8 fs-8">
                <i class="ri-lock-2-line me-1"></i> Pelatihan Batch Terkunci (Curriculum Freeze)
            </span>
        @endif
        <a href="{{ route('materi.detail', $courseId) }}" class="btn btn-danger radius-8 px-3" wire:navigate>
            <i class="ri-arrow-left-line me-1"></i> Kembali ke Silabus
        </a>
    </div>

    {{-- KARTU FORM KONTEN --}}
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card form-konten-card shadow-sm border-0 mb-40">
                <div
                    class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-20 pb-16 border-bottom">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">{{ $lessonId ? 'Edit Materi Pembelajaran' : 'Tambah Materi Baru' }}</h5>
                        <p class="text-muted fs-8 mb-0">Tulis materi lengkap dengan teks terformat, video YouTube
                            tersemat, gambar, serta lampiran slide/dokumen resmi.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span
                            class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 radius-8 fs-8">
                            <i class="ri-folder-2-line me-1"></i> {{ $chapter?->title ?? 'Bab Silabus' }}
                        </span>
                        <span class="badge bg-secondary-subtle text-dark border px-3 py-2 radius-8 fs-8 font-monospace">
                            <i class="ri-hashtag me-1"></i> Materi Urutan #{{ $lesson['order'] }}
                        </span>
                        <span
                            class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 radius-8 fs-8">
                            <i class="ri-shield-check-line me-1"></i> {{ $lesson['version'] }}
                        </span>
                    </div>
                </div>

                {{-- Judul Konten Materi --}}
                <div class="mb-20">
                    <label for="lessonTitleInput" class="form-label fw-semibold text-dark fs-8 mb-1">Judul Materi Pembelajaran <span
                            class="text-danger">*</span></label>
                    <input type="text" id="lessonTitleInput" wire:model.live.debounce.300ms="lesson.title"
                        class="form-control radius-8 py-2 @error('lesson.title') is-invalid @enderror"
                        placeholder="Contoh: Pengantar Core Values BerAKHLAK dan Implementasi Nyata ASN">
                    @error('lesson.title')
                        <div class="invalid-feedback fs-8 d-block mt-1">
                            <i class="ri-error-warning-line me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Status Upload Berkas Media --}}
                <div id="uploadStatusBanner" class="alert alert-info py-2 px-3 radius-8 fs-8 d-none mb-3">
                    <i class="ri-loader-4-line ri-spin me-1"></i> Sedang mengunggah berkas lampiran materi ke server...
                </div>

                {{-- Hidden File Input untuk Attach Dokumen & Unggah Gambar --}}
                <input type="file" id="quillFileInput" style="display: none;"
                    accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.zip">
                <input type="file" id="quillImageInput" style="display: none;" accept="image/*">

                {{-- Toolbar & Editor Area (Quill Snow Editor) --}}
                <div class="mb-24" wire:ignore>
                    <div id="quillEditor" data-content="{{ base64_encode($lesson['body_text'] ?? '') }}"></div>
                </div>
                @error('lesson.body_text')
                    <div class="invalid-feedback fs-8 d-block mb-3">
                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                    </div>
                @enderror

                {{-- Tombol Tambah Konten Sesuai Gambar --}}
                @if (!$isFrozen)
                    <button type="button" id="btnSubmitKonten"
                        class="btn btn-simple-gold w-100 shadow-sm py-12 fw-semibold">
                        <i class="ri-save-line me-1"></i> {{ $lessonId ? 'Perbarui Materi' : 'Tambah Materi' }}
                    </button>
                @else
                    <button type="button" class="btn btn-secondary w-100 py-12 radius-8 fw-semibold" disabled>
                        <i class="ri-lock-2-line me-1"></i> Materi Terkunci (Batch Sedang Aktif Berjalan)
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@include('mods.admin.materi.atc.materi-editor-atc')
