<?php

namespace App\Http\Controllers;

use App\Repositories\EvaluasiRepo;
use Yajra\DataTables\Facades\DataTables;

class EvaluasiController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side master evaluasi per kelas.
     * Ultra-Thin Controller pattern untuk penyediaan data Yajra DataTables evaluasi per kelas.
     */
    public function dataDt()
    {
        $data = EvaluasiRepo::getDt();

        return DataTables::of($data)
            ->toJson();
    }

    /**
     * Serve JSON for Yajra DataTables server-side riwayat pengerjaan kuis peserta per kelas.
     */
    public function attemptsDt(int $course_id)
    {
        $data = EvaluasiRepo::getAttemptsDt($course_id);

        return DataTables::of($data)
            ->toJson();
    }
}
