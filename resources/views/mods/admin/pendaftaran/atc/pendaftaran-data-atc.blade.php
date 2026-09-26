@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tablePendaftaran thead tr:first-child th {
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
        #tablePendaftaran thead th,
        #tablePendaftaran thead th:hover,
        #tablePendaftaran thead th:focus,
        #tablePendaftaran thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tablePendaftaran thead tr:first-child th.dt-orderable-asc:hover,
        #tablePendaftaran thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tablePendaftaran thead tr:first-child th.dt-orderable-asc,
        #tablePendaftaran thead tr:first-child th.dt-orderable-desc,
        #tablePendaftaran thead tr:first-child th.dt-ordering-asc,
        #tablePendaftaran thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tablePendaftaran thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tablePendaftaran thead tr:first-child th span.dt-column-order:before,
        #tablePendaftaran thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tablePendaftaran thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tablePendaftaran thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
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
        #tablePendaftaran td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tablePendaftaran tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tablePendaftaran td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        #tablePendaftaran tbody tr:hover td.dt-empty,
        #tablePendaftaran tbody tr.dt-empty:hover td {
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

        #tablePendaftaran {
            width: 100% !important;
        }

        #tablePendaftaran .dropdown {
            position: relative;
            display: inline-block;
        }

        #tablePendaftaran .dropdown-menu {
            z-index: 1065 !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initPendaftaranTable() {
            var tableEl = document.getElementById('tablePendaftaran');
            if (!tableEl) return;

            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePendaftaran')) {
                $('#tablePendaftaran').DataTable().destroy();
            }

            if ($.fn.DataTable) {
                window.dtTable = $('#tablePendaftaran').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [5, 'desc'] // Default order by Tgl Daftar
                    ],
                    ajax: '{{ route('pendaftaran.dt') }}',
                    columns: [
                        // Kolom 0: Nomor Urut Otomatis
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-medium text-muted',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        // Kolom 1: No. Registrasi
                        {
                            data: 'registration_number',
                            name: 'registration_number',
                            orderable: true,
                            searchable: true,
                            className: 'fw-semibold text-navy',
                            render: function(data) {
                                return `<span class="badge bg-light text-navy border border-simpel font-monospace px-2 py-1">${data}</span>`;
                            }
                        },

                        // Kolom 2: Nama Peserta
                        {
                            data: 'user_name',
                            name: 'user.name',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                let email = row.user ? (row.user.email || '') : '';
                                return `
                                    <div>
                                        <span class="fw-bold text-dark d-block">${data}</span>
                                        <small class="text-muted fs-8">${email}</small>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 3: NIP & Instansi
                        {
                            data: 'user_nip',
                            name: 'user.nip',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                let opd = row.user_opd || '-';
                                return `
                                    <div>
                                        <span class="fw-medium text-dark d-block">${data}</span>
                                        <small class="text-muted fs-8">${opd}</small>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 4: Nama Pelatihan
                        {
                            data: 'course_title',
                            name: 'course.title',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                let code = row.course_code || '';
                                return `
                                    <div>
                                        <span class="badge bg-secondary-subtle text-secondary fs-8 mb-1">${code}</span>
                                        <span class="fw-semibold text-dark d-block line-clamp-1">${data}</span>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 5: Tanggal Daftar
                        {
                            data: 'enrolled_at_formatted',
                            name: 'enrolled_at',
                            orderable: true,
                            searchable: false,
                            className: 'text-center fs-8 text-muted'
                        },

                        // Kolom 6: Status
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            className: 'text-center'
                        },

                        // Kolom 7: Aksi Detail
                        {
                            data: 'id',
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `
                                    <button type="button" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 py-1 px-2 rounded-pill btn-detail-pendaftaran" data-id="${data}" style="font-size: 11.5px; white-space: nowrap;">
                                        <i class="ri-eye-line"></i> Detail
                                    </button>
                                `;
                            }
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada data pendaftaran diklat',
                        zeroRecords: 'Belum ada data pendaftaran diklat'
                    }
                });

                // Setup Search Per Kolom
                $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                    let colIdx = $(this).parent().index();
                    if (window.dtTable.column(colIdx).search() !== this.value) {
                        window.dtTable.column(colIdx).search(this.value).draw();
                    }
                });

                // Listener untuk tombol Detail Pendaftaran membuka modal Livewire
                $('#tablePendaftaran').off('click', '.btn-detail-pendaftaran').on('click', '.btn-detail-pendaftaran', function(e) {
                    e.preventDefault();
                    let regId = $(this).data('id');
                    let lwEl = document.getElementById('tablePendaftaran')?.closest('[wire\\:id]');
                    if (lwEl && window.Livewire) {
                        let component = Livewire.find(lwEl.getAttribute('wire:id'));
                        if (component) {
                            component.call('showDetail', regId);
                        }
                    }
                });
            }
        }

        // Initialize on DOM ready and Livewire navigation
        $(document).ready(function() {
            initPendaftaranTable();
        });

        document.addEventListener('livewire:navigated', function() {
            initPendaftaranTable();
        });

        window.addEventListener('reloadDT', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
