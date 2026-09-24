<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MateriController extends Controller
{
    /**
     * Upload media atau dokumen lampiran dari Rich Form Editor (Quill).
     */
    public function uploadMedia(Request $request): JsonResponse
    {
        $file = $request->file('file');
        $isImage = $file && str_starts_with($file->getMimeType() ?? '', 'image/');
        $maxKb = $isImage ? 2048 : 10240; // Gambar: 2MB, Dokumen: 10MB
        $maxLabel = $isImage ? '2 MB' : '10 MB';

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:'.$maxKb,
        ], [
            'file.required' => 'Berkas lampiran materi wajib dipilih.',
            'file.file' => 'Berkas yang diunggah tidak valid.',
            'file.max' => 'Ukuran '.($isImage ? 'gambar' : 'dokumen').' melebihi batas maksimal (Maksimal '.$maxLabel.').',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('file'),
            ], 422);
        }

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
