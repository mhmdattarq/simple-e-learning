<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Evaluasi\EvaluasiCreate;
use App\Livewire\Admin\Evaluasi\EvaluasiData;
use App\Livewire\Admin\Evaluasi\EvaluasiDetail;
use App\Livewire\Admin\Kategori\KategoriCreate;
use App\Livewire\Admin\Kategori\KategoriData;
use App\Livewire\Admin\Kategori\KategoriEdit;
use App\Livewire\Admin\Kelas\KelasCreate;
use App\Livewire\Admin\Kelas\KelasData;
use App\Livewire\Admin\Kelas\KelasEdit;
use App\Livewire\Admin\Kontak\KontakPesanData;
use App\Livewire\Admin\Kontak\KontakSettingData;
use App\Livewire\Admin\Materi\MateriDetail;
use App\Livewire\Admin\Notifikasi\NotifikasiIndex;
use App\Livewire\Admin\Profile\AdminProfileIndex;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Landing\JadwalIndex;
use App\Livewire\Landing\KelasDetail;
use App\Livewire\Landing\KelasIndex;
use App\Livewire\Landing\KontakIndex;
use App\Livewire\Landing\LandingIndex;
use App\Livewire\Peserta\Evaluasi\QuizKerjakan;
use App\Livewire\Peserta\Materi\MateriBelajar;
use App\Livewire\Peserta\Profile\ProfileIndex;
use App\Models\Course;
use App\Models\User;
use App\Repositories\EvaluasiRepo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');
Route::livewire('/bulanan', KelasIndex::class)->name('landing.kelas.bulanan');
Route::livewire('/bangkom', KelasIndex::class)->name('landing.kelas.bangkom');
Route::livewire('/bangkom-external', KelasIndex::class)->name('landing.kelas.bangkom_external');
Route::livewire('/jadwal', JadwalIndex::class)->name('jadwal');
Route::livewire('/kontak', KontakIndex::class)->middleware('throttle:contact')->name('kontak');
Route::redirect('/contact', '/kontak');
Route::livewire('/kelas', KelasIndex::class)->name('pelatihan.index');

// Backward-compatible redirect aliases for old URLs
Route::get('/kelas-batch', fn () => redirect()->to(route('landing.kelas.bulanan'), 301))->name('landing.kelas.batch');
Route::get('/kelas-permanen', fn () => redirect()->to(route('landing.kelas.bangkom'), 301))->name('landing.kelas.permanen');
Route::get('/kelas-berbayar', fn () => redirect()->to(route('landing.kelas.bangkom_external'), 301))->name('landing.kelas.berbayar');

// Detail Kelas per Jenis (Type-aware & SEO URLs)
Route::livewire('/bulanan/{course}', KelasDetail::class)->name('landing.kelas.detail.bulanan');
Route::livewire('/bangkom/{course}', KelasDetail::class)->name('landing.kelas.detail.bangkom');
Route::livewire('/bangkom-external/{course}', KelasDetail::class)->name('landing.kelas.detail.bangkom_external');

// Backward-compatible Detail Kelas Redirects
Route::get('/kelas-batch/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course), 301))->name('landing.kelas.detail.batch');
Route::get('/kelas-permanen/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course), 301))->name('landing.kelas.detail.permanen');
Route::get('/kelas-berbayar/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course), 301))->name('landing.kelas.detail.berbayar');

// Generic / Backward-compatible Detail Kelas
Route::get('/kelas/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course)))
    ->where('course', '^(?!data$|create$|edit$|datatable$)[^/]+$');
Route::redirect('/pelatihan', '/kelas');
Route::get('/pelatihan/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course)));
Route::get('/favicon.ico', fn () => response()->file(public_path('favicon.ico')));

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->middleware('throttle:register')->name('register');
    Route::livewire('/forgot-password', ForgotPassword::class)->middleware('throttle:forgot-password')->name('password.request');
    Route::livewire('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    Route::livewire('/email/verify/{token}', VerifyEmail::class)->middleware('throttle:verification')->name('verification.verify');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// 3. Peserta / Siswa (Authenticated)
Route::middleware('auth')->group(function () {
    Route::livewire('/profil', ProfileIndex::class)->name('peserta.profil');

    // Ruang Belajar & Materi per Jenis
    Route::livewire('/bulanan/{course}/materi', MateriBelajar::class)->name('peserta.materi.bulanan');
    Route::livewire('/bangkom/{course}/materi', MateriBelajar::class)->name('peserta.materi.bangkom');
    Route::livewire('/bangkom-external/{course}/materi', MateriBelajar::class)->name('peserta.materi.bangkom_external');

    // Backward-compatible redirects for old materi URLs
    Route::get('/kelas-batch/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course), 301))->name('peserta.materi.batch');
    Route::get('/kelas-permanen/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course), 301))->name('peserta.materi.permanen');
    Route::get('/kelas-berbayar/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course), 301))->name('peserta.materi.berbayar');

    // Evaluasi & Kuis per Jenis (Slug-based & Type-aware)
    Route::livewire('/bulanan/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.bulanan');
    Route::livewire('/bangkom/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.bangkom');
    Route::livewire('/bangkom-external/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.bangkom_external');

    // Backward-compatible redirects for old evaluasi URLs
    Route::get('/kelas-batch/{course}/evaluasi/{quiz}', fn (Course $course, $quiz) => redirect()->to(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $quiz]), 301))->name('peserta.evaluasi.kerjakan.batch');
    Route::get('/kelas-permanen/{course}/evaluasi/{quiz}', fn (Course $course, $quiz) => redirect()->to(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $quiz]), 301))->name('peserta.evaluasi.kerjakan.permanen');
    Route::get('/kelas-berbayar/{course}/evaluasi/{quiz}', fn (Course $course, $quiz) => redirect()->to(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $quiz]), 301))->name('peserta.evaluasi.kerjakan.berbayar');

    // Generic / Backward-compatible Redirects
    Route::get('/kelas/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course)));
    Route::get('/pelatihan/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course)));
    Route::get('/kelas/{course}/evaluasi/{quiz}', fn (Course $course, $quiz) => redirect()->to(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $quiz])));
    Route::livewire('/evaluasi/kerjakan/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.show');

    // Unduh & Pratinjau Sertifikat Pelatihan (PDF & Gambar)
    Route::get('/kursus/{course}/sertifikat/unduh-pdf', [CertificateController::class, 'downloadPdf'])->name('peserta.sertifikat.unduh-pdf');
    Route::get('/kursus/{course}/sertifikat/unduh-gambar', [CertificateController::class, 'downloadImage'])->name('peserta.sertifikat.unduh-gambar');
    Route::get('/kursus/{course}/sertifikat/preview', [CertificateController::class, 'preview'])->name('peserta.sertifikat.preview');

    // Route alias / generic sertifikat
    Route::get('/sertifikat/{course}/unduh/pdf', [CertificateController::class, 'downloadPdf'])->name('peserta.sertifikat.pdf');
    Route::get('/sertifikat/{course}/unduh/gambar', [CertificateController::class, 'downloadImage'])->name('peserta.sertifikat.gambar');
});

// 4. Logout (Authenticated)
Route::post('/logout', function () {
    $user = Auth::user();
    if ($user instanceof User) {
        EvaluasiRepo::finalizeActiveSessionsForUser($user);
    }

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->to(request('redirect', route('login')));
})->name('logout');

// 5. Admin Panel (Super Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::livewire('/admin/dashboard', DashboardIndex::class)->name('admin.dashboard');
    Route::livewire('/admin/notifikasi', NotifikasiIndex::class)->name('admin.notifikasi');
    Route::livewire('/admin/profil', AdminProfileIndex::class)->name('admin.profil');

    // Modul: Kategori Kelas (Tahap 2)
    Route::prefix('kategori')->name('kategori.')->group(function () {
        Route::get('/datatable', [KategoriController::class, 'dataDt'])->name('dt');
        Route::livewire('/data', KategoriData::class)->name('data');
        Route::livewire('/create', KategoriCreate::class)->name('create');
        Route::livewire('/edit/{id}', KategoriEdit::class)->name('edit');
    });

    // Modul: Kelola Kelas (Tahap 3)
    Route::prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/datatable', [KelasController::class, 'dataDt'])->name('dt');
        Route::livewire('/data', KelasData::class)->name('data');
        Route::livewire('/create', KelasCreate::class)->name('create');
        Route::livewire('/edit/{id}', KelasEdit::class)->name('edit');
    });

    // Modul: Materi / Kurikulum Kelas
    Route::prefix('materi')->name('materi.')->group(function () {
        Route::post('/upload-media', [MateriController::class, 'uploadMedia'])->middleware('throttle:uploads')->name('upload-media');
        Route::redirect('/data', '/kelas/data');
        Route::livewire('/detail/{id}', MateriDetail::class)->name('detail');
        Route::get('/detail/{course_id}/editor/{lesson_id?}', function ($course_id, $lesson_id = null) {
            $params = ['id' => $course_id, 'view' => 'editor'];
            if ($lesson_id) {
                $params['lesson_id'] = $lesson_id;
            }
            if (request()->query('chapter_id')) {
                $params['chapter_id'] = request()->query('chapter_id');
            }

            return redirect()->route('materi.detail', $params);
        })->name('editor');
    });

    // Modul: Evaluasi & Kuis (Tahap Evaluasi)
    Route::prefix('evaluasi')->name('evaluasi.')->group(function () {
        Route::get('/datatable', [EvaluasiController::class, 'dataDt'])->name('dt');
        Route::get('/detail/{course_id}/datatable', [EvaluasiController::class, 'attemptsDt'])->name('detail.dt');
        Route::livewire('/data', EvaluasiData::class)->name('data');
        Route::livewire('/create', EvaluasiCreate::class)->name('create');
        Route::livewire('/detail/{course_id}', EvaluasiDetail::class)->name('detail');
    });

    // Modul: Layanan Kontak (Tahap 2 & 3)
    Route::prefix('kontak-admin')->name('kontak.')->group(function () {
        Route::prefix('pesan')->name('pesan.')->group(function () {
            Route::get('/datatable', [ContactMessageController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', KontakPesanData::class)->name('data');
        });
        Route::livewire('/pengaturan', KontakSettingData::class)->name('setting.data');
    });
});
