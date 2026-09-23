<?php

namespace App\Http\Controllers;

use App\Repositories\PerencanaanRepo;
use Yajra\DataTables\Facades\DataTables;

class PerencanaanController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     * Ultra-Thin Controller pattern untuk penyediaan data Yajra DataTables perencanaan pelatihan.
     */
    public function dataDt()
    {
        $data = PerencanaanRepo::getDt();

        return DataTables::of($data)
            ->toJson();
    }
}
