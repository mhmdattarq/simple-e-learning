<?php

namespace App\Http\Controllers;

use App\Repositories\KelasRepo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern untuk penyediaan data Yajra DataTables kelas.
     */
    public function dataDt(Request $request)
    {
        $type = $request->query('type', 'all');
        $data = KelasRepo::getDt($type);

        return DataTables::of($data)
            ->toJson();
    }
}
