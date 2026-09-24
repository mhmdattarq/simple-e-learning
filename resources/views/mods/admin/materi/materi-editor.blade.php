<div>
    @push('css')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <style>
            .form-konten-card {
                background: #ffffff;
                border-radius: 12px;
                border: 1px solid #edf2f7;
                padding: 24px;
            }

            .ql-toolbar.ql-snow {
                border-top-left-radius: 8px;
                border-top-right-radius: 8px;
                border-color: #e2e8f0;
                background: #ffffff;
                padding: 10px 12px;
            }

            .ql-container.ql-snow {
                border-bottom-left-radius: 8px;
                border-bottom-right-radius: 8px;
                border-color: #e2e8f0;
                min-height: 280px;
                font-family: inherit;
                font-size: 15px;
                color: #2d3748;
            }

            .ql-editor.ql-blank::before {
                color: #a0aec0;
                font-style: italic;
            }

            .btn-tambah-konten {
                background-color: #6366f1;
                border-color: #6366f1;
                color: #ffffff;
                padding: 12px;
                font-size: 15px;
                font-weight: 600;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .btn-tambah-konten:hover {
                background-color: #4f46e5;
                border-color: #4f46e5;
                color: #ffffff;
            }
        </style>
    @endpush

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

    {{-- KARTU FORM KONTEN PERSIS SEPERTI GAMBAR USER --}}
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card form-konten-card shadow-sm border-0 mb-40">
                <h5 class="fw-bold text-dark mb-20">Form Konten</h5>

                {{-- Pengaturan Bab & Urutan Ringkas di Atas Editor --}}
                <div class="row g-3 mb-20">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Bab Silabus Tujuan <span
                                class="text-danger">*</span></label>
                        <select wire:model.live="lesson.chapter_id" class="form-select form-select-sm radius-8">
                            @foreach ($chapters as $ch)
                                <option value="{{ $ch['id'] }}">{{ $ch['title'] }}</option>
                            @endforeach
                        </select>
                        @error('lesson.chapter_id')
                            <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Tipe Media <span
                                class="text-danger">*</span></label>
                        <select wire:model.live="lesson.content_type" class="form-select form-select-sm radius-8">
                            <option value="article">Artikel / Teks</option>
                            <option value="video">Video YouTube</option>
                            <option value="document">Dokumen / Slide</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Nomor Urut <span
                                class="text-danger">*</span></label>
                        <input type="number" min="1" wire:model="lesson.order"
                            class="form-control form-control-sm radius-8" placeholder="1">
                        @error('lesson.order')
                            <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Versi Modul <span
                                class="text-danger">*</span></label>
                        <input type="text" wire:model="lesson.version" class="form-control form-control-sm radius-8"
                            placeholder="Versi 1.0">
                        @error('lesson.version')
                            <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                {{-- Kolom Tambahan Berdasarkan Tipe Media --}}
                @if (($lesson['content_type'] ?? '') === 'video')
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Tautan Video YouTube (Embed Link)
                            <span class="text-danger">*</span></label>
                        <input type="url" wire:model="lesson.video_url" class="form-control radius-8 py-2"
                            placeholder="https://www.youtube.com/watch?v=...">
                        @error('lesson.video_url')
                            <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                @elseif (($lesson['content_type'] ?? '') === 'document')
                    <div class="mb-20">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Unggah Berkas Slide / Dokumen
                            (PDF/PPTX, Maks. 10MB)</label>
                        <input type="file" wire:model="attachmentFile" class="form-control radius-8 py-2"
                            accept=".pdf,.pptx,.ppt">
                        @error('attachmentFile')
                            <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                @endif

                {{-- Judul Konten Materi --}}
                <div class="mb-20">
                    <label class="form-label fw-semibold text-dark fs-8 mb-1">Judul Materi Pembelajaran <span
                            class="text-danger">*</span></label>
                    <input type="text" wire:model="lesson.title" class="form-control radius-8 py-2"
                        placeholder="Contoh: Pengantar Core Values BerAKHLAK dan Implementasi Nyata ASN">
                    @error('lesson.title')
                        <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Toolbar & Editor Area (Quill Snow Editor) --}}
                <div class="mb-24" wire:ignore>
                    <div id="quillToolbar">
                        <span class="ql-formats">
                            <select class="ql-font"></select>
                            <select class="ql-size"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-underline"></button>
                            <button class="ql-strike"></button>
                        </span>
                        <span class="ql-formats">
                            <select class="ql-color"></select>
                            <select class="ql-background"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-script" value="sub"></button>
                            <button class="ql-script" value="super"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-header" value="1"></button>
                            <button class="ql-header" value="2"></button>
                            <button class="ql-blockquote"></button>
                            <button class="ql-code-block"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-indent" value="-1"></button>
                            <button class="ql-indent" value="+1"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-direction" value="rtl"></button>
                            <select class="ql-align"></select>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                            <button class="ql-video"></button>
                            <button class="ql-formula"></button>
                        </span>
                        <span class="ql-formats">
                            <button class="ql-clean"></button>
                        </span>
                    </div>
                    <div id="quillEditor"></div>
                </div>

                {{-- Tombol Tambah Konten Sesuai Gambar --}}
                @if (!$isFrozen)
                    <button type="button" id="btnSubmitKonten" class="btn btn-simple-gold w-100 shadow-sm">
                        {{ $lessonId ? 'Perbarui Konten' : 'Tambah Konten' }}
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

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        let quillInstance = null;

        function initQuillEditor() {
            const editorContainer = document.getElementById('quillEditor');
            const toolbarContainer = document.getElementById('quillToolbar');
            if (!editorContainer || !toolbarContainer) return;

            if (typeof Quill === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js';
                script.onload = () => {
                    initQuillEditor();
                };
                document.head.appendChild(script);
                return;
            }

            // Clean up previous instance if any
            if (quillInstance) {
                quillInstance = null;
                editorContainer.innerHTML = '';
            }

            quillInstance = new Quill('#quillEditor', {
                theme: 'snow',
                placeholder: 'Tulis Sesuatu...',
                modules: {
                    toolbar: '#quillToolbar'
                }
            });

            // Load existing content if editing
            const existingContent = @js($lesson['body_text'] ?? '');
            if (existingContent && existingContent.trim() !== '') {
                try {
                    quillInstance.root.innerHTML = existingContent;
                } catch (e) {
                    quillInstance.setText(existingContent);
                }
            }
        }

        function handleSaveKonten() {
            if (quillInstance) {
                const plainText = quillInstance.getText().trim();
                if (!plainText || plainText.length === 0) {
                    Livewire.dispatch('alert-show', {
                        data: {
                            type: 'warning',
                            title: 'Konten Kosong',
                            message: 'Naskah konten materi pembelajaran belum ditulis.'
                        }
                    });
                    return;
                }

                const htmlContent = quillInstance.root.innerHTML;
                @this.call('save', htmlContent);
            } else {
                @this.call('save');
            }
        }

        document.addEventListener('livewire:navigated', () => {
            setTimeout(initQuillEditor, 100);
        });

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initQuillEditor, 100);

            document.addEventListener('click', (e) => {
                if (e.target && (e.target.id === 'btnSubmitKonten' || e.target.closest(
                        '#btnSubmitKonten'))) {
                    handleSaveKonten();
                }
            });
        });
    </script>
@endpush
