@push('css')
    <style>
        /* === Custom DataTables 2 Styling for Persetujuan Pimpinan === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tablePersetujuan thead tr:first-child th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            border-color: #e2e8f0 !important;
            padding: 12px 14px !important;
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
        #tablePersetujuan thead th,
        #tablePersetujuan thead th:hover,
        #tablePersetujuan thead th:focus,
        #tablePersetujuan thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tablePersetujuan thead tr:first-child th.dt-orderable-asc:hover,
        #tablePersetujuan thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tablePersetujuan thead tr:first-child th.dt-orderable-asc,
        #tablePersetujuan thead tr:first-child th.dt-orderable-desc,
        #tablePersetujuan thead tr:first-child th.dt-ordering-asc,
        #tablePersetujuan thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tablePersetujuan thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tablePersetujuan thead tr:first-child th span.dt-column-order:before,
        #tablePersetujuan thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tablePersetujuan thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tablePersetujuan thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
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
            line-height: 32px !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 0 10px !important;
            font-size: 12px !important;
            width: 100% !important;
            transition: all 0.2s ease !important;
        }

        #header-filter input.search-col-dt:focus {
            border-color: #071a33 !important;
            box-shadow: 0 0 0 2px rgba(7, 26, 51, 0.1) !important;
            outline: none !important;
        }

        /* Table Body Rows */
        #tablePersetujuan td {
            padding: 14px 14px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tablePersetujuan tbody tr:hover {
            background-color: #f8fafc !important;
        }

        /* Paging Buttons */
        .dt-container .dt-paging {
            padding-top: 12px !important;
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

        #tablePersetujuan {
            width: 100% !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initPersetujuanTable() {
            var tableEl = document.getElementById('tablePersetujuan');
            if (!tableEl) return;

            // Destroy existing instance if present
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePersetujuan')) {
                $('#tablePersetujuan').DataTable().destroy();
            }

            if ($.fn.DataTable) {
                window.dtTable = $('#tablePersetujuan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [6, 'desc'] // Default order by Pengusul & Waktu (updated_at)
                    ],
                    ajax: '{{ route('pimpinan.persetujuan.dt') }}',
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

                        // Kolom 1: Aksi Tinjau Usulan
                        {
                            data: null,
                            name: 'courses.id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                const reviewUrl = `{{ url('/pimpinan/persetujuan/review') }}/${data.id}`;
                                return `
                                    <a href="${reviewUrl}" class="btn btn-sm btn-primary px-3 py-1 rounded-pill d-inline-flex align-items-center gap-1 shadow-sm fs-8 fw-semibold text-white"
                                        wire:navigate title="Tinjau rincian usulan pelatihan">
                                        <i class="ri-file-shield-line"></i> Tinjau
                                    </a>
                                `;
                            }
                        },

                        // Kolom 2: Kode Diklat
                        {
                            data: 'code',
                            name: 'courses.code',
                            orderable: true,
                            searchable: true,
                            className: 'font-monospace fw-semibold text-navy',
                            render: function(data) {
                                return `<span class="badge bg-light text-navy border border-simpel font-monospace px-2 py-1 fs-8">${data}</span>`;
                            }
                        },

                        // Kolom 3: Program Pelatihan & Kategori
                        {
                            data: 'title',
                            name: 'courses.title',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                return `
                                    <div>
                                        <span class="fw-bold text-dark d-block fs-7 lh-sm mb-1">${data}</span>
                                        <span class="text-muted fs-8 d-inline-flex align-items-center gap-1">
                                            <i class="ri-folder-3-line text-primary"></i> ${row.category_name}
                                        </span>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 4: Tipe & Metode
                        {
                            data: 'method_badge',
                            name: 'courses.method',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                return `
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        ${row.method_badge}
                                        ${row.type_badge}
                                    </div>
                                `;
                            }
                        },

                        // Kolom 5: Jadwal & Kuota
                        {
                            data: 'schedule_formatted',
                            name: 'courses.start_date',
                            orderable: true,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <div>
                                        ${data}
                                        <div class="mt-1 d-inline-flex align-items-center gap-1 text-muted fs-8">
                                            <i class="ri-user-3-line text-primary"></i> <span class="fw-semibold text-dark">${row.quota}</span> Peserta
                                        </div>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 6: Pengusul & Waktu
                        {
                            data: 'creator_name',
                            name: 'courses.updated_at',
                            orderable: true,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                                    <div>
                                        <div class="fw-medium text-dark fs-8">${data}</div>
                                        <div class="text-muted fs-8 mt-0_5">
                                            <i class="ri-time-line me-1 text-secondary"></i>${row.submitted_at_formatted}
                                        </div>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 7: Status
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: false,
                            searchable: false,
                            className: 'text-center'
                        }
                    ],
                    language: {
                        emptyTable: 'Tidak ada usulan rencana pelatihan yang menunggu persetujuan',
                        zeroRecords: 'Tidak ada usulan rencana pelatihan yang cocok'
                    }
                });

                // Setup Search Per Kolom
                $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                    let colIdx = $(this).parent().index();
                    if (window.dtTable.column(colIdx).search() !== this.value) {
                        window.dtTable.column(colIdx).search(this.value).draw();
                    }
                });
            }
        }

        // Initialize on DOM ready and Livewire navigation
        $(document).ready(function() {
            initPersetujuanTable();
        });

        document.addEventListener('livewire:navigated', function() {
            initPersetujuanTable();
        });

        window.addEventListener('reloadDT', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
