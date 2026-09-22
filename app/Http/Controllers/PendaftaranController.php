<?php

namespace App\Http\Controllers;

use App\Repositories\PendaftaranRepo;
use Yajra\DataTables\Facades\DataTables;

class PendaftaranController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern per PRD-LW.
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
}
