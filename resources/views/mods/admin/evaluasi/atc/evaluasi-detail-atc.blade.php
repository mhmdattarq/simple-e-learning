@push('css')
    <style>
        /* === Custom Styling for SIMPEL BKPSDM Detail & Monitoring Evaluasi === */
        .stat-card-monitoring {
            background: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card-monitoring:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06) !important;
        }

        .stat-icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .quiz-curriculum-card {
            transition: all 0.2s ease;
        }

        .quiz-curriculum-card:hover {
            border-color: #cbd5e1 !important;
        }

        .final-quiz-card {
            border-left: 4px solid #f59e0b !important;
        }

        .transition-transform {
            transition: transform 0.2s ease-in-out;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* === Custom DataTables 2 Styling for SIMPEL BKPSDM Attempts Table === */
        #tableAttempts thead tr:first-child th {
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
            box-shadow: none !important;
        }

        /* Sort Arrows */
        #tableAttempts thead tr:first-child th.dt-orderable-asc,
        #tableAttempts thead tr:first-child th.dt-orderable-desc,
        #tableAttempts thead tr:first-child th.dt-ordering-asc,
        #tableAttempts thead tr:first-child th.dt-ordering-desc {
            padding-right: 28px !important;
            position: relative !important;
        }

        #tableAttempts thead tr:first-child th span.dt-column-order {
            position: absolute !important;
            right: 10px !important;
            top: 0 !important;
            bottom: 0 !important;
            width: 12px !important;
        }

        #tableAttempts thead tr:first-child th span.dt-column-order:before,
        #tableAttempts thead tr:first-child th span.dt-column-order:after {
            color: #94a3b8 !important;
            opacity: 0.5 !important;
        }

        #tableAttempts thead tr:first-child th.dt-ordering-asc span.dt-column-order:before,
        #tableAttempts thead tr:first-child th.dt-ordering-desc span.dt-column-order:after {
            color: #0f172a !important;
            opacity: 1 !important;
        }

        /* Second Row Header (Filter) */
        #header-filter-attempts th {
            background-color: #ffffff !important;
            padding: 8px 10px !important;
            border-bottom: 2px solid #e2e8f0 !important;
            cursor: default !important;
            outline: none !important;
            box-shadow: none !important;
        }

        #header-filter-attempts th::before,
        #header-filter-attempts th::after,
        #header-filter-attempts th span.dt-column-order {
            display: none !important;
            content: "" !important;
            visibility: hidden !important;
        }

        #header-filter-attempts input.search-col-dt {
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
        }

        #header-filter-attempts input.search-col-dt:focus {
            border-color: #94a3b8 !important;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.25) !important;
            outline: none !important;
        }

        #tableAttempts td {
            padding: 12px 12px !important;
            vertical-align: middle !important;
            font-size: 13px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #tableAttempts tbody tr:hover {
            background-color: #f8fafc !important;
        }

        #tableAttempts td.dt-empty {
            text-align: center !important;
            padding: 32px 12px !important;
            color: #64748b !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
        }
    </style>
@endpush

@push('js-stack')
    <script>
        function initAttemptsTable() {
            var tableEl = document.getElementById('tableAttempts');
            if (!tableEl) return;

            // 1. Hancurkan instance lama jika sudah terinisialisasi
            if ($.fn.DataTable && $.fn.DataTable.isDataTable('#tableAttempts')) {
                $('#tableAttempts').DataTable().destroy();
            }

            // 2. Inisialisasi DataTables Server-Side Baru untuk Riwayat Pengerjaan Peserta
            if ($.fn.DataTable) {
                window.dtAttemptsTable = $('#tableAttempts').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: false,
                    scrollX: false,
                    autoWidth: false,
                    orderCellsTop: true,
                    pageLength: 25,
                    dom: 'lrtip',
                    order: [
                        [6, 'desc'] // Default order by Waktu Submit terbaru
                    ],
                    ajax: '{{ route('evaluasi.detail.dt', $course->id) }}',
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

                        // Kolom 1: Nama Peserta & NIP / Email
                        {
                            data: 'user.name',
                            name: 'user.name',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let userName = data || 'Peserta';
                                let subInfo = '';
                                if (row.user?.nip) {
                                    subInfo = `NIP. ${row.user.nip}`;
                                } else if (row.user?.email) {
                                    subInfo = row.user.email;
                                }

                                return `
                                <div>
                                    <div class="fw-semibold text-dark mb-0" style="font-size: 13px;">${userName}</div>
                                    <small class="text-muted" style="font-size: 11px;">${subInfo}</small>
                                </div>
                                `;
                            }
                        },

                        // Kolom 2: Instrumen Kuis
                        {
                            data: 'quiz.title',
                            name: 'quiz.title',
                            orderable: true,
                            searchable: true,
                            render: function(data, type, row) {
                                let quizTitle = data || '-';
                                let isFinal = row.quiz?.type === 'final';
                                let badge = isFinal
                                    ? '<span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1" style="font-size: 10px;"><i class="ri-award-line me-1"></i>Ujian Akhir</span>'
                                    : '<span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1" style="font-size: 10px;"><i class="ri-booklet-line me-1"></i>Kuis Bab</span>';

                                return `
                                <div>
                                    <div class="fw-medium text-dark mb-1" style="font-size: 13px;">${quizTitle}</div>
                                    <div>${badge}</div>
                                </div>
                                `;
                            }
                        },

                        // Kolom 3: Skor Diperoleh
                        {
                            data: 'total_earned_score',
                            name: 'total_earned_score',
                            orderable: true,
                            searchable: false,
                            className: 'text-center',
                            render: function(data, type, row) {
                                let earned = data ?? 0;
                                let possible = row.total_possible_score ?? 0;
                                return `<span class="badge bg-light text-dark border px-2 py-1" style="font-size: 12px; font-weight: 600;">${earned} / ${possible} Poin</span>`;
                            }
                        },

                        // Kolom 4: Nilai Akhir (%)
                        {
                            data: 'percentage',
                            name: 'percentage',
                            orderable: true,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                let val = Number(data || 0).toFixed(1);
                                return `<span class="fw-bold text-dark" style="font-size: 13.5px;">${val}%</span>`;
                            }
                        },

                        // Kolom 5: Status Kelulusan
                        {
                            data: 'is_passed',
                            name: 'is_passed',
                            orderable: true,
                            searchable: false,
                            className: 'text-center',
                            render: function(data) {
                                if (data) {
                                    return '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px;"><i class="ri-checkbox-circle-line me-1"></i>Lulus</span>';
                                }
                                return '<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 11px;"><i class="ri-close-circle-line me-1"></i>Tidak Lulus</span>';
                            }
                        },

                        // Kolom 6: Waktu Submit
                        {
                            data: 'submitted_at',
                            name: 'submitted_at',
                            orderable: true,
                            searchable: false,
                            className: 'text-center text-muted',
                            render: function(data) {
                                if (!data) return '<span class="text-muted fst-italic">-</span>';
                                let dateObj = new Date(data);
                                if (isNaN(dateObj.getTime())) {
                                    return data.substring(0, 16);
                                }
                                return dateObj.toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });
                            }
                        }
                    ],
                    language: {
                        emptyTable: 'Belum ada peserta yang mengerjakan evaluasi pada kelas ini',
                        zeroRecords: 'Tidak ditemukan riwayat pengerjaan yang cocok'
                    },
                    initComplete: function(settings) {
                        var table = settings.oInstance.api();

                        // Filter Kolom Input pada Thead Kedua (#header-filter-attempts)
                        $('#header-filter-attempts input.search-col-dt').on('keyup change clear', function() {
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
            document.addEventListener('DOMContentLoaded', initAttemptsTable);
        } else {
            initAttemptsTable();
        }

        // Saat navigasi SPA Livewire (wire:navigate)
        document.addEventListener('livewire:navigated', initAttemptsTable);

        // Event reloadDT untuk reload ajax tabel secara realtime tanpa refresh halaman
        window.addEventListener('reloadDT', function() {
            if (window.dtAttemptsTable && typeof window.dtAttemptsTable.ajax?.reload === 'function') {
                window.dtAttemptsTable.ajax.reload(null, false);
            }
        });
    </script>
@endpush
