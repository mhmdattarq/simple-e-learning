@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM (Evaluasi Modul) === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tableEvaluasi thead tr:first-child th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-color: #e2e8f0 !important;
            padding: 12px 12px !important;
            vertical-align: middle !important;
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
            transition: background-color 0.15s ease !important;
        }

        /* Remove ANY DataTables or browser default hover/focus/active outline */
        table.dataTable thead>tr>th:hover,
        table.dataTable thead>tr>th:focus,
        table.dataTable thead>tr>th:active,
        table.dataTable thead>tr>th.dt-orderable-asc:hover,
        table.dataTable thead>tr>th.dt-orderable-desc:hover,
        table.dataTable thead>tr>td.dt-orderable-asc:hover,
        table.dataTable thead>tr>td.dt-orderable-desc:hover,
        #tableEvaluasi thead th,
        #tableEvaluasi thead th:hover,
        #tableEvaluasi thead th:focus,
        #tableEvaluasi thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tableEvaluasi thead tr:first-child th.dt-orderable-asc:hover,
        #tableEvaluasi thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tableEvaluasi thead tr:first-child th.dt-orderable-asc,
        #tableEvaluasi thead tr:first-child th.dt-orderable-desc,
        #tableEvaluasi thead tr:first-child th.dt-ordering-asc,
        #tableEvaluasi thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tableEvaluasi thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tableEvaluasi thead tr:first-child th span.dt-column-order:before,
        #tableEvaluasi thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tableEvaluasi thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tableEvaluasi thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
            color: #0f172a !important;
            opacity: 1 !important;
        }

        /* Second Row Header (Filter): Light & Completely Disable Sort UI */
        #header-filter th {
            background-color: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            cursor: default !important;
            outline: none !important;
            box-shadow: none !important;
        }

        #header-filter th::before,
        #header-filter th::after,
        #header-filter th span.dt-column-order {
            display: none !important;
            content: "" !important;
            visibility: hidden !important;
        }

        #header-filter input.search-col-dt {
            height: 32px !important;
            min-height: 32px !important;
            max-height: 32px !important;
            line-height: 32px !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 0 8px !important;
            font-size: 12px !important;
            color: #1e293b !important;
            width: 100% !important;
            box-sizing: border-box !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        #header-filter input.search-col-dt:focus {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.25) !important;
            outline: none !important;
        }

        /* Table Rows & Cells */
        #tableEvaluasi td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tableEvaluasi tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tableEvaluasi td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        #tableEvaluasi tbody tr:hover td.dt-empty,
        #tableEvaluasi tbody tr.dt-empty:hover td {
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        /* Length Menu & Info */
        .dt-container .dt-length,
        .dt-container .dt-info {
            font-size: 12.5px !important;
            color: #64748b !important;
        }

        .dt-container .dt-length select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 4px 24px 4px 8px !important;
            font-size: 12px !important;
            color: #334155 !important;
            outline: none !important;
            background-color: #ffffff !important;
        }

        /* Pagination Clean & Compact */
        .dt-container .dt-paging {
            display: flex !important;
            align-items: center !important;
            gap: 4px !important;
        }

        .dt-container .dt-paging .dt-paging-button {
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            padding: 5px 10px !important;
            font-size: 12px !important;
            color: #475569 !important;
            background: #ffffff !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .dt-container .dt-paging .dt-paging-button.current,
        .dt-container .dt-paging .dt-paging-button.current:hover {
            background: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
            font-weight: 600 !important;
        }

        .dt-container .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) {
            background: #e2e8f0 !important;
            color: #071a33 !important;
            border-color: #cbd5e1 !important;
        }

        .dt-container .dt-paging .dt-paging-button.disabled {
            opacity: 0.4 !important;
        }

        @media (min-width: 992px) {
            .table-responsive {
                min-height: 320px;
                overflow: visible !important;
            }

            #tableEvaluasi_wrapper .dataTables_scroll,
            #tableEvaluasi_wrapper .dataTables_scrollBody {
                overflow: visible !important;
            }

            #tableEvaluasi {
                width: 100% !important;
                table-layout: fixed !important;
            }
        }

        @media (max-width: 991.98px) {
            .table-responsive {
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
            }

            #tableEvaluasi {
                width: 100% !important;
                min-width: 650px;
            }
        }

        #tableEvaluasi .btn-evaluasi-action {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }

        #tableEvaluasi .btn-evaluasi-action .badge-evaluasi-count {
            font-size: 10px;
            padding: 2px 7px;
            transition: all 0.2s ease-in-out;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initEvaluasiTable() {
            var tableEl = document.getElementById('tableEvaluasi');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableEvaluasi')) {
                $('#tableEvaluasi').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Server-Side Baru (5 Kolom: No, Nama Kelas, Total Soal, Partisipasi, Evaluasi)
            if ($.fn.DataTable) {
                window.dtTable = $('#tableEvaluasi').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true, // Sorting hanya di row header pertama
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [1, 'asc'] // Default order by Nama Kelas (Kolom index 1)
                    ],
                    ajax: '{{ route('evaluasi.dt') }}',
                    columns: [
                        // Kolom 0: Nomor Urut Otomatis
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-medium text-muted',
                            width: '50px',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        // Kolom 1: Nama Kelas & Tipe
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true,
                            width: '24%',
                            render: function(data, type, row) {
                                let detailUrl = "{{ route('evaluasi.detail', ':id') }}".replace(':id', row.id);

                                let typeBadge = '';
                                if (row.type === 'permanent') {
                                    typeBadge =
                                        '<span class="badge bg-success-subtle text-success" style="font-size: 10.5px;"><i class="ri-infinity-line me-1"></i>Permanen</span>';
                                } else if (row.type === 'paid' || row.type === 'berbayar') {
                                    typeBadge =
                                        '<span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 10.5px;"><i class="ri-money-dollar-circle-line me-1"></i>Berbayar</span>';
                                } else {
                                    typeBadge =
                                        '<span class="badge bg-primary-subtle text-primary" style="font-size: 10.5px;"><i class="ri-calendar-line me-1"></i>Batch</span>';
                                }

                                let catText = row.category ? `<span class="text-muted" style="font-size: 11.5px;"><i class="ri-folder-3-line me-1"></i>${row.category.name}</span>` : '';

                                return `
                                <div style="word-break: break-word;">
                                    <div class="fw-semibold text-dark mb-1" style="font-size: 13.5px; line-height: 1.4;">
                                        <a href="${detailUrl}" class="text-dark text-decoration-none hover-text-primary" wire:navigate>${data}</a>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        ${typeBadge}
                                        ${catText}
                                    </div>
                                </div>
                                `;
                            }
                        },

                        // Kolom 2: Total Soal
                        {
                            data: 'questions_count',
                            name: 'questions_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '18%',
                            render: function(data, type, row) {
                                let count = Number(row.questions_count || 0);
                                if (count > 0) {
                                    return `<span class="badge bg-info-subtle text-info fw-semibold px-3 py-2 radius-8" style="font-size: 12px;"><i class="ri-question-line me-1"></i>${count} Soal</span>`;
                                }
                                return '<span class="badge bg-light text-secondary border px-3 py-2 radius-8" style="font-size: 11.5px;"><i class="ri-question-line me-1 text-muted"></i>0 Soal</span>';
                            }
                        },

                        // Kolom 3: Partisipasi
                        {
                            data: 'attempts_count',
                            name: 'attempts_count',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '22%',
                            render: function(data, type, row) {
                                let count = Number(row.attempts_count || 0);
                                if (count > 0) {
                                    return `<span class="badge bg-success-subtle text-success fw-semibold px-3 py-2 radius-8" style="font-size: 12px;"><i class="ri-user-follow-line me-1"></i>${count} Pengerjaan</span>`;
                                }
                                return '<span class="badge bg-light text-muted border px-3 py-2 radius-8" style="font-size: 11.5px;"><i class="ri-user-line me-1"></i>Belum Ada</span>';
                            }
                        },

                        // Kolom 4: Evaluasi (Tombol Monitoring Evaluasi & Status)
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            width: '28%',
                            render: function(data, type, row) {
                                let detailUrl = "{{ route('evaluasi.detail', ':id') }}".replace(':id', row.id);
                                let quizzesCount = Number(row.quizzes_count || 0);
                                let chapterCount = Number(row.chapter_quizzes_count || 0);
                                let hasFinal = Boolean(row.final_quiz_exists);

                                let finalBadge = hasFinal 
                                    ? '<span class="badge bg-warning-subtle text-dark border border-warning-subtle" style="font-size: 10px;"><i class="ri-award-line me-1"></i>Ada Ujian Akhir</span>' 
                                    : '<span class="badge bg-light text-muted border" style="font-size: 10px;"><i class="ri-close-circle-line me-1"></i>Belum Ada Ujian Akhir</span>';

                                let chapterText = chapterCount > 0 
                                    ? `<span class="text-muted" style="font-size: 11px;"><i class="ri-booklet-line me-1"></i>${chapterCount} Kuis Bab</span>` 
                                    : `<span class="text-muted fst-italic" style="font-size: 11px;">Belum ada kuis bab</span>`;

                                return `
                                <div class="d-flex flex-column align-items-center gap-1 py-1">
                                    <a href="${detailUrl}" 
                                       class="btn btn-sm btn-simple-gold btn-evaluasi-action d-inline-flex align-items-center gap-2 fw-semibold text-nowrap shadow-sm"
                                       title="Monitoring Evaluasi Kelas"
                                       wire:navigate>
                                        <i class="ri-questionnaire-line fs-6"></i>
                                        <span>Monitoring Evaluasi</span>
                                        <span class="badge bg-dark text-white rounded-pill badge-evaluasi-count">${quizzesCount}</span>
                                    </a>
                                    <div class="d-flex align-items-center gap-1 mt-1">
                                        ${chapterText}
                                        <span class="text-muted">&bull;</span>
                                        ${finalBadge}
                                    </div>
                                </div>
                                `;
                            }
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada data evaluasi kelas',
                        zeroRecords: 'Belum ada data evaluasi kelas'
                    },
                    initComplete: function(settings) {
                        var table = settings.oInstance.api();

                        // Filter Kolom Input pada Thead Kedua (#header-filter)
                        $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                            var colIndex = $(this).closest('th').index();
                            if (table.column(colIndex).search() !== this.value) {
                                table.column(colIndex).search(this.value).draw();
                            }
                        });
                    }
                });
            }
        }

        // Lifecycle Pengaktifan Tabel:
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initEvaluasiTable);
        } else {
            initEvaluasiTable();
        }

        // Saat navigasi SPA Livewire (wire:navigate)
        document.addEventListener('livewire:navigated', initEvaluasiTable);

        // Event reloadDT untuk reload ajax tabel secara realtime tanpa refresh halaman
        window.addEventListener('reloadDT', function() {
            if (window.dtTable && typeof window.dtTable.ajax?.reload === 'function') {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
