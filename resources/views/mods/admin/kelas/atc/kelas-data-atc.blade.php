@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tableKelas thead tr:first-child th {
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
        #tableKelas thead th,
        #tableKelas thead th:hover,
        #tableKelas thead th:focus,
        #tableKelas thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tableKelas thead tr:first-child th.dt-orderable-asc:hover,
        #tableKelas thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tableKelas thead tr:first-child th.dt-orderable-asc,
        #tableKelas thead tr:first-child th.dt-orderable-desc,
        #tableKelas thead tr:first-child th.dt-ordering-asc,
        #tableKelas thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tableKelas thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tableKelas thead tr:first-child th span.dt-column-order:before,
        #tableKelas thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tableKelas thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tableKelas thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
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
        #tableKelas td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tableKelas tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tableKelas td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        #tableKelas tbody tr:hover td.dt-empty,
        #tableKelas tbody tr.dt-empty:hover td {
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        /* Length Menu & Info */
        .dt-container .dt-length,
        .dt-container div.dt-length {
            margin-bottom: 16px !important;
        }

        .dt-container .dt-length select {
            height: 32px !important;
            min-height: 32px !important;
            max-height: 32px !important;
            line-height: 32px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 0 8px !important;
            font-size: 12px !important;
            color: #1e293b !important;
            background-color: #fff !important;
            margin: 0 4px !important;
            outline: none !important;
        }

        .dt-container .dt-length label,
        .dt-container .dt-info {
            font-size: 12px !important;
            color: #64748b !important;
        }

        .dt-container .dt-info {
            padding-top: 14px !important;
        }

        /* Paging Buttons */
        .dt-container .dt-paging {
            padding-top: 10px !important;
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 6px !important;
            font-size: 12px !important;
            padding: 4px 10px !important;
            margin: 0 2px !important;
            border: 1px solid transparent !important;
            transition: all 0.15s ease !important;
        }

        .dt-container .dt-paging .dt-paging-button.current,
        .dt-container .dt-paging .dt-paging-button.current:hover {
            background: #071a33 !important;
            color: #ffffff !important;
            border-color: #071a33 !important;
            font-weight: 700 !important;
        }

        .dt-container .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) {
            background: #e2e8f0 !important;
            color: #071a33 !important;
            border-color: #cbd5e1 !important;
        }

        .dt-container .dt-paging .dt-paging-button.disabled {
            opacity: 0.4 !important;
        }

        .table-responsive {
            min-height: 320px;
            overflow: visible !important;
        }

        #tableKelas_wrapper .dataTables_scroll,
        #tableKelas_wrapper .dataTables_scrollBody {
            overflow: visible !important;
        }

        #tableKelas {
            width: 100% !important;
        }

        #tableKelas .dropdown {
            position: relative !important;
            display: inline-block;
        }

        #tableKelas .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: auto !important;
            margin-top: 4px !important;
            z-index: 1065 !important;
        }

        #tableKelas button[data-bs-toggle="dropdown"] * {
            pointer-events: none;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initKelasTable() {
            var tableEl = document.getElementById('tableKelas');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableKelas')) {
                $('#tableKelas').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Baru
            if ($.fn.DataTable) {
                window.dtTable = $('#tableKelas').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true, // Pastikan sorting hanya di row header pertama
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [3, 'asc'] // Default order by Nama Kelas
                    ],
                    ajax: '{{ route('kelas.dt') }}',
                    columns: [
                        // Kolom 0: Checkbox Baris
                        {
                            data: null,
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                return '<input class="form-check-input check-data-item" type="checkbox" value="' +
                                    data.id + '">';
                            }
                        },

                        // Kolom 1: Aksi Dropdown & Shortcut Kelola Materi
                        {
                            data: null,
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                let editUrl = "{{ route('kelas.edit', ':id') }}".replace(':id', row.id);
                                let materiUrl = "{{ route('materi.detail', ':id') }}".replace(':id', row.id);
                                let identity = String(data.title || '').replace(/'/g, "\\'");

                                let actions = `
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-warning" href="${editUrl}" wire:navigate>
                                        <i class="ri-edit-line"></i> Edit Kelas
                                    </a>
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-primary" href="${materiUrl}" wire:navigate>
                                        <i class="ri-book-open-line"></i> Kelola Materi
                                    </a>
                                    <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalDelete"
                                       wire:click="hookModalDelete(${data.id}, '${identity}')">
                                        <i class="ri-delete-bin-line"></i> Hapus
                                    </button>
                                `;

                                return `
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="${materiUrl}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                                        title="Kelola Kurikulum & Materi"
                                        style="padding: 4px 7px; font-size: 13px; border-radius: 6px;"
                                        wire:navigate>
                                        <i class="ri-book-open-line"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-light border text-dark"
                                            data-bs-toggle="dropdown"
                                            data-bs-display="static"
                                            aria-expanded="false"
                                            style="padding: 4px 8px; font-size: 12px; border-radius: 6px;">
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-start shadow-sm border-0 p-2" style="border-radius: 10px; min-width: 175px;">
                                            ${actions}
                                        </div>
                                    </div>
                                </div>
                            `;
                            }
                        },

                        // Kolom 2: Nomor Urut Otomatis
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-medium text-muted',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        // Kolom 3: Nama Pelatihan, Tipe, & Indikator Kurikulum
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let materiUrl = "{{ route('materi.detail', ':id') }}".replace(':id', row.id);
                                let typeBadge = '';
                                if (row.type === 'permanent') {
                                    typeBadge =
                                        '<span class="badge bg-success-subtle text-success" style="font-size: 10.5px;"><i class="ri-infinite-line me-1"></i>Permanen (Self-Paced)</span>';
                                } else if (row.type === 'paid' || row.type === 'berbayar') {
                                    let priceFormatted = row.price ? 'Rp ' + Number(row.price).toLocaleString('id-ID') : 'Rp 0';
                                    typeBadge =
                                        `<span class="badge bg-warning-subtle text-dark border border-warning" style="font-size: 10.5px;"><i class="ri-money-dollar-circle-line me-1"></i>Berbayar (${priceFormatted})</span>`;
                                } else {
                                    let startDate = row.start_date ? row.start_date.substring(0, 10) : '';
                                    let endDate = row.end_date ? row.end_date.substring(0, 10) : '';
                                    let dates = (startDate && endDate) ? ` (${startDate} s.d ${endDate})` :
                                        '';
                                    typeBadge =
                                        `<span class="badge bg-primary-subtle text-primary" style="font-size: 10.5px;"><i class="ri-calendar-line me-1"></i>Batch${dates}</span>`;
                                }

                                let curriculumBadge = '';
                                let chaptersCount = Number(row.chapters_count || 0);
                                let lessonsCount = Number(row.lessons_count || 0);

                                if (chaptersCount > 0) {
                                    curriculumBadge = `<span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size: 10.5px;">
                                        <i class="ri-book-open-line me-1"></i>${chaptersCount} Bab &bull; ${lessonsCount} Materi
                                    </span>`;
                                } else {
                                    curriculumBadge = `<span class="badge bg-secondary-subtle text-muted border border-secondary-subtle" style="font-size: 10.5px;">
                                        <i class="ri-book-line me-1"></i>0 Bab (Belum ada materi)
                                    </span>`;
                                }

                                return `
                                <div>
                                    <div class="fw-semibold text-dark mb-1" style="font-size: 13px; line-height: 1.4;">
                                        <a href="${materiUrl}" class="text-dark text-decoration-none" wire:navigate>${data}</a>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        ${typeBadge}
                                        ${curriculumBadge}
                                    </div>
                                </div>
                            `;
                            }
                        },

                        // Kolom 4: Kategori (Teks Netral Tanpa Badge)
                        {
                            data: 'category.name',
                            name: 'category.name',
                            orderable: false,
                            searchable: true,
                            className: 'text-secondary',
                            render: function(data, type, row) {
                                return row.category ? row.category.name : '-';
                            }
                        },

                        // Kolom 5: Status Diklat (7 PRD Lifecycle States)
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                if (data === 'published') {
                                    return '<span class="badge bg-success text-white px-2 py-1"><i class="ri-broadcast-line me-1"></i>Dibuka</span>';
                                } else if (data === 'submitted') {
                                    return '<span class="badge bg-info text-white px-2 py-1"><i class="ri-send-plane-line me-1"></i>Diajukan</span>';
                                } else if (data === 'approved') {
                                    return '<span class="badge bg-primary text-white px-2 py-1"><i class="ri-checkbox-circle-line me-1"></i>Disetujui</span>';
                                } else if (data === 'ongoing') {
                                    return '<span class="badge bg-warning text-dark px-2 py-1"><i class="ri-play-circle-line me-1"></i>Berjalan</span>';
                                } else if (data === 'completed') {
                                    return '<span class="badge bg-dark text-white px-2 py-1"><i class="ri-check-double-line me-1"></i>Selesai</span>';
                                } else if (data === 'archived') {
                                    return '<span class="badge bg-secondary text-white px-2 py-1"><i class="ri-archive-line me-1"></i>Diarsipkan</span>';
                                }
                                return '<span class="badge bg-simple-gold text-dark px-2 py-1"><i class="ri-draft-line me-1"></i>Draft</span>';
                            }
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada data kelas pelatihan',
                        zeroRecords: 'Belum ada data kelas pelatihan'
                    },
                    initComplete: function(settings) {
                        var table = settings.oInstance.api();

                        // 3. Filter Kolom Input pada Thead Kedua (#header-filter)
                        $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                            var colIndex = $(this).closest('th').index();
                            if (table.column(colIndex).search() !== this.value) {
                                table.column(colIndex).search(this.value).draw();
                            }
                        });

                        // 4. Checkbox Pilih Semua
                        $('.check-data-all').on('change', function() {
                            $('.check-data-item').prop('checked', this.checked);
                        });
                    }
                });
            }
        }

        // 5. Lifecycle Pengaktifan Tabel:
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initKelasTable);
        } else {
            initKelasTable();
        }

        // Saat navigasi SPA Livewire (wire:navigate)
        document.addEventListener('livewire:navigated', initKelasTable);

        window.addEventListener('reloadDT', function() {
            if (window.dtTable && typeof window.dtTable.ajax?.reload === 'function') {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
