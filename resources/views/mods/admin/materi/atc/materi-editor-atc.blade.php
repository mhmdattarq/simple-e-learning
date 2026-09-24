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
            min-height: 350px;
            font-family: inherit;
            font-size: 15px;
            color: #2d3748;
        }

        .ql-editor.ql-blank::before {
            color: #a0aec0;
            font-style: italic;
        }

        /* --- FONT FAMILIES DROPDOWN & STYLES --- */
        .ql-snow .ql-picker.ql-font {
            width: 120px !important;
        }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="serif"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="serif"]::before {
            content: 'Serif' !important;
            font-family: Georgia, Times New Roman, serif !important;
        }
        .ql-snow .ql-picker.ql-font .ql-picker-label[data-value="monospace"]::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item[data-value="monospace"]::before {
            content: 'Monospace' !important;
            font-family: Monaco, Consolas, "Courier New", monospace !important;
        }
        .ql-snow .ql-picker.ql-font .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-font .ql-picker-item:not([data-value])::before {
            content: 'Sans Serif' !important;
            font-family: 'Inter', sans-serif !important;
        }
        .ql-editor .ql-font-serif {
            font-family: Georgia, Times New Roman, serif !important;
        }
        .ql-editor .ql-font-monospace {
            font-family: Monaco, Consolas, "Courier New", monospace !important;
        }
        .ql-editor .ql-font-sans-serif {
            font-family: 'Inter', sans-serif !important;
        }

        /* --- FONT SIZES DROPDOWN & STYLES --- */
        .ql-snow .ql-picker.ql-size {
            width: 125px !important;
        }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="small"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="small"]::before {
            content: 'Kecil (12px)' !important;
        }
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before {
            content: 'Normal (15px)' !important;
        }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="large"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="large"]::before {
            content: 'Besar (20px)' !important;
        }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="huge"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="huge"]::before {
            content: 'Judul (28px)' !important;
        }
        .ql-editor .ql-size-small { font-size: 12px !important; }
        .ql-editor .ql-size-large { font-size: 20px !important; line-height: 1.4 !important; }
        .ql-editor .ql-size-huge { font-size: 28px !important; line-height: 1.3 !important; }

        /* --- POPUP / PICKER OPTIONS Z-INDEX --- */
        .ql-snow .ql-picker-options {
            z-index: 1060 !important;
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
            border-radius: 8px !important;
            padding: 6px !important;
        }

        /* --- TEXT ALIGNMENT --- */
        .ql-editor .ql-align-center { text-align: center !important; }
        .ql-editor .ql-align-right { text-align: right !important; }
        .ql-editor .ql-align-justify { text-align: justify !important; }

        /* --- MARGIN & INDENT --- */
        .ql-editor p.ql-indent-1, .ql-editor h1.ql-indent-1, .ql-editor h2.ql-indent-1, .ql-editor blockquote.ql-indent-1 { padding-left: 2.5rem !important; }
        .ql-editor p.ql-indent-2, .ql-editor h1.ql-indent-2, .ql-editor h2.ql-indent-2, .ql-editor blockquote.ql-indent-2 { padding-left: 5rem !important; }
        .ql-editor p.ql-indent-3, .ql-editor h1.ql-indent-3, .ql-editor h2.ql-indent-3, .ql-editor blockquote.ql-indent-3 { padding-left: 7.5rem !important; }
        .ql-editor p.ql-indent-4, .ql-editor h1.ql-indent-4, .ql-editor h2.ql-indent-4, .ql-editor blockquote.ql-indent-4 { padding-left: 10rem !important; }
        .ql-editor p.ql-indent-5, .ql-editor h1.ql-indent-5, .ql-editor h2.ql-indent-5, .ql-editor blockquote.ql-indent-5 { padding-left: 12.5rem !important; }

        /* --- VIDEO EMBED --- */
        .ql-editor iframe.ql-video {
            width: 100% !important;
            max-width: 720px !important;
            height: 400px !important;
            border-radius: 8px !important;
            display: block !important;
            margin: 16px auto !important;
        }

        /* --- TOOLBAR ATTACH BUTTON PERFECT ALIGNMENT --- */
        .ql-snow.ql-toolbar button.ql-attachment,
        .ql-snow .ql-toolbar button.ql-attachment {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .ql-snow.ql-toolbar button.ql-attachment svg,
        .ql-snow .ql-toolbar button.ql-attachment svg {
            width: 18px !important;
            height: 18px !important;
            float: none !important;
        }

        /* --- DOKUMEN / SLIDE ATTACHMENT CARD --- */
        .materi-doc-card {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-left: 5px solid #f3bc42 !important;
            border-radius: 12px !important;
            padding: 14px 18px !important;
            margin: 16px 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        let quillInstance = null;

        function registerQuillCustomBlotsAndIcons() {
            if (typeof Quill === 'undefined') return;

            // Icon attachment SVG standar dengan ukuran dan stroke persis bawaan Quill
            const icons = Quill.import('ui/icons');
            icons['attachment'] = `<svg viewBox="0 0 18 18">
                <path class="ql-stroke" d="M13.7,6.8L7.6,12.9c-1.3,1.3-3.4,1.3-4.7,0s-1.3-3.4,0-4.7l6.6-6.6c0.9-0.9,2.3-0.9,3.2,0s0.9,2.3,0,3.2L6.1,11.4 c-0.4,0.4-1.2,0.4-1.6,0c-0.4-0.4-0.4-1.2,0-1.6l5.7-5.7"></path>
            </svg>`;

            // Custom Parchment Blot untuk Document Attachment Card
            try {
                const BlockEmbed = Quill.import('blots/block/embed');
                if (BlockEmbed) {
                    class DocAttachmentBlot extends BlockEmbed {
                        static create(value) {
                            let node = super.create();
                            node.setAttribute('contenteditable', 'false');
                            node.className = 'materi-doc-card border rounded-12 p-3 my-3 bg-light shadow-xs d-flex align-items-center justify-content-between flex-wrap gap-2';
                            node.style.borderLeft = '5px solid #f3bc42';

                            let iconClass = 'ri-file-text-line text-secondary';
                            const ext = (value.extension || '').toLowerCase();
                            if (ext === 'pdf') {
                                iconClass = 'ri-file-pdf-line text-danger';
                            } else if (['ppt', 'pptx'].includes(ext)) {
                                iconClass = 'ri-file-ppt-line text-warning';
                            } else if (['doc', 'docx'].includes(ext)) {
                                iconClass = 'ri-file-word-line text-primary';
                            } else if (['xls', 'xlsx'].includes(ext)) {
                                iconClass = 'ri-file-excel-line text-success';
                            }

                            node.innerHTML = `
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 rounded-8 bg-white border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                        <i class="${iconClass} fs-3"></i>
                                    </div>
                                    <div>
                                        <strong class="text-dark d-block fs-7">${value.filename || 'Dokumen Pelatihan'}</strong>
                                        <small class="text-muted fs-8"><i class="ri-attachment-line me-1"></i>Dokumen / Slide Tayang Resmi · ${value.size || ''}</small>
                                    </div>
                                </div>
                                <a href="${value.url}" target="_blank" download class="btn btn-sm btn-outline-primary radius-8 px-3">
                                    <i class="ri-download-line me-1"></i> Unduh Berkas
                                </a>
                            `;
                            return node;
                        }

                        static value(node) {
                            return {
                                url: node.querySelector('a')?.getAttribute('href') || '',
                                filename: node.querySelector('strong')?.innerText || '',
                                size: '',
                                extension: ''
                            };
                        }
                    }
                    DocAttachmentBlot.blotName = 'docAttachment';
                    DocAttachmentBlot.tagName = 'div';
                    DocAttachmentBlot.className = 'materi-doc-card';
                    Quill.register(DocAttachmentBlot, true);
                }
            } catch (e) {
                console.warn('Error registering DocAttachmentBlot:', e);
            }
        }

        function initQuillEditor() {
            const editorContainer = document.getElementById('quillEditor');
            if (!editorContainer) return;

            if (typeof Quill === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js';
                script.onload = () => {
                    registerQuillCustomBlotsAndIcons();
                    initQuillEditor();
                };
                document.head.appendChild(script);
                return;
            }

            registerQuillCustomBlotsAndIcons();

            // Bersihkan instance toolbar lama jika ada (mencegah duplikasi toolbar saat wire:navigate)
            const parent = editorContainer.parentElement;
            if (parent) {
                const existingToolbars = parent.querySelectorAll('.ql-toolbar');
                existingToolbars.forEach(tb => tb.remove());
            }

            if (quillInstance) {
                quillInstance = null;
            }
            editorContainer.innerHTML = '';

            // Definisi toolbar array lengkap standar Quill Snow
            const toolbarOptions = [
                [{ 'font': [] }, { 'size': [] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'script': 'sub'}, { 'script': 'super' }],
                [{ 'header': 1 }, { 'header': 2 }, 'blockquote', 'code-block'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video', 'formula', 'attachment'],
                ['clean']
            ];

            quillInstance = new Quill('#quillEditor', {
                theme: 'snow',
                placeholder: 'Tulis naskah materi pembelajaran di sini... Anda dapat menyematkan video YouTube, gambar, maupun melampirkan berkas slide/dokumen...',
                modules: {
                    toolbar: {
                        container: toolbarOptions,
                        handlers: {
                            // Handler khusus tombol lampirkan dokumen / slide
                            'attachment': function() {
                                triggerAttachDocument();
                            },
                            // Handler khusus video YouTube agar otomatis dikonversi ke embed format
                            'video': function() {
                                const url = prompt('Masukkan tautan video YouTube (contoh: https://www.youtube.com/watch?v=... atau https://youtu.be/...):');
                                if (url && url.trim() !== '') {
                                    let embedUrl = url.trim();
                                    if (embedUrl.includes('youtube.com/watch?v=')) {
                                        const id = embedUrl.split('v=')[1]?.split('&')[0];
                                        if (id) embedUrl = `https://www.youtube.com/embed/${id}`;
                                    } else if (embedUrl.includes('youtu.be/')) {
                                        const id = embedUrl.split('youtu.be/')[1]?.split('?')[0];
                                        if (id) embedUrl = `https://www.youtube.com/embed/${id}`;
                                    }
                                    const range = this.quill.getSelection(true) || { index: this.quill.getLength() };
                                    this.quill.insertEmbed(range.index, 'video', embedUrl);
                                }
                            }
                        }
                    }
                }
            });

            // Set tooltip pada tombol attachment
            const attachBtn = document.querySelector('.ql-toolbar button.ql-attachment');
            if (attachBtn) {
                attachBtn.setAttribute('title', 'Lampirkan Berkas Slide/Dokumen (PDF, PPTX, DOCX, XLS)');
            }

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

        // Handler untuk Upload dan Attach Dokumen/Slide (PDF/PPT/DOC)
        function triggerAttachDocument() {
            const fileInput = document.getElementById('quillFileInput');
            if (fileInput) {
                fileInput.value = '';
                fileInput.click();
            }
        }

        function handleFileUpload(file) {
            if (!file) return;

            const banner = document.getElementById('uploadStatusBanner');
            if (banner) banner.classList.remove('d-none');

            const formData = new FormData();
            formData.append('file', file);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            fetch('{{ route('materi.upload-media') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (banner) banner.classList.add('d-none');

                if (data.success && quillInstance) {
                    const ext = (data.extension || '').toLowerCase();
                    let iconClass = 'ri-file-text-line text-secondary';
                    if (ext === 'pdf') {
                        iconClass = 'ri-file-pdf-line text-danger';
                    } else if (['ppt', 'pptx'].includes(ext)) {
                        iconClass = 'ri-file-ppt-line text-warning';
                    } else if (['doc', 'docx'].includes(ext)) {
                        iconClass = 'ri-file-word-line text-primary';
                    } else if (['xls', 'xlsx'].includes(ext)) {
                        iconClass = 'ri-file-excel-line text-success';
                    }

                    const range = quillInstance.getSelection(true) || { index: quillInstance.getLength() };

                    try {
                        quillInstance.insertEmbed(range.index, 'docAttachment', {
                            url: data.url,
                            filename: data.filename,
                            size: data.size,
                            extension: data.extension
                        });
                        quillInstance.setSelection(range.index + 1);
                    } catch (err) {
                        const cardHtml = `
                        <div class="materi-doc-card border rounded-12 p-3 my-3 bg-light shadow-xs d-flex align-items-center justify-content-between flex-wrap gap-2" contenteditable="false" style="border-left: 5px solid #f3bc42 !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2 rounded-8 bg-white border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="${iconClass} fs-3"></i>
                                </div>
                                <div>
                                    <strong class="text-dark d-block fs-7">${data.filename}</strong>
                                    <small class="text-muted fs-8"><i class="ri-attachment-line me-1"></i>Dokumen / Slide Tayang Resmi · ${data.size}</small>
                                </div>
                            </div>
                            <a href="${data.url}" target="_blank" download class="btn btn-sm btn-outline-primary radius-8 px-3">
                                <i class="ri-download-line me-1"></i> Unduh Berkas
                            </a>
                        </div>
                        <p><br></p>`;
                        quillInstance.clipboard.dangerouslyPasteHTML(range.index, cardHtml);
                    }
                } else {
                    alert(data.message || 'Gagal mengunggah berkas dokumen.');
                }
            })
            .catch(err => {
                if (banner) banner.classList.add('d-none');
                console.error(err);
                alert('Terjadi kesalahan saat mengunggah berkas.');
            });
        }

        function handleSaveKonten() {
            if (quillInstance) {
                const plainText = quillInstance.getText().trim();
                const htmlContent = quillInstance.root.innerHTML;

                // Periksa apakah ada konten berupa teks, gambar, video, atau dokumen card
                const hasMedia = htmlContent.includes('<img') || htmlContent.includes('<iframe') || htmlContent.includes('materi-doc-card');

                if ((!plainText || plainText.length === 0) && !hasMedia) {
                    Livewire.dispatch('alert-show', {
                        data: {
                            type: 'warning',
                            title: 'Konten Kosong',
                            message: 'Naskah konten materi pembelajaran belum ditulis.'
                        }
                    });
                    return;
                }

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
                if (e.target && (e.target.id === 'btnSubmitKonten' || e.target.closest('#btnSubmitKonten'))) {
                    handleSaveKonten();
                }
            });

            const fileInput = document.getElementById('quillFileInput');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        handleFileUpload(this.files[0]);
                    }
                });
            }
        });
    </script>
@endpush
