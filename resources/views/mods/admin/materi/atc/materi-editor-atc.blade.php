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

        /* --- TOOLBAR & CONTAINER UNIFIED BORDER & STYLING --- */
        .ql-toolbar.ql-snow {
            border: 1px solid #e2e8f0 !important;
            border-bottom: 1px solid #edf2f7 !important;
            border-top-left-radius: 8px !important;
            border-top-right-radius: 8px !important;
            background: #ffffff !important;
            padding: 10px 12px !important;
            transition: border-color 0.2s ease;
        }

        .ql-container.ql-snow {
            border: 1px solid #e2e8f0 !important;
            border-top: none !important;
            border-bottom-left-radius: 8px !important;
            border-bottom-right-radius: 8px !important;
            min-height: 380px !important;
            font-family: inherit !important;
            font-size: 15px !important;
            color: #2d3748 !important;
            background: #ffffff !important;
            transition: border-color 0.2s ease;
        }

        /* Hilangkan garis biru fokus bawaan browser (Firefox / Chrome outline) */
        #quillEditor,
        #quillEditor:focus,
        #quillEditor:focus-visible,
        .ql-container,
        .ql-container:focus,
        .ql-container:focus-visible,
        .ql-editor,
        .ql-editor:focus,
        .ql-editor:focus-visible,
        .ql-snow .ql-editor:focus {
            outline: none !important;
            box-shadow: none !important;
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

        /* --- RESPONSIVE & ELEGANT IMAGE STYLING --- */
        .ql-editor img {
            max-width: 100% !important;
            max-height: 440px !important;
            width: auto !important;
            height: auto !important;
            object-fit: contain !important;
            border-radius: 8px !important;
            display: block !important;
            margin: 16px auto !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        }

        .ql-editor .ql-align-center img {
            margin-left: auto !important;
            margin-right: auto !important;
            display: block !important;
        }

        .ql-editor .ql-align-right img {
            margin-left: auto !important;
            margin-right: 0 !important;
            display: block !important;
        }

        .ql-editor .ql-align-left img {
            margin-left: 0 !important;
            margin-right: auto !important;
            display: block !important;
        }

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
            gap: 16px !important;
            white-space: normal !important;
            user-select: none !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
        }

        .materi-doc-card * {
            white-space: normal !important;
            text-decoration: none !important;
        }

        .materi-doc-left {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .materi-doc-icon {
            width: 44px !important;
            height: 44px !important;
            min-width: 44px !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            flex-shrink: 0 !important;
        }

        .materi-doc-info {
            min-width: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 2px !important;
        }

        .materi-doc-title {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #1e293b !important;
            line-height: 1.3 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            margin: 0 !important;
        }

        .materi-doc-meta {
            font-size: 12px !important;
            color: #64748b !important;
            display: flex !important;
            align-items: center !important;
            gap: 5px !important;
            margin: 0 !important;
            line-height: 1.2 !important;
        }

        .materi-doc-meta svg {
            flex-shrink: 0 !important;
        }

        /* Button Unduh Berkas - Compact & Clean */
        .materi-doc-btn {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 8px 16px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #0284c7 !important;
            background-color: #f0f9ff !important;
            border: 1px solid #bae6fd !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            transition: all 0.2s ease !important;
            line-height: 1.2 !important;
            cursor: pointer !important;
        }

        .materi-doc-btn:hover {
            background-color: #0284c7 !important;
            color: #ffffff !important;
            border-color: #0284c7 !important;
            text-decoration: none !important;
        }

        .materi-doc-btn svg {
            flex-shrink: 0 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    <script>
        let quillInstance = null;

        // Vektor SVG Bawaan untuk Kartu Dokumen
        function getDocumentSvgs(ext) {
            const e = (ext || '').toLowerCase();
            let iconSvg = '';

            if (e === 'pdf') {
                iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M7 18H17V16H7V18Z" fill="#ef4444"/><path d="M17 14H7V12H17V14Z" fill="#ef4444"/><path d="M7 10H11V8H7V10Z" fill="#ef4444"/><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V9C21 8.46957 20.7893 7.96086 20.4142 7.58579L15.4142 2.58579C15.0391 2.21071 14.5304 2 14 2H6ZM5 5C5 4.44772 5.44772 4 6 4H13V9H18V19C18 19.5523 17.5523 20 17 20H7C6.44772 20 6 19.5523 6 19V5ZM15 4.41421L18.5858 8H15V4.41421Z" fill="#ef4444"/></svg>';
            } else if (['ppt', 'pptx'].includes(e)) {
                iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V9C21 8.46957 20.7893 7.96086 20.4142 7.58579L15.4142 2.58579C15.0391 2.21071 14.5304 2 14 2H6ZM5 5C5 4.44772 5.44772 4 6 4H13V9H18V19C18 19.5523 17.5523 20 17 20H7C6.44772 20 6 19.5523 6 19V5ZM15 4.41421L18.5858 8H15V4.41421Z" fill="#f59e0b"/><path d="M8 12H13C14.1046 12 15 12.8954 15 14C15 15.1046 14.1046 16 13 16H10V18H8V12ZM10 14H13C13.5523 14 14 13.5523 14 13C14 12.4477 13.5523 12 13 12H10V14Z" fill="#f59e0b"/></svg>';
            } else if (['doc', 'docx'].includes(e)) {
                iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V9C21 8.46957 20.7893 7.96086 20.4142 7.58579L15.4142 2.58579C15.0391 2.21071 14.5304 2 14 2H6ZM5 5C5 4.44772 5.44772 4 6 4H13V9H18V19C18 19.5523 17.5523 20 17 20H7C6.44772 20 6 19.5523 6 19V5ZM15 4.41421L18.5858 8H15V4.41421Z" fill="#2563eb"/><path d="M8 12L9.5 17H11L12 14L13 17H14.5L16 12H14.5L13.5 15.5L12.5 12.5H11.5L10.5 15.5L9.5 12H8Z" fill="#2563eb"/></svg>';
            } else if (['xls', 'xlsx'].includes(e)) {
                iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V9C21 8.46957 20.7893 7.96086 20.4142 7.58579L15.4142 2.58579C15.0391 2.21071 14.5304 2 14 2H6ZM5 5C5 4.44772 5.44772 4 6 4H13V9H18V19C18 19.5523 17.5523 20 17 20H7C6.44772 20 6 19.5523 6 19V5ZM15 4.41421L18.5858 8H15V4.41421Z" fill="#16a34a"/><path d="M8 12L10.5 15L8 18H10L11.5 16.2L13 18H15L12.5 15L15 12H13L11.5 13.8L10 12H8Z" fill="#16a34a"/></svg>';
            } else {
                iconSvg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6 2C4.34315 2 3 3.34315 3 5V19C3 20.6569 4.34315 22 6 22H18C19.6569 22 21 20.6569 21 19V9C21 8.46957 20.7893 7.96086 20.4142 7.58579L15.4142 2.58579C15.0391 2.21071 14.5304 2 14 2H6ZM5 5C5 4.44772 5.44772 4 6 4H13V9H18V19C18 19.5523 17.5523 20 17 20H7C6.44772 20 6 19.5523 6 19V5ZM15 4.41421L18.5858 8H15V4.41421Z" fill="#64748b"/></svg>';
            }

            const clipSvg = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>';
            const downloadSvg = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>';

            return { iconSvg, clipSvg, downloadSvg };
        }

        function createCardHtml(data) {
            const ext = (data.extension || '').toLowerCase();
            const svgs = getDocumentSvgs(ext);
            const filename = data.filename || 'Dokumen Kelas';
            const size = data.size || '';
            const url = data.url || '#';

            return '<div class="materi-doc-left">' +
                '<div class="materi-doc-icon">' + svgs.iconSvg + '</div>' +
                '<div class="materi-doc-info">' +
                    '<div class="materi-doc-title">' + filename + '</div>' +
                    '<div class="materi-doc-meta">' + svgs.clipSvg + '<span>Dokumen / Slide Tayang Resmi · ' + size + '</span></div>' +
                '</div>' +
            '</div>' +
            '<a href="' + url + '" target="_blank" download class="materi-doc-btn">' + svgs.downloadSvg + '<span>Unduh Berkas</span></a>';
        }

        function registerQuillCustomBlotsAndIcons() {
            if (typeof Quill === 'undefined') return;

            // Icon attachment SVG standar dengan ukuran dan stroke persis bawaan Quill
            const icons = Quill.import('ui/icons');
            icons['attachment'] = '<svg viewBox="0 0 18 18"><path class="ql-stroke" d="M13.7,6.8L7.6,12.9c-1.3,1.3-3.4,1.3-4.7,0s-1.3-3.4,0-4.7l6.6-6.6c0.9-0.9,2.3-0.9,3.2,0s0.9,2.3,0,3.2L6.1,11.4 c-0.4,0.4-1.2,0.4-1.6,0c-0.4-0.4-0.4-1.2,0-1.6l5.7-5.7"></path></svg>';

            // Custom Parchment Blot untuk Document Attachment Card
            try {
                const BlockEmbed = Quill.import('blots/block/embed');
                if (BlockEmbed) {
                    class DocAttachmentBlot extends BlockEmbed {
                        static create(value) {
                            let node = super.create();
                            node.setAttribute('contenteditable', 'false');
                            node.className = 'materi-doc-card';
                            node.innerHTML = createCardHtml(value);
                            return node;
                        }

                        static value(node) {
                            return {
                                url: node.querySelector('a')?.getAttribute('href') || '',
                                filename: node.querySelector('.materi-doc-title')?.innerText || '',
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
                            // Handler khusus tombol image agar berkas diunggah ke storage alih-alih base64
                            'image': function() {
                                triggerUploadImage();
                            },
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

            // Load existing content from data-content attribute
            const b64 = editorContainer.getAttribute('data-content') || '';
            let existingContent = '';
            if (b64) {
                try {
                    existingContent = decodeURIComponent(escape(atob(b64)));
                } catch (err) {
                    try {
                        existingContent = atob(b64);
                    } catch (e2) {
                        existingContent = '';
                    }
                }
            }

            if (existingContent && existingContent.trim() !== '') {
                try {
                    quillInstance.root.innerHTML = existingContent;
                } catch (e) {
                    quillInstance.setText(existingContent);
                }
            } else {
                quillInstance.setText('');
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

            const maxBytes = 10 * 1024 * 1024; // 10 MB
            if (file.size > maxBytes) {
                const currentMb = (file.size / (1024 * 1024)).toFixed(1);
                Livewire.dispatch('alert-show', {
                    data: {
                        type: 'danger',
                        message: 'Ukuran berkas dokumen melebihi batas maksimal 10 MB (' + currentMb + ' MB).'
                    }
                });
                return;
            }

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
            .then(res => {
                if (res.status === 413) {
                    throw new Error('Ukuran berkas melebihi batas maksimal server (413 Request Entity Too Large).');
                }
                return res.json();
            })
            .then(data => {
                if (banner) banner.classList.add('d-none');

                if (data.success && quillInstance) {
                    const ext = (data.extension || '').toLowerCase();
                    const range = quillInstance.getSelection(true) || { index: quillInstance.getLength() };

                    try {
                        quillInstance.insertEmbed(range.index, 'docAttachment', {
                            url: data.url,
                            filename: data.filename,
                            size: data.size,
                            extension: ext
                        });
                        quillInstance.setSelection(range.index + 1);
                    } catch (err) {
                        const fallbackCard = '<div class="materi-doc-card" contenteditable="false">' + createCardHtml(data) + '</div><p><br></p>';
                        quillInstance.clipboard.dangerouslyPasteHTML(range.index, fallbackCard);
                    }
                } else {
                    Livewire.dispatch('alert-show', {
                        data: {
                            type: 'danger',
                            message: data.message || 'Gagal mengunggah berkas lampiran materi.'
                        }
                    });
                }
            })
            .catch(err => {
                if (banner) banner.classList.add('d-none');
                console.error(err);
                Livewire.dispatch('alert-show', {
                    data: {
                        type: 'danger',
                        message: err.message || 'Terjadi kesalahan saat mengunggah berkas ke server.'
                    }
                });
            });
        }

        // Handler untuk Upload Gambar langsung ke Server
        function triggerUploadImage() {
            const imgInput = document.getElementById('quillImageInput');
            if (imgInput) {
                imgInput.value = '';
                imgInput.click();
            }
        }

        function handleImageUpload(file) {
            if (!file) return;

            // Pre-check batas ukuran gambar (Maksimal 2 MB agar server ringan dan loading cepat)
            const maxImgBytes = 2 * 1024 * 1024;
            if (file.size > maxImgBytes) {
                const currentMb = (file.size / (1024 * 1024)).toFixed(1);
                Livewire.dispatch('alert-show', {
                    data: {
                        type: 'danger',
                        message: 'Ukuran berkas gambar melebihi batas maksimal 2 MB (' + currentMb + ' MB).'
                    }
                });
                return;
            }

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
            .then(res => {
                if (res.status === 413) {
                    throw new Error('Ukuran gambar melebihi batas kapasitas server (413 Request Entity Too Large).');
                }
                return res.json();
            })
            .then(data => {
                if (banner) banner.classList.add('d-none');

                if (data.success && quillInstance) {
                    const range = quillInstance.getSelection(true) || { index: quillInstance.getLength() };
                    quillInstance.insertEmbed(range.index, 'image', data.url);
                    quillInstance.setSelection(range.index + 1);
                } else {
                    Livewire.dispatch('alert-show', {
                        data: {
                            type: 'danger',
                            message: data.message || 'Gagal mengunggah berkas gambar.'
                        }
                    });
                }
            })
            .catch(err => {
                if (banner) banner.classList.add('d-none');
                console.error(err);
                Livewire.dispatch('alert-show', {
                    data: {
                        type: 'danger',
                        message: err.message || 'Terjadi kesalahan saat mengunggah gambar ke server.'
                    }
                });
            });
        }

        function handleSaveKonten() {
            let htmlContent = '';
            if (quillInstance) {
                const plainText = quillInstance.getText().trim();
                const rawHtml = quillInstance.root.innerHTML;
                const hasMedia = rawHtml.includes('<img') || rawHtml.includes('<iframe') || rawHtml.includes('materi-doc-card');

                if (plainText.length > 0 || hasMedia) {
                    htmlContent = rawHtml;
                }

                // Cek batas payload naskah untuk mencegah 413 Request Entity Too Large dari Nginx
                if (htmlContent.length > 2 * 1024 * 1024) {
                    Livewire.dispatch('alert-show', {
                        data: {
                            type: 'danger',
                            message: 'Ukuran naskah materi melebihi batas maksimal server. Pastikan gambar diunggah melalui tombol gambar pada toolbar.'
                        }
                    });
                    return;
                }
            }

            // Panggil save ke Livewire agar validasi server berjalan, mengisi error bag dan mewarnai is-invalid pada field input
            const btn = document.getElementById('btnSubmitKonten');
            const lwContainer = btn ? btn.closest('[wire\\:id]') : null;
            const wireId = lwContainer ? lwContainer.getAttribute('wire:id') : null;
            const component = wireId ? Livewire.find(wireId) : null;

            if (component) {
                if (typeof component.saveLesson === 'function') {
                    component.saveLesson(htmlContent);
                } else if (typeof component.save === 'function') {
                    component.save(htmlContent);
                } else {
                    try {
                        component.call('saveLesson', htmlContent);
                    } catch (e) {
                        component.call('save', htmlContent);
                    }
                }
            } else if (typeof @this !== 'undefined') {
                try {
                    @this.call('saveLesson', htmlContent);
                } catch (e) {
                    @this.call('save', htmlContent);
                }
            }
        }

        window.initQuillEditor = initQuillEditor;
        window.handleSaveKonten = handleSaveKonten;

        document.addEventListener('livewire:navigated', () => {
            setTimeout(initQuillEditor, 100);
        });

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initQuillEditor, 100);
        });

        window.addEventListener('init-editor', () => {
            setTimeout(initQuillEditor, 100);
        });

        // Event delegation untuk tombol simpan konten dan upload berkas
        document.addEventListener('click', (e) => {
            if (e.target && (e.target.id === 'btnSubmitKonten' || e.target.closest('#btnSubmitKonten'))) {
                handleSaveKonten();
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target && e.target.id === 'quillFileInput' && e.target.files && e.target.files[0]) {
                handleFileUpload(e.target.files[0]);
            }
            if (e.target && e.target.id === 'quillImageInput' && e.target.files && e.target.files[0]) {
                handleImageUpload(e.target.files[0]);
            }
        });
    </script>
@endpush
