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
            <a href="{{ route('materi.detail', $courseId) }}" class="btn btn-sm btn-outline-secondary radius-8 px-3" wire:navigate>
                <i class="ri-arrow-left-line me-1"></i> Kembali ke Silabus
            </a>
            <span class="badge bg-secondary-subtle text-secondary font-monospace fs-8">{{ $course->code }}</span>
            <span class="text-muted fs-8">·</span>
            <span class="text-dark fw-semibold fs-8">{{ $course->title }}</span>
        </div>

        @if ($isFrozen)
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2 radius-8 fs-8">
                <i class="ri-lock-2-line me-1"></i> Pelatihan Batch Terkunci (Curriculum Freeze)
            </span>
        @endif
    </div>

    {{-- KARTU FORM KONTEN PERSIS SEPERTI GAMBAR USER --}}
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card form-konten-card shadow-sm border-0 mb-40">
                <h5 class="fw-bold text-dark mb-20">Form Konten</h5>

                {{-- Pengaturan Bab & Urutan Ringkas di Atas Editor --}}
                <div class="row g-3 mb-20">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Bab Silabus Tujuan <span class="text-danger">*</span></label>
                        <select wire:model="lesson.chapter_id" class="form-select form-select-sm radius-8">
                            @foreach ($chapters as $ch)
                                <option value="{{ $ch['id'] }}">{{ $ch['title'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Nomor Urut Materi <span class="text-danger">*</span></label>
                        <input type="number" min="1" wire:model="lesson.order" class="form-control form-control-sm radius-8" placeholder="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-dark fs-8 mb-1">Versi Modul <span class="text-danger">*</span></label>
                        <input type="text" wire:model="lesson.version" class="form-control form-control-sm radius-8" placeholder="Versi 1.0">
                    </div>
                </div>

                {{-- Judul Konten Materi --}}
                <div class="mb-20">
                    <label class="form-label fw-semibold text-dark fs-8 mb-1">Judul Materi Pembelajaran <span class="text-danger">*</span></label>
                    <input type="text" wire:model="lesson.title" class="form-control radius-8 py-2" placeholder="Contoh: Pengantar Core Values BerAKHLAK dan Implementasi Nyata ASN">
                    @error('lesson.title') <small class="text-danger fs-8 d-block mt-1">{{ $message }}</small> @enderror
                </div>

                {{-- Toolbar & Editor Area (Quill Snow Editor) --}}
                <div class="mb-24" wire:ignore>
                    <div id="quillEditor"></div>
                </div>

                {{-- Tombol Tambah Konten Sesuai Gambar --}}
                @if (! $isFrozen)
                    <button type="button" onclick="handleSaveKonten()" id="btnSubmitKonten" class="btn btn-tambah-konten w-100 shadow-sm">
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
<script>
    // Module-level flag: prevents double initialization regardless of which event fires first
    if (typeof window._quillEditorInitialized === 'undefined') {
        window._quillEditorInitialized = false;
    }
    let quillInstance = null;

    function initQuillEditor() {
        if (window._quillEditorInitialized) return;

        const editorContainer = document.getElementById('quillEditor');
        if (!editorContainer) return;

        if (typeof Quill === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js';
            script.onload = () => initQuillEditor();
            document.head.appendChild(script);
            return;
        }

        // Set flag BEFORE instantiating to block any concurrent calls
        window._quillEditorInitialized = true;

        // Reset container so Quill gets a clean slate
        editorContainer.innerHTML = '';
        editorContainer.className = '';

        const toolbarOptions = [
            [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'script': 'sub' }, { 'script': 'super' }],
            [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
            [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
            [{ 'direction': 'rtl' }, { 'align': [] }],
            ['link', 'image', 'video', 'formula'],
            ['clean']
        ];

        quillInstance = new Quill('#quillEditor', {
            theme: 'snow',
            placeholder: 'Tulis Sesuatu...',
            modules: { toolbar: toolbarOptions }
        });

        // Load existing content when editing an existing lesson
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
            if (!plainText) {
                Livewire.dispatch('alert-show', {
                    data: {
                        type: 'warning',
                        title: 'Konten Kosong',
                        message: 'Naskah konten materi pembelajaran belum ditulis.'
                    }
                });
                return;
            }
            @this.call('save', quillInstance.root.innerHTML);
        } else {
            @this.call('save');
        }
    }

    // Reset flag and re-init when Livewire navigates to this page
    document.addEventListener('livewire:navigated', () => {
        window._quillEditorInitialized = false;
        quillInstance = null;
        setTimeout(initQuillEditor, 50);
    });

    // Init on first hard load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => setTimeout(initQuillEditor, 50));
    } else {
        setTimeout(initQuillEditor, 50);
    }
</script>
@endpush
