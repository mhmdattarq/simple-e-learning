<?php

namespace App\Http\Controllers;

use App\Repositories\PimpinanRepo;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class PimpinanController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side in Pimpinan Persetujuan Rencana.
     */
    public function persetujuanDt(): JsonResponse
    {
        $data = PimpinanRepo::getPersetujuanDt();

        return DataTables::of($data)
            ->filterColumn('title', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('courses.title', 'like', "%{$keyword}%")
                        ->orWhereHas('category', function ($sub) use ($keyword) {
                            $sub->where('name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->filterColumn('courses.title', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('courses.title', 'like', "%{$keyword}%")
                        ->orWhereHas('category', function ($sub) use ($keyword) {
                            $sub->where('name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->filterColumn('code', function ($query, $keyword) {
                $query->where('courses.code', 'like', "%{$keyword}%");
            })
            ->filterColumn('courses.code', function ($query, $keyword) {
                $query->where('courses.code', 'like', "%{$keyword}%");
            })
            ->addColumn('category_name', fn ($row) => $row->category?->name ?? '-')
            ->addColumn('creator_name', fn ($row) => $row->creator?->name ?? 'Admin Diklat')
            ->addColumn('schedule_formatted', function ($row) {
                if ($row->isPermanent()) {
                    return '<span class="badge bg-light text-navy border border-simpel">Mandiri (Fleksibel)</span>';
                }
                $start = $row->start_date ? $row->start_date->format('d M Y') : '-';
                $end = $row->end_date ? $row->end_date->format('d M Y') : '-';

                return '<div class="text-nowrap fs-8"><span class="fw-semibold text-dark">'.$start.'</span><span class="text-muted mx-1">s/d</span><span class="fw-semibold text-dark">'.$end.'</span></div>';
            })
            ->addColumn('method_badge', function ($row) {
                $method = ucfirst((string) $row->method);

                return '<span class="badge bg-secondary-subtle text-secondary px-2 py-1 border border-secondary-subtle">'.$method.'</span>';
            })
            ->addColumn('type_badge', function ($row) {
                $typeLabel = $row->isBatch() ? 'Batch' : 'Mandiri';
                $class = $row->isBatch() ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-info-subtle text-info border border-info-subtle';

                return '<span class="badge '.$class.' px-2 py-0_5 fs-8">'.$typeLabel.'</span>';
            })
            ->addColumn('status_badge', function ($row) {
                $status = $row->status;

                return '<span class="badge '.$status->badgeClass().' px-2_5 py-1">'.$status->label().'</span>';
            })
            ->addColumn('submitted_at_formatted', function ($row) {
                return $row->updated_at ? $row->updated_at->format('d M Y, H:i') : '-';
            })
            ->rawColumns(['schedule_formatted', 'method_badge', 'type_badge', 'status_badge'])
            ->toJson();
    }
}
