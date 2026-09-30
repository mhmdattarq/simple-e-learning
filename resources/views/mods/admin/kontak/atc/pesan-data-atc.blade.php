@push('css')
    <style>
        #tablePesan thead tr:first-child th {
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
        }

        #header-filter-pesan th {
            background-color: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        #tablePesan td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tablePesan tbody tr:hover {
            background-color: #f8fafc !important;
        }

        .unread-row {
            background-color: #fff9f9 !important;
            font-weight: 500;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function escapeHtml(text) {
            if (!text) return '';
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function initPesanTable() {
            var tableEl = document.getElementById('tablePesan');
            if (!tableEl) return;

            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tablePesan')) {
                $('#tablePesan').DataTable().destroy();
            }

            if ($.fn.DataTable) {
                window.dtPesanTable = $('#tablePesan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    ajax: {
                        url: '{{ route('kontak.pesan.dt') }}',
                        type: 'GET',
                        data: function(d) {
                            d.status = $('#header-filter-pesan select').val();
                        }
                    },
                    order: [[6, 'desc']], // Urutkan berdasarkan waktu kirim terbaru
                    createdRow: function(row, data) {
                        if (data.status === 'unread') {
                            $(row).addClass('unread-row');
                        }
                    },
                    columns: [
                        // Kolom 0: Nomor Urut
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center text-muted fw-semibold',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },

                        // Kolom 1: Aksi
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                var msgId = parseInt(row.id || data);
                                var safeName = String(row.name || '').replace(/'/g, "\\'");
                                return `
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-circle w-32-px h-32-px d-inline-flex align-items-center justify-content-center"
                                            onclick="openPesanDetail(${msgId})" title="Lihat Detail Pesan">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle w-32-px h-32-px d-inline-flex align-items-center justify-content-center"
                                            onclick="deletePesan(${msgId}, '${safeName}')" title="Hapus Pesan">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        },

                        // Kolom 2: Pengirim
                        {
                            data: 'name',
                            name: 'name',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                var phoneBadge = '';
                                if (row.phone) {
                                    phoneBadge = `<div class="text-success mt-0_5" style="font-size: 11px;"><i class="ri-whatsapp-line me-1"></i>${escapeHtml(row.phone)}</div>`;
                                }
                                return `
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 13.5px;">${escapeHtml(data)}</div>
                                        <small class="text-muted" style="font-size: 11.5px;">${escapeHtml(row.email)}</small>
                                        ${phoneBadge}
                                    </div>
                                `;
                            }
                        },

                        // Kolom 3: Topik Pertanyaan
                        {
                            data: 'subject',
                            name: 'subject',
                            orderable: true,
                            searchable: true,
                            render: function(data) {
                                return `<span class="fw-semibold text-dark" style="font-size: 13px;">${escapeHtml(data)}</span>`;
                            }
                        },

                        // Kolom 4: Pratinjau Pesan
                        {
                            data: 'message',
                            name: 'message',
                            orderable: false,
                            searchable: true,
                            render: function(data) {
                                if (!data) return '-';
                                var snippet = data.length > 75 ? data.substring(0, 75) + '...' : data;
                                return `<span class="text-muted fs-7" title="${escapeHtml(data)}">${escapeHtml(snippet)}</span>`;
                            }
                        },

                        // Kolom 5: Status
                        {
                            data: 'status',
                            name: 'status',
                            orderable: true,
                            searchable: true,
                            className: 'text-center',
                            render: function(data) {
                                if (data === 'unread') {
                                    return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 radius-6" style="font-size: 11px;"><i class="ri-mail-unread-line me-1"></i>Belum Dibaca</span>';
                                } else if (data === 'read') {
                                    return '<span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 radius-6" style="font-size: 11px;"><i class="ri-mail-open-line me-1"></i>Dibaca</span>';
                                } else if (data === 'replied') {
                                    return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 radius-6" style="font-size: 11px;"><i class="ri-checkbox-circle-line me-1"></i>Dibalas</span>';
                                }
                                return `<span class="badge bg-light text-dark">${escapeHtml(data)}</span>`;
                            }
                        },

                        // Kolom 6: Waktu Kirim
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: true,
                            searchable: false,
                            className: 'text-center text-muted fs-8',
                            render: function(data, type, row) {
                                return row.formatted_date || data;
                            }
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada pesan yang masuk di kotak pesan.',
                        zeroRecords: 'Tidak ditemukan pesan yang cocok dengan pencarian.',
                        processing: '<div class="spinner-border spinner-border-sm text-primary" role="status"></div> Memuat pesan...'
                    },
                    initComplete: function(settings) {
                        var table = settings.oInstance.api();

                        // Filter Pengirim (index 2)
                        $('#header-filter-pesan input:eq(0)').on('keyup change clear', function() {
                            if (table.column(2).search() !== this.value) {
                                table.column(2).search(this.value).draw();
                            }
                        });

                        // Filter Topik (index 3)
                        $('#header-filter-pesan input:eq(1)').on('keyup change clear', function() {
                            if (table.column(3).search() !== this.value) {
                                table.column(3).search(this.value).draw();
                            }
                        });

                        // Filter Status dropdown (index 5)
                        $('#header-filter-pesan select').on('change', function() {
                            table.ajax.reload();
                        });
                    }
                });
            }
        }

        function getPesanLivewire() {
            const el = document.getElementById('kontak-pesan-root');
            return (el && window.Livewire) ? Livewire.find(el.getAttribute('wire:id')) : null;
        }

        // Bridge to Livewire for Opening Detail Modal
        function openPesanDetail(id) {
            if (window.Livewire) {
                Livewire.dispatch('modal-detail-pesan-set', { id: id });
            }
        }

        // Bridge to Livewire for Delete Confirmation
        function deletePesan(id, name) {
            if (window.Livewire) {
                var payload = {
                    id: parseInt(id),
                    title: 'Konfirmasi Hapus Pesan',
                    msg: 'Apakah Anda yakin ingin menghapus pesan dari "' + name + '"? Tindakan ini tidak dapat dibatalkan.',
                    dispatch: 'KontakPesanData-delete'
                };
                Livewire.dispatch('modal-delete-setDeleteId', {
                    data: payload,
                    id: payload.id,
                    title: payload.title,
                    msg: payload.msg,
                    dispatch: payload.dispatch
                });
            }
        }

        // Lifecycle Hooks:
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPesanTable);
        } else {
            initPesanTable();
        }

        document.addEventListener('livewire:navigated', initPesanTable);

        window.addEventListener('reloadDT', function() {
            if (window.dtPesanTable && typeof window.dtPesanTable.ajax?.reload === 'function') {
                window.dtPesanTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
