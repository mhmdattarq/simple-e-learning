@push('css-stack')
    <style>
        .table-responsive {
            overflow: visible !important;
        }

        #tableAbsensi_wrapper .dataTables_scroll,
        #tableAbsensi_wrapper .dataTables_scrollBody {
            overflow: visible !important;
        }

        #tableAbsensi .dropdown {
            position: relative;
            display: inline-block;
        }

        #tableAbsensi .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1060 !important;
        }

        .dataTables_length label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .dataTables_length select {
            border-radius: 6px;
            padding: 3px 8px;
            border: 1px solid #dee2e6;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initAbsensiTable() {
            var tableEl = document.getElementById('tableAbsensi');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableAbsensi')) {
                $('#tableAbsensi').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Baru
            if ($.fn.DataTable) {
                window.dtTable = $('#tableAbsensi').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    ajax: {
                        url: "{{ route('absensi.dt') }}",
                        type: 'GET',
                        data: function(d) {
                            d.course_id = $('#filter_course_id').val();
                            d.date = $('#filter_date').val();
                            d.token_status = $('#filter_token_status').val();
                        }
                    },
                    dom: "<'row mb-2'<'col-sm-12 col-md-6'l>>" +
                        "<'row'<'col-sm-12'tr>>" +
                        "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                    order: [
                        [4, 'desc']
                    ], // Urutkan default berdasarkan Tanggal (Kolom 4: Tanggal & Waktu)
                    searchDelay: 400,
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

                        // Kolom 1: Nomor Urut Otomatis
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center fw-medium text-muted',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        // Kolom 2: Judul Sesi / Agenda
                        {
                            data: 'session_title',
                            name: 'session_title',
                            orderable: true,
                            searchable: true,
                            render: function(data) {
                                return `<span class="fw-bold text-dark d-block" style="font-size: 13.5px;">${data}</span>`;
                            }
                        },

                        // Kolom 3: Pelatihan
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

                        // Kolom 4: Tanggal & Waktu
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

                        // Kolom 5: Mentor Pengampu
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

                        // Kolom 6: Status & Token Sesi
                        {
                            data: 'token_badge',
                            name: 'attendance_token',
                            orderable: false,
                            searchable: true
                        },

                        // Kolom 7: Rekap Presensi
                        {
                            data: 'attendance_summary_badge',
                            name: 'id',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    language: {
                        search: "Cari Data:",
                        searchPlaceholder: "Ketik kata kunci pencarian...",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        zeroRecords: "Tidak ada data sesi absensi ditemukan",
                        info: "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ sesi)",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "❯",
                            previous: "❮"
                        }
                    }
                });
            }
        }

        // Lifecycle Inisialisasi Tabel: kompatibel penuh dengan reload halaman biasa & navigasi SPA (wire:navigate)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAbsensiTable);
        } else {
            initAbsensiTable();
        }

        document.addEventListener('livewire:navigated', function() {
            initAbsensiTable();
        });

        // Event Delegation untuk Filter & Aksi UI (Tetap aktif meskipun DOM berganti saat wire:navigate)
        $(document).off('change.absensiFilter', '#filter_course_id, #filter_date, #filter_token_status')
                   .on('change.absensiFilter', '#filter_course_id, #filter_date, #filter_token_status', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload();
            }
        });

        // Setup Custom Search Box dengan debounce 350ms (Event Delegation)
        let absensiSearchTimer;
        $(document).off('keyup.absensiSearch input.absensiSearch', '#custom_search_dt')
                   .on('keyup.absensiSearch input.absensiSearch', '#custom_search_dt', function() {
            clearTimeout(absensiSearchTimer);
            let keyword = this.value;
            absensiSearchTimer = setTimeout(function() {
                if (window.dtTable) {
                    window.dtTable.search(keyword).draw();
                }
            }, 350);
        });

        $(document).off('click.absensiRefresh', '#btn-refresh-absensi')
                   .on('click.absensiRefresh', '#btn-refresh-absensi', function() {
            if (window.dtTable) {
                window.dtTable.ajax.reload(null, false);
            }
        });

        $(document).off('click.absensiReset', '#btn-reset-filters')
                   .on('click.absensiReset', '#btn-reset-filters', function() {
            $('#filter_course_id').val('');
            $('#filter_date').val('');
            $('#filter_token_status').val('');
            $('#custom_search_dt').val('');
            if (window.dtTable) {
                window.dtTable.search('').ajax.reload();
            }
        });

        $(document).off('change.absensiCheckAll', '.check-data-all')
                   .on('change.absensiCheckAll', '.check-data-all', function() {
            $('.check-data-item').prop('checked', this.checked);
        });

        // Listener Event Livewire Modals & Reload DT
        function registerAbsensiLivewireEvents() {
            if (typeof Livewire === 'undefined' || window._absensiLivewireEventsRegistered) return;
            window._absensiLivewireEventsRegistered = true;

            Livewire.on('reloadDT', () => {
                if (window.dtTable) {
                    window.dtTable.ajax.reload(null, false);
                }
            });

            Livewire.on('open-modal-token', () => {
                var modalEl = document.getElementById('modalToken');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('open-modal-sheet', () => {
                var modalEl = document.getElementById('modalAttendanceSheet');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('open-modal-correction', () => {
                var modalEl = document.getElementById('modalCorrection');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                }
            });

            Livewire.on('close-modal-correction', () => {
                var modalEl = document.getElementById('modalCorrection');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    var modalInstance = bootstrap.Modal.getInstance(modalEl) || bootstrap.Modal.getOrCreateInstance(modalEl);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                }
            });

            Livewire.on('alert', (event) => {
                let data = Array.isArray(event) ? event[0] : event;
                let type = data.type || 'info';
                let message = data.message || '';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: type,
                        title: type === 'success' ? 'Berhasil!' : (type === 'error' ? 'Peringatan!' : 'Informasi'),
                        text: message,
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    alert(message);
                }
            });
        }

        if (typeof Livewire !== 'undefined') {
            registerAbsensiLivewireEvents();
        } else {
            document.addEventListener('livewire:init', registerAbsensiLivewireEvents);
        }

        // Global Event Delegation untuk Dropdown Bootstrap dalam Yajra DataTables
        $(document).on('show.bs.dropdown', '#tableAbsensi .dropdown', function() {
            var $menu = $(this).find('.dropdown-menu');
            $('body').append($menu.detach());
            var eOffset = $(this).offset();
            $menu.css({
                'display': 'block',
                'top': eOffset.top + $(this).outerHeight(),
                'left': eOffset.left - ($menu.outerWidth() - $(this).outerWidth())
            });
        });

        $(document).on('hide.bs.dropdown', '#tableAbsensi .dropdown', function() {
            $(this).append($('body > .dropdown-menu').detach());
            $('body > .dropdown-menu').hide();
        });
    </script>
@endpush
