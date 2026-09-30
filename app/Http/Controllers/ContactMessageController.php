<?php

namespace App\Http\Controllers;

use App\Repositories\ContactMessageRepo;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    /**
     * Serve JSON for Yajra DataTables server-side.
     */
    public function dataDt()
    {
        $status = request('status') ?: request('columns.5.search.value');
        $data = ContactMessageRepo::getDt($status);

        return DataTables::of($data)
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('phone', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('subject', function ($query, $keyword) {
                $query->where('subject', 'like', "%{$keyword}%");
            })
            ->addColumn('clean_phone', function ($row) {
                return $row->clean_phone;
            })
            ->addColumn('whatsapp_reply_url', function ($row) {
                return $row->whatsapp_reply_url;
            })
            ->addColumn('email_reply_url', function ($row) {
                return $row->email_reply_url;
            })
            ->addColumn('formatted_date', function ($row) {
                return $row->created_at ? $row->created_at->translatedFormat('d M Y, H:i') : '-';
            })
            ->toJson();
    }
}
