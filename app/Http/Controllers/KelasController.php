<?php

namespace App\Http\Controllers;

use App\Repositories\KelasRepo;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern untuk penyediaan data Yajra DataTables kelas pelatihan.
     */
    public function dataDt()
    {
        $data = KelasRepo::getDt();

        return DataTables::of($data)
            ->toJson();
    }
}
