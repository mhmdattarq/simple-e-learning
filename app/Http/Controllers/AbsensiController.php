<?php

namespace App\Http\Controllers;

use App\Repositories\AbsensiRepo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    /**
     * Endpoint JSON Yajra DataTables Server-Side untuk Modul Absensi.
     */
    public function dataDt(Request $request): JsonResponse
    {
        $courseId = $request->filled('course_id') ? (int) $request->get('course_id') : null;
        $date = $request->filled('date') ? $request->get('date') : null;
        $tokenStatus = $request->filled('token_status') ? $request->get('token_status') : null;

        $user = $request->user();
        $mentorId = $user && $user->isMentor() ? $user->id : null;

        $data = AbsensiRepo::getDt($courseId, $date, $tokenStatus, $mentorId);

        return DataTables::of($data)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                $search = $request->input('search.value');
                if (! empty($search)) {
                    $keyword = trim($search);
                    $query->where(function ($q) use ($keyword) {
                        $q->where('session_title', 'like', "%{$keyword}%")
                            ->orWhere('attendance_token', 'like', "%{$keyword}%")
                            ->orWhere('room_or_link', 'like', "%{$keyword}%")
                            ->orWhereHas('course', function ($cq) use ($keyword) {
                                $cq->where('title', 'like', "%{$keyword}%")
                                    ->orWhere('code', 'like', "%{$keyword}%");
                            })
                            ->orWhereHas('mentor', function ($mq) use ($keyword) {
                                $mq->where('name', 'like', "%{$keyword}%")
                                    ->orWhere('nip', 'like', "%{$keyword}%");
                            });
                    });
                }
            })
            ->addColumn('is_current_mentor', fn ($row) => $user && $user->isMentor() && (int) $row->mentor_id === (int) $user->id)
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
            ->addColumn('attendance_status', function ($row) {
                return $row->getAttendanceStatus();
            })
            ->addColumn('attendance_status_badge', function ($row) {
                return $row->getAttendanceStatusBadge();
            })
            ->addColumn('attendance_ratio_badge', function ($row) {
                $enrolled = $row->course?->participants()
                    ->wherePivotIn('status', ['verified', 'active', 'completed'])
                    ->count() ?? 0;
                $present = $row->attendances->whereIn('status', ['hadir', 'terlambat'])->count();

                $percent = $enrolled > 0 ? round(($present / $enrolled) * 100) : 0;
                $color = $percent >= 80 ? 'success' : ($percent >= 50 ? 'warning' : 'secondary');

                return '<div class="d-inline-flex flex-column align-items-center gap-1">
                    <span class="badge bg-light text-dark border px-2 py-1 fs-7">
                        <i class="ri-user-follow-line text-primary me-1"></i><strong>'.$present.'</strong> / '.$enrolled.' Peserta
                    </span>
                    <span class="badge bg-'.$color.'-subtle text-'.$color.' border border-'.$color.'-subtle px-2 py-0" style="font-size: 11px;">
                        '.$percent.'% Hadir
                    </span>
                </div>';
            })
            ->addColumn('action', function ($row) {
                $url = route('absensi.kelola', $row->id);

                return '<a href="'.$url.'" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1 px-3 py-1 radius-6 fw-medium" wire:navigate>
                    <i class="ri-settings-3-line"></i> Kelola Absensi
                </a>';
            })
            ->addColumn('token_badge', function ($row) {
                if ($row->isAttendanceActive()) {
                    $expiresTime = $row->token_expires_at->format('H:i');

                    return '<div class="d-inline-flex align-items-center gap-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold fs-7" style="letter-spacing: 1px;">
                            <i class="ri-key-2-line me-1"></i>'.$row->attendance_token.'
                        </span>
                        <small class="text-muted" style="font-size: 11px;">s.d '.$expiresTime.'</small>
                    </div>';
                }

                if ($row->attendance_token && $row->token_expires_at && $row->token_expires_at->isPast()) {
                    return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1"><i class="ri-time-line me-1"></i>Kedaluwarsa</span>';
                }

                return '<span class="badge bg-light text-muted px-2 py-1 border"><i class="ri-lock-line me-1"></i>Belum Dibuka</span>';
            })
            ->addColumn('attendance_summary_badge', function ($row) {
                $enrolled = $row->course?->participants()
                    ->wherePivotIn('status', ['verified', 'active', 'completed'])
                    ->count() ?? 0;
                $present = $row->attendances->whereIn('status', ['hadir', 'terlambat'])->count();

                $percent = $enrolled > 0 ? round(($present / $enrolled) * 100) : 0;
                $color = $percent >= 80 ? 'success' : ($percent >= 50 ? 'warning' : 'secondary');
                $url = route('absensi.kelola', $row->id);

                return '<a href="'.$url.'" class="btn btn-sm btn-light border py-1 px-2 d-inline-flex align-items-center gap-2 text-start" wire:navigate
                    title="Klik untuk melihat rekapitulasi kehadiran peserta">
                    <span class="badge bg-'.$color.' text-white px-2 py-1 fw-semibold">'.$percent.'%</span>
                    <span class="text-muted fs-8">('.$present.'/'.$enrolled.' hadir)</span>
                    <i class="ri-eye-line text-primary ms-1"></i>
                </a>';
            })
            ->rawColumns(['token_badge', 'attendance_summary_badge', 'attendance_status_badge', 'attendance_ratio_badge', 'action'])
            ->toJson();
    }
}
