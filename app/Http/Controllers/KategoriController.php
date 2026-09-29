<?php

namespace App\Http\Controllers;

use App\Repositories\KategoriRepo;
use Yajra\DataTables\Facades\DataTables;

class KategoriController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     */
    public function dataDt()
    {
        $data = KategoriRepo::getDt();

        return DataTables::of($data)
            ->toJson();
    }
}
