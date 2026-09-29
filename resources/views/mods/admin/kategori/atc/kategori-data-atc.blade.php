@push('css')
    <style>
        /* === Custom DataTables Styling for Kategori SIMPEL BKPSDM === */
        .dt-container {
            font-family: 'Inter', -apple-system, sans-serif;
        }

        #tableKategori thead tr:first-child th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            border-top: 1px solid #e2e8f0 !important;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 12px 12px !important;
            vertical-align: middle !important;
            outline: none !important;
            box-shadow: none !important;
        }

        table.dataTable thead>tr>th:hover,
        table.dataTable thead>tr>th:focus,
        table.dataTable thead>tr>th:active,
        #tableKategori thead th {
            outline: none !important;
            box-shadow: none !important;
        }

        #tableKategori thead tr:first-child th.dt-orderable-asc:hover,
        #tableKategori thead tr:first-child th.dt-orderable-desc:hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            cursor: pointer !important;
        }

        #tableKategori thead tr:first-child th.dt-orderable-asc,
        #tableKategori thead tr:first-child th.dt-orderable-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        #header-filter-kategori th {
            background-color: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            cursor: default !important;
        }

        #header-filter-kategori th span.dt-column-order {
            display: none !important;
        }

        #header-filter-kategori input.search-col-dt {
            font-size: 11.5px !important;
            padding: 4px 8px !important;
            border-radius: 6px !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            background-color: #f8fafc !important;
            font-weight: 400 !important;
        }

        #header-filter-kategori input.search-col-dt:focus {
            background-color: #ffffff !important;
            border-color: #f3bc42 !important;
            box-shadow: 0 0 0 2px rgba(243, 188, 66, 0.25) !important;
            outline: none !important;
        }

        #tableKategori tbody tr {
            transition: background-color 0.15s ease;
        }

        #tableKategori tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tableKategori tbody td {
            padding: 12px 12px !important;
            font-size: 13px !important;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9 !important;
            vertical-align: middle !important;
        }

        .table-responsive {
            min-height: 280px;
            overflow: visible !important;
        }

        #tableKategori_wrapper .dataTables_scroll,
        #tableKategori_wrapper .dataTables_scrollBody {
            overflow: visible !important;
        }

        #tableKategori {
            width: 100% !important;
        }

        #tableKategori .dropdown {
            position: relative !important;
            display: inline-block;
        }

        #tableKategori .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            left: 0 !important;
            right: auto !important;
            margin-top: 4px !important;
            z-index: 1065 !important;
        }

        #tableKategori button[data-bs-toggle="dropdown"] * {
            pointer-events: none;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initKategoriTable() {
            var tableEl = document.getElementById('tableKategori');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableKategori')) {
                $('#tableKategori').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Baru
            if ($.fn.DataTable) {
                window.dtKategoriTable = $('#tableKategori').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [3, 'asc'] // Default order by Nama Kategori
                    ],
                    ajax: '{{ route('kategori.dt') }}',
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
                                let url = "{{ route('kategori.edit', ':id') }}";
                                let editUrl = url.replace(':id', row.id);
                                let identity = String(data.name || '').replace(/'/g, "\\'");

                                let actions = `
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 px-3 rounded text-warning" href="${editUrl}" wire:navigate>
                                        <i class="ri-edit-line"></i> Edit Kategori
                                    </a>
                                    <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2 px-3 rounded border-0 bg-transparent w-100 text-start"
                                       data-bs-toggle="modal"
                                       data-bs-target="#modalDelete"
                                       wire:click="hookModalDelete(${data.id}, '${identity}')">
                                        <i class="ri-delete-bin-line"></i> Hapus
                                    </button>
                                `;

                                return `
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

                        // Kolom 3: Nama Kategori
                        {
                            data: 'name',
                            name: 'name',
                            render: function(data) {
                                return '<div class="fw-semibold text-dark fs-7">' + (data || '-') + '</div>';
                            }
                        },


                        // Kolom 5: Deskripsi
                        {
                            data: 'description',
                            name: 'description',
                            render: function(data) {
                                if (!data) {
                                    return '<span class="text-muted fst-italic fs-8">Tidak ada deskripsi</span>';
                                }
                                let truncated = data.length > 80 ? data.substring(0, 80) + '...' : data;
                                return '<span class="text-muted fs-8" title="' + data.replace(/"/g, '&quot;') + '">' + truncated + '</span>';
                            }
                        },

                        // Kolom 6: Total Kelas
                        {
                            data: 'courses_count',
                            name: 'courses_count',
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                let count = parseInt(data) || 0;
                                if (count > 0) {
                                    return '<span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 radius-6 fs-8 fw-bold">' +
                                        '<i class="ri-book-read-line me-1"></i>' + count + ' Kelas</span>';
                                }
                                return '<span class="badge bg-light text-muted border px-2 py-1 radius-6 fs-8">0 Kelas</span>';
                            }
                        }
                    ],
                    language: {
                        processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Memuat data kategori...',
                        emptyTable: 'Belum ada data kategori kelas',
                        zeroRecords: 'Kategori tidak ditemukan',
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori',
                        infoEmpty: 'Menampilkan 0 data',
                        infoFiltered: '(disaring dari _MAX_ total kategori)',
                        paginate: {
                            first: '<i class="ri-skip-back-line"></i>',
                            previous: '<i class="ri-arrow-left-s-line"></i>',
                            next: '<i class="ri-arrow-right-s-line"></i>',
                            last: '<i class="ri-skip-forward-line"></i>'
                        }
                    }
                });

                // 3. Pencarian Kolom Khusus (Individual Column Filtering)
                $('#header-filter-kategori input.search-col-dt').on('keyup change clear', function() {
                    let colIdx = $(this).closest('th').index();
                    if (window.dtKategoriTable.column(colIdx).search() !== this.value) {
                        window.dtKategoriTable.column(colIdx).search(this.value).draw();
                    }
                });

                // 4. Checkbox Master All Toggle
                $('#tableKategori').on('click', '.check-data-all', function() {
                    let isChecked = $(this).is(':checked');
                    $('#tableKategori tbody .check-data-item').prop('checked', isChecked);
                });
            }
        }

        // Jalankan saat script dimuat pertama kali
        document.addEventListener('DOMContentLoaded', function() {
            initKategoriTable();
        });

        // Jalankan ulang setiap kali navigasi SPA Livewire selesai
        document.addEventListener('livewire:navigated', function() {
            initKategoriTable();
        });

        // Tangkap event reloadDT dari Livewire component
        window.addEventListener('reloadDT', function() {
            if (window.dtKategoriTable) {
                window.dtKategoriTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
