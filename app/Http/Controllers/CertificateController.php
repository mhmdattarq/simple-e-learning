<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Repositories\CertificateRepo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    /**
     * Unduh sertifikat kelulusan kelas dalam format PDF standar A4 Landscape.
     */
    public function downloadPdf(Course $course, Request $request): Response
    {
        $user = $this->resolveTargetUser($request);

        if (! CertificateRepo::canAccess($course, $user) && ! Auth::user()->isAdmin()) {
            abort(403, 'Sertifikat belum dapat diunduh. Anda harus menyelesaikan dan dinyatakan lulus ujian akhir kelas terlebih dahulu.');
        }

        $pdf = CertificateRepo::generatePdf($course, $user);
        $filename = CertificateRepo::getFilename($course, $user, 'pdf');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'Content-Length' => strlen($pdf),
        ]);
    }

    /**
     * Unduh sertifikat kelulusan kelas dalam format Gambar PNG resolusi tinggi.
     */
    public function downloadImage(Course $course, Request $request): Response
    {
        $user = $this->resolveTargetUser($request);

        if (! CertificateRepo::canAccess($course, $user) && ! Auth::user()->isAdmin()) {
            abort(403, 'Sertifikat belum dapat diunduh. Anda harus menyelesaikan dan dinyatakan lulus ujian akhir kelas terlebih dahulu.');
        }

        $png = CertificateRepo::generatePng($course, $user);
        $filename = CertificateRepo::getFilename($course, $user, 'png');

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'Content-Length' => strlen($png),
        ]);
    }

    /**
     * Pratinjau langsung sertifikat PDF di browser (inline view).
     */
    public function preview(Course $course, Request $request): Response
    {
        $user = $this->resolveTargetUser($request);

        if (! CertificateRepo::canAccess($course, $user) && ! Auth::user()->isAdmin()) {
            abort(403, 'Sertifikat belum dapat diakses. Anda harus menyelesaikan dan dinyatakan lulus ujian akhir kelas terlebih dahulu.');
        }

        $pdf = CertificateRepo::generatePdf($course, $user);
        $filename = CertificateRepo::getFilename($course, $user, 'pdf');

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
            'Cache-Control' => 'private, no-cache, must-revalidate',
            'Content-Length' => strlen($pdf),
        ]);
    }

    /**
     * Menentukan pengguna target (peserta bersangkutan atau jika diakses admin dengan parameter user_id).
     */
    protected function resolveTargetUser(Request $request): User
    {
        $authUser = Auth::user();

        if ($authUser && $authUser->isAdmin() && $request->filled('user_id')) {
            return User::findOrFail($request->integer('user_id'));
        }

        return $authUser;
    }
}
