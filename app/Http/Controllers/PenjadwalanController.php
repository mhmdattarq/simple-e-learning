<?php

namespace App\Http\Controllers;

use App\Repositories\PenjadwalanRepo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PenjadwalanController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern per PRD-LW.
     */
    public function dataDt(Request $request)
    {
        $courseId = $request->filled('course_id') ? (int) $request->get('course_id') : null;
        $mentorId = $request->filled('mentor_id') ? (int) $request->get('mentor_id') : null;
        $date = $request->filled('date') ? $request->get('date') : null;

        $data = PenjadwalanRepo::getDt($courseId, $mentorId, $date);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('course_title', fn ($row) => $row->course?->title ?? '-')
            ->addColumn('course_code', fn ($row) => $row->course?->code ?? '-')
            ->addColumn('mentor_name', fn ($row) => $row->mentor?->name ?? '-')
            ->addColumn('mentor_nip', fn ($row) => $row->mentor?->nip ?? '-')
            ->addColumn('session_date_formatted', function ($row) {
                return $row->session_date ? $row->session_date->format('d/m/Y') : '-';
            })
            ->addColumn('time_range', function ($row) {
                $start = substr($row->start_time, 0, 5);
                $end = substr($row->end_time, 0, 5);

                return "{$start} - {$end} WIB";
            })
            ->addColumn('location_badge', function ($row) {
                if ($row->isOnline()) {
                    $link = htmlspecialchars($row->room_or_link, ENT_QUOTES, 'UTF-8');

                    return '<a href="'.$link.'" target="_blank" class="badge bg-primary-subtle text-primary text-decoration-none d-inline-flex align-items-center gap-1"><i class="ri-video-chat-line"></i> Link Daring</a>';
                }

                return '<span class="text-dark"><i class="ri-building-line text-muted me-1"></i>'.htmlspecialchars($row->room_or_link, ENT_QUOTES, 'UTF-8').'</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;
                if ($status === 'ongoing') {
                    return '<span class="badge bg-warning text-dark px-2 py-1"><i class="ri-loader-4-line me-1"></i>Berlangsung</span>';
                } elseif ($status === 'completed') {
                    return '<span class="badge bg-success text-white px-2 py-1"><i class="ri-checkbox-circle-line me-1"></i>Selesai</span>';
                } elseif ($status === 'cancelled') {
                    return '<span class="badge bg-danger text-white px-2 py-1"><i class="ri-close-circle-line me-1"></i>Dibatalkan</span>';
                }

                return '<span class="badge bg-secondary text-white px-2 py-1"><i class="ri-calendar-check-line me-1"></i>Terjadwal</span>';
            })
            ->rawColumns(['location_badge', 'status_badge'])
            ->toJson();
    }
}
