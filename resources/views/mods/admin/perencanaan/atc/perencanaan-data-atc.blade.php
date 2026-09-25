@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tablePerencanaan thead tr:first-child th {
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
        #tablePerencanaan thead th,
        #tablePerencanaan thead th:hover,
        #tablePerencanaan thead th:focus,
        #tablePerencanaan thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tablePerencanaan thead tr:first-child th.dt-orderable-asc:hover,
        #tablePerencanaan thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tablePerencanaan thead tr:first-child th.dt-orderable-asc,
        #tablePerencanaan thead tr:first-child th.dt-orderable-desc,
        #tablePerencanaan thead tr:first-child th.dt-ordering-asc,
        #tablePerencanaan thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tablePerencanaan thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tablePerencanaan thead tr:first-child th span.dt-column-order:before,
        #tablePerencanaan thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tablePerencanaan thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tablePerencanaan thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
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
        #tablePerencanaan td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tablePerencanaan tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tablePerencanaan td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        #tablePerencanaan tbody tr:hover td.dt-empty,
        #tablePerencanaan tbody tr.dt-empty:hover td {
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
        }

        #tablePerencanaan {
            width: 100% !important;
        }

        #tablePerencanaan .dropdown {
            position: relative;
            display: inline-block;
        }

        #tablePerencanaan .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1060 !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initPerencanaanTable() {
            var tableEl = document.getElementById('tablePerencanaan');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePerencanaan')) {
                $('#tablePerencanaan').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Baru
            if ($.fn.DataTable) {
                window.dtTable = $('#tablePerencanaan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true, // Pastikan sorting hanya di row header pertama
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [3, 'asc'] // Default order by Kode Diklat
                    ],
                    ajax: '{{ route('perencanaan.dt') }}',
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

                        // Kolom 1: Aksi Dropdown
                        {
                            data: null,
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                let url = "{{ route('perencanaan.edit', ':id') }}";
                                let editUrl = url.replace(':id', row.id);
                                let identity = String(data.title || data.code || '').replace(/'/g, "\\'");
                                let submitBtn = '';

                                if (row.status === 'draft') {
                                    submitBtn = `
                                        <button type="button" class="dropdown-item text-primary d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                            wire:click="submitToLeader(${data.id})">
                                            <i class="ri-send-plane-line"></i> Ajukan ke Pimpinan
                                        </button>
                                    `;
                                } else if (row.status === 'approved' || row.status === 'draft') {
                                    submitBtn = `
                                        <button type="button" class="dropdown-item text-success d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                            wire:click="openRegistration(${data.id})">
                                            <i class="ri-broadcast-line"></i> Buka Pendaftaran
                                        </button>
                                    `;
                                } else if (row.status === 'published') {
                                    submitBtn = `
                                        <button type="button" class="dropdown-item text-warning d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                            wire:click="startCourse(${data.id})">
                                            <i class="ri-play-circle-line"></i> Mulai Pelatihan
                                        </button>
                                    `;
                                } else if (row.status === 'ongoing') {
                                    submitBtn = `
                                        <button type="button" class="dropdown-item text-dark d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                            wire:click="completeCourse(${data.id})">
                                            <i class="ri-check-double-line"></i> Selesaikan Pelatihan
                                        </button>
                                    `;
                                }

                                let archiveBtn = '';
                                if (row.status === 'completed' || row.status === 'draft') {
                                    archiveBtn = `
                                        <button type="button" class="dropdown-item text-secondary d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                            wire:click="archiveCourse(${data.id})">
                                            <i class="ri-archive-line"></i> Arsipkan
                                        </button>
                                    `;
                                }

                                return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border text-dark" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 4px 8px; font-size: 12px; border-radius: 6px;">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="border-radius: 10px; min-width: 185px;">
                                        ${submitBtn}
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-warning" href="${editUrl}" wire:navigate>
                                            <i class="ri-edit-line"></i> Edit Pelatihan
                                        </a>
                                        ${archiveBtn}
                                        <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                           data-bs-toggle="modal"
                                           data-bs-target="#modalDelete"
                                           wire:click="hookModalDelete(${data.id}, '${identity}')">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
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

                        // Kolom 3: Kode Diklat (Teks Netral Tanpa Badge)
                        {
                            data: 'code',
                            name: 'code',
                            orderable: true,
                            searchable: true,
                            className: 'fw-semibold text-dark',
                            render: function(data) {
                                return data;
                            }
                        },

                        // Kolom 4: Nama Pelatihan & Tipe
                        {
                            data: 'title',
                            name: 'title',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let typeBadge = '';
                                if (row.type === 'permanent') {
                                    typeBadge =
                                        '<span class="badge bg-success-subtle text-success ms-1" style="font-size: 10.5px;"><i class="ri-infinite-line me-1"></i>Buka Terus (Self-Paced)</span>';
                                } else {
                                    let startDate = row.start_date ? row.start_date.substring(0, 10) : '';
                                    let endDate = row.end_date ? row.end_date.substring(0, 10) : '';
                                    let dates = (startDate && endDate) ? ` (${startDate} s.d ${endDate})` :
                                        '';
                                    typeBadge =
                                        `<span class="badge bg-primary-subtle text-primary ms-1" style="font-size: 10.5px;"><i class="ri-calendar-line me-1"></i>Batch${dates}</span>`;
                                }

                                return `
                                <div>
                                    <div class="fw-semibold text-dark mb-1" style="font-size: 13px; line-height: 1.4;">${data}</div>
                                    <div>${typeBadge}</div>
                                </div>
                            `;
                            }
                        },

                        // Kolom 5: Kategori (Teks Netral Tanpa Badge)
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

                        // Kolom 6: Metode Pelatihan
                        {
                            data: 'method',
                            name: 'method',
                            orderable: true,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                if (data === 'luring') {
                                    return '<span class="badge bg-warning-subtle text-warning border-warning-subtle"><i class="ri-community-line me-1"></i>Tatap Muka (Luring)</span>';
                                } else if (data === 'hybrid') {
                                    return '<span class="badge bg-info-subtle text-info border-info-subtle"><i class="ri-shuffle-line me-1"></i>Hybrid</span>';
                                }
                                return '<span class="badge bg-success-subtle text-success border-success-subtle"><i class="ri-computer-line me-1"></i>Daring (Online)</span>';
                            }
                        },

                        // Kolom 7: Kuota Peserta
                        {
                            data: 'quota',
                            name: 'quota',
                            orderable: true,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                return `<span class="fw-semibold text-dark">${data || 0}</span> <small class="text-muted">ASN</small>`;
                            }
                        },

                        // Kolom 8: Status Diklat (7 PRD Lifecycle States)
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
                        emptyTable: 'Belum ada data perencanaan diklat',
                        zeroRecords: 'Belum ada data perencanaan diklat'
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
            document.addEventListener('DOMContentLoaded', initPerencanaanTable);
        } else {
            initPerencanaanTable();
        }

        // Saat navigasi SPA Livewire (wire:navigate)
        document.addEventListener('livewire:navigated', initPerencanaanTable);
    </script>
@endpush
