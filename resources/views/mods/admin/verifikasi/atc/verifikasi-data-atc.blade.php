@push('css')
    <style>
        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM (Exact match to Pendaftaran) === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        #tableVerifikasi thead tr:first-child th {
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

        table.dataTable thead>tr>th:hover,
        table.dataTable thead>tr>th:focus,
        table.dataTable thead>tr>th:active,
        table.dataTable thead>tr>th.dt-orderable-asc:hover,
        table.dataTable thead>tr>th.dt-orderable-desc:hover,
        table.dataTable thead>tr>td.dt-orderable-asc:hover,
        table.dataTable thead>tr>td.dt-orderable-desc:hover,
        #tableVerifikasi thead th,
        #tableVerifikasi thead th:hover,
        #tableVerifikasi thead th:focus,
        #tableVerifikasi thead th:active {
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
        }

        #tableVerifikasi thead tr:first-child th.dt-orderable-asc:hover,
        #tableVerifikasi thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        #tableVerifikasi thead tr:first-child th.dt-orderable-asc,
        #tableVerifikasi thead tr:first-child th.dt-orderable-desc,
        #tableVerifikasi thead tr:first-child th.dt-ordering-asc,
        #tableVerifikasi thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        #tableVerifikasi thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tableVerifikasi thead tr:first-child th span.dt-column-order:before,
        #tableVerifikasi thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tableVerifikasi thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tableVerifikasi thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
            color: #0f172a !important;
            opacity: 1 !important;
        }

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
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 11.5px;
            padding: 4px 8px;
            background-color: #f8fafc;
            transition: all 0.2s;
            outline: none !important;
            box-shadow: none !important;
        }

        #header-filter input.search-col-dt:focus {
            background-color: #ffffff;
            border-color: var(--simpel-navy, #071a33);
            box-shadow: 0 0 0 2px rgba(7, 26, 51, 0.1) !important;
            outline: none !important;
        }

        #tableVerifikasi tbody td {
            font-size: 13px !important;
            padding: 12px 12px !important;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            vertical-align: middle;
        }

        #tableVerifikasi tbody tr:hover td {
            background-color: #f8fafc !important;
        }

        #tableVerifikasi td.dt-empty {
            text-align: center !important;
            padding: 24px 12px !important;
            color: #64748b !important;
            font-size: 12.5px !important;
        }

        .dt-container .dt-length,
        .dt-container div.dt-length {
            margin-bottom: 16px !important;
        }

        .dt-container .dt-length select {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            padding: 4px 8px !important;
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

        .dt-container .dt-paging {
            padding-top: 10px !important;
        }

        .dt-container .dt-paging .dt-paging-button {
            border-radius: 6px !important;
            font-size: 12px !important;
            padding: 4px 10px !important;
            margin: 0 2px !important;
            border: 1px solid transparent !important;
            background: transparent !important;
            color: #475569 !important;
            box-shadow: none !important;
            outline: none !important;
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

        #tableVerifikasi .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1060 !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initVerifikasiTable() {
            var tableEl = document.getElementById('tableVerifikasi');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableVerifikasi')) {
                $('#tableVerifikasi').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Baru
            if ($.fn.DataTable) {
                window.dtTable = $('#tableVerifikasi').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true, // Pastikan sorting hanya di row header pertama
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [8, 'desc'] // Default order by Tgl Daftar
                    ],
                    ajax: '{{ route('verifikasi.dt') }}',
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
                                return `
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-light border text-dark" data-bs-toggle="dropdown" aria-expanded="false" style="padding: 4px 8px; font-size: 12px; border-radius: 6px;">
                                        <i class="ri-more-2-fill"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2" style="border-radius: 10px; min-width: 170px;">
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-navy border-0 bg-transparent w-100 text-start"
                                            wire:click="openVerifyModal(${data.id})">
                                            <i class="ri-shield-check-line text-success"></i> Periksa Berkas
                                        </button>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-navy border-0 bg-transparent w-100 text-start"
                                            wire:click="showDetail(${data.id})">
                                            <i class="ri-eye-line text-primary"></i> Detail Berkas
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

                        // Kolom 3: No. Registrasi
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

                        // Kolom 4: Nama Peserta
                        {
                            data: 'user_name',
                            name: 'user.name',
                            orderable: false,
                            searchable: true,
                            render: function(data, type, row) {
                                let email = row.user_email || (row.user ? row.user.email : '') || '';
                                return `
                                    <div>
                                        <span class="fw-bold text-dark d-block">${data}</span>
                                        <small class="text-muted fs-8">${email}</small>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 5: NIP & Instansi
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

                        // Kolom 6: Nama Pelatihan
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

                        // Kolom 7: Surat Tugas (PDF)
                        {
                            data: 'letter_url',
                            name: 'recommendation_letter_path',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                if (data) {
                                    return `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-pill fs-8">
                                        <i class="ri-file-pdf-line me-1"></i>PDF
                                    </a>`;
                                }
                                return `<span class="text-muted fs-8">-</span>`;
                            }
                        },

                        // Kolom 8: Tanggal Daftar
                        {
                            data: 'enrolled_at_formatted',
                            name: 'enrolled_at',
                            orderable: true,
                            searchable: false,
                            className: 'text-center fs-8 text-muted'
                        },

                        // Kolom 9: Status
                        {
                            data: 'status_badge',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            className: 'text-center'
                        }
                    ],
                    language: {
                        emptyTable: 'belum ada data verifikasi berkas',
                        zeroRecords: 'belum ada data verifikasi berkas',
                        processing: '<div class="d-flex align-items-center justify-content-center gap-2 text-primary my-3"><div class="spinner-border spinner-border-sm"></div> Memuat data verifikasi...</div>',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ berkas',
                        infoEmpty: 'Menampilkan 0 berkas',
                        infoFiltered: '(disaring dari _MAX_ total berkas)',
                        paginate: {
                            first: '<i class="ri-arrow-left-double-line"></i>',
                            previous: '<i class="ri-arrow-left-s-line"></i>',
                            next: '<i class="ri-arrow-right-s-line"></i>',
                            last: '<i class="ri-arrow-right-double-line"></i>'
                        }
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

        // Initialize on DOM ready and Livewire navigation
        $(document).ready(function() {
            initVerifikasiTable();
        });

        document.addEventListener('livewire:navigated', function() {
            initVerifikasiTable();
        });

        window.addEventListener('reloadDT', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
