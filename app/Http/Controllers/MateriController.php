<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    /**
     * Upload media atau dokumen lampiran dari Rich Form Editor (Quill).
     */
    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:25600', // Maksimal 25MB
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $sizeInBytes = $file->getSize();

            // Format ukuran file yang mudah dibaca
            if ($sizeInBytes >= 1048576) {
                $formattedSize = round($sizeInBytes / 1048576, 1).' MB';
            } else {
                $formattedSize = round($sizeInBytes / 1024, 0).' KB';
            }

            // Simpan ke storage publik
            $path = $file->store('courses/materials', 'public');
            $url = Storage::url($path);

            return response()->json([
                'success' => true,
                'url' => $url,
                'path' => $path,
                'filename' => $originalName,
                'extension' => $extension,
                'size' => $formattedSize,
            ]);
        } catch (\Exception $e) {
            Log::error('Upload media materi editor gagal', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah berkas: '.$e->getMessage(),
            ], 500);
        }
    }
}
