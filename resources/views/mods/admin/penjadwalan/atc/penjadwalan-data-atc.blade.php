@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        /* First Row Header: Clean Neutral Light */
        #tablePenjadwalan thead tr:first-child th {
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
        #tablePenjadwalan thead th,
        #tablePenjadwalan thead th:hover,
        #tablePenjadwalan thead th:focus,
        #tablePenjadwalan thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        /* Subtle light hover on orderable headers */
        #tablePenjadwalan thead tr:first-child th.dt-orderable-asc:hover,
        #tablePenjadwalan thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        /* Reserve space for sort arrows on orderable columns to prevent text collision */
        #tablePenjadwalan thead tr:first-child th.dt-orderable-asc,
        #tablePenjadwalan thead tr:first-child th.dt-orderable-desc,
        #tablePenjadwalan thead tr:first-child th.dt-ordering-asc,
        #tablePenjadwalan thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        /* Sort Arrows in First Row: Neutral Slate */
        #tablePenjadwalan thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tablePenjadwalan thead tr:first-child th span.dt-column-order:before,
        #tablePenjadwalan thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tablePenjadwalan thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tablePenjadwalan thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
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
        #tablePenjadwalan td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tablePenjadwalan tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tablePenjadwalan td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            box-shadow: none !important;
        }

        #tablePenjadwalan tbody tr:hover td.dt-empty,
        #tablePenjadwalan tbody tr.dt-empty:hover td {
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

        #tablePenjadwalan {
            width: 100% !important;
        }

        #tablePenjadwalan .dropdown {
            position: relative;
            display: inline-block;
        }

        #tablePenjadwalan .dropdown-menu {
            z-index: 1065 !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initPenjadwalanTable() {
            var tableEl = document.getElementById('tablePenjadwalan');
            if (!tableEl) return;

            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePenjadwalan')) {
                $('#tablePenjadwalan').DataTable().destroy();
            }

            if ($.fn.DataTable) {
                window.dtTable = $('#tablePenjadwalan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [5, 'asc'] // Default order by Tanggal & Waktu Sesi
                    ],
                    ajax: '{{ route('penjadwalan.dt') }}',
                    columns: [
                        // Kolom 0: Checkbox
                        {
                            data: null,
                            name: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                return '<input class="form-check-input check-data-item" type="checkbox" value="' + data.id + '">';
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
                                let url = "{{ route('penjadwalan.edit', ':id') }}";
                                let editUrl = url.replace(':id', row.id);
                                let titleSafe = String(data.session_title || '').replace(/'/g, "\\'");

                                return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border text-dark" data-bs-toggle="dropdown" data-bs-strategy="fixed" data-bs-boundary="window" aria-expanded="false" style="padding: 4px 8px; font-size: 12px; border-radius: 6px;">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="border-radius: 10px; min-width: 160px;">
                                        <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-warning" href="${editUrl}" wire:navigate>
                                            <i class="ri-edit-line"></i> Edit Jadwal
                                        </a>
                                        <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                           data-bs-toggle="modal"
                                           data-bs-target="#modalDelete"
                                           wire:click="hookModalDelete(${data.id}, '${titleSafe}')">
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

                        // Kolom 3: Judul Sesi / Agenda
                        {
                            data: 'session_title',
                            name: 'session_title',
                            orderable: true,
                            searchable: true,
                            render: function(data) {
                                return `<span class="fw-bold text-dark d-block" style="font-size: 13.5px;">${data}</span>`;
                            }
                        },

                        // Kolom 4: Pelatihan
                        {
                            data: 'course_title',
                            name: 'course.title',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let code = row.course_code ? `<span class="badge bg-light text-secondary me-1" style="font-size: 11px;">${row.course_code}</span>` : '';
                                return `
                                    <div>
                                        ${code}
                                        <span class="text-dark fw-medium">${data}</span>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 5: Tanggal & Waktu
                        {
                            data: 'session_date_formatted',
                            name: 'session_date',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                return `
                                    <div>
                                        <div class="fw-semibold text-dark"><i class="ri-calendar-line text-muted me-1"></i>${data}</div>
                                        <small class="text-muted"><i class="ri-time-line me-1"></i>${row.time_range}</small>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 6: Mentor Pengampu
                        {
                            data: 'mentor_name',
                            name: 'mentor.name',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let nip = row.mentor_nip ? `<small class="text-muted d-block">NIP: ${row.mentor_nip}</small>` : '';
                                return `
                                    <div>
                                        <span class="fw-medium text-dark">${data}</span>
                                        ${nip}
                                    </div>
                                `;
                            }
                        },

                        // Kolom 7: Ruangan / Link
                        {
                            data: 'location_badge',
                            name: 'room_or_link',
                            orderable: false,
                            searchable: true
                        },

                        // Kolom 8: Status
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            className: 'text-center'
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada data jadwal pelatihan',
                        zeroRecords: 'Belum ada data jadwal pelatihan'
                    },
                    initComplete: function(settings) {
                        var table = settings.oInstance.api();

                        // Filter per Kolom
                        $('#header-filter input.search-col-dt').on('keyup change clear', function() {
                            var colIndex = $(this).closest('th').index();
                            if (table.column(colIndex).search() !== this.value) {
                                table.column(colIndex).search(this.value).draw();
                            }
                        });

                        // Checkbox Pilih Semua
                        $('.check-data-all').on('change', function() {
                            $('.check-data-item').prop('checked', this.checked);
                        });
                    }
                });
            }
        }

        // Initialize on DOM ready and Livewire navigation
        $(document).ready(function() {
            initPenjadwalanTable();
        });

        document.addEventListener('livewire:navigated', function() {
            initPenjadwalanTable();
        });

        window.addEventListener('reloadDT', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
