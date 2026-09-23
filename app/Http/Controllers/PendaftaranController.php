<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Repositories\PendaftaranRepo;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Yajra\DataTables\Facades\DataTables;

class PendaftaranController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern untuk penyediaan data Yajra DataTables pendaftaran peserta diklat.
     */
    public function dataDt()
    {
        $data = PendaftaranRepo::getDt();

        return DataTables::of($data)
            ->addColumn('user_name', fn ($row) => $row->user?->name ?? '-')
            ->addColumn('user_nip', fn ($row) => $row->user?->nip ?? '-')
            ->addColumn('user_opd', fn ($row) => $row->user?->opd_agency ?? '-')
            ->addColumn('course_title', fn ($row) => $row->course?->title ?? '-')
            ->addColumn('course_code', fn ($row) => $row->course?->code ?? '-')
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                $badgeClass = $status?->badgeClass() ?? 'bg-secondary text-white';
                $label = $status?->label() ?? ucfirst((string) $row->status);

                return '<span class="badge '.$badgeClass.' px-2_5 py-1">'.$label.'</span>';
            })
            ->addColumn('letter_url', function ($row) {
                return $row->recommendation_letter_path
                    ? asset('storage/'.$row->recommendation_letter_path)
                    : null;
            })
            ->addColumn('enrolled_at_formatted', function ($row) {
                return $row->enrolled_at ? $row->enrolled_at->format('d/m/Y H:i') : '-';
            })
            ->rawColumns(['status_badge'])
            ->toJson();
    }

    /**
     * Unduh Rekapitulasi Berkas Pendaftaran Peserta ASN (Format CSV / Spreadsheet).
     */
    public function exportRekap(Request $request): StreamedResponse
    {
        $courseId = $request->filled('course_id') ? (int) $request->get('course_id') : null;
        $status = $request->filled('status') ? $request->get('status') : null;

        $query = PendaftaranRepo::getDt($courseId);
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $filename = 'rekap-pendaftaran-diklat-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                'No',
                'No. Registrasi',
                'Nama Lengkap ASN',
                'NIP',
                'Instansi / OPD',
                'Nama Pelatihan',
                'Kode Pelatihan',
                'Tanggal Daftar',
                'Status Pendaftaran',
                'Verifikator',
                'Tanggal Verifikasi',
                'Catatan Verifikasi',
                'File Berkas Surat Tugas',
            ]);

            $no = 1;
            // Process in chunks of 200
            $query->chunk(200, function ($registrations) use ($handle, &$no) {
                foreach ($registrations as $reg) {
                    $statusLabel = $reg->status instanceof RegistrationStatus
                        ? $reg->status->label()
                        : ucfirst((string) $reg->status);

                    $letterUrl = $reg->recommendation_letter_path
                        ? asset('storage/'.$reg->recommendation_letter_path)
                        : '-';

                    fputcsv($handle, [
                        $no++,
                        $reg->registration_number ?? '-',
                        $reg->user?->name ?? '-',
                        "'".($reg->user?->nip ?? '-'),
                        $reg->user?->opd_agency ?? '-',
                        $reg->course?->title ?? '-',
                        $reg->course?->code ?? '-',
                        $reg->enrolled_at ? $reg->enrolled_at->format('d/m/Y H:i') : '-',
                        $statusLabel,
                        $reg->verifier?->name ?? '-',
                        $reg->verified_at ? $reg->verified_at->format('d/m/Y H:i') : '-',
                        $reg->verification_notes ?? '-',
                        $letterUrl,
                    ]);
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
