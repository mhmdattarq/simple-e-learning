@push('css')
    <style>
        /* === PDF Viewer Container Styling (ATC Component) === */
        #pdfViewerWrapper {
            position: relative;
            background-color: #525659;
            min-height: 720px;
            transition: all 0.3s ease;
        }

        .pdf-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.75);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10;
            color: #ffffff;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .pdf-loading-overlay.fade-out {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        (function() {
            /**
             * Inisialisasi Kontrol Interaktif Viewer PDF pada Komponen Verifikasi Detail ATC
             * Mencegah full page reload saat memeriksa berkas surat usulan ASN.
             */
            function initPdfViewerAtc() {
                var wrapper = document.getElementById('pdfViewerWrapper');
                if (!wrapper) return;

                var iframe = wrapper.querySelector('iframe');
                if (!iframe) return;

                // Pasang overlay spinner loading jika belum ada
                var existingOverlay = wrapper.querySelector('.pdf-loading-overlay');
                if (!existingOverlay) {
                    var overlay = document.createElement('div');
                    overlay.className = 'pdf-loading-overlay';
                    overlay.innerHTML = `
                        <div class="spinner-border text-warning mb-2" role="status" style="width: 2.2rem; height: 2.2rem;"></div>
                        <span style="font-size: 13px; font-weight: 500;">Memuat Dokumen PDF...</span>
                    `;
                    wrapper.appendChild(overlay);

                    // Sembunyikan spinner ketika iframe selesai dimuat
                    iframe.addEventListener('load', function() {
                        overlay.classList.add('fade-out');
                    });

                    // Timeout fallback jika event load tidak tertrigger browser (misal cache)
                    setTimeout(function() {
                        overlay.classList.add('fade-out');
                    }, 1200);
                }
            }

            // Inisialisasi saat DOM siap & navigasi Livewire
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPdfViewerAtc);
            } else {
                initPdfViewerAtc();
            }

            document.addEventListener('livewire:navigated', function() {
                initPdfViewerAtc();
            });
        })();
    </script>
@endpush
