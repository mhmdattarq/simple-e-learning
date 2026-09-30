<?php

use App\Http\Controllers\Auth\GoogleAuthController;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');
Route::livewire('/kelas-batch', KelasIndex::class)->name('landing.kelas.batch');
Route::livewire('/kelas-permanen', KelasIndex::class)->name('landing.kelas.permanen');
Route::livewire('/kelas-berbayar', KelasIndex::class)->name('landing.kelas.berbayar');
Route::livewire('/jadwal', JadwalIndex::class)->name('jadwal');
Route::livewire('/kontak', KontakIndex::class)->middleware('throttle:contact')->name('kontak');
Route::redirect('/contact', '/kontak');
Route::livewire('/kelas', KelasIndex::class)->name('pelatihan.index');

// Detail Kelas per Jenis (Type-aware & SEO URLs)
Route::livewire('/kelas-batch/{course}', KelasDetail::class)->name('landing.kelas.detail.batch');
Route::livewire('/kelas-permanen/{course}', KelasDetail::class)->name('landing.kelas.detail.permanen');
Route::livewire('/kelas-berbayar/{course}', KelasDetail::class)->name('landing.kelas.detail.berbayar');

// Generic / Backward-compatible Detail Kelas
Route::get('/kelas/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course)))
    ->where('course', '^(?!data$|create$|edit$|datatable$)[^/]+$');
Route::redirect('/pelatihan', '/kelas');
Route::get('/pelatihan/{course}', fn (Course $course) => redirect()->to(route('landing.kelas.detail', $course)));
Route::get('/favicon.ico', fn () => response()->file(public_path('favicon.ico')));

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->middleware('throttle:login')->name('login');
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
    Route::livewire('/kelas-batch/{course}/materi', MateriBelajar::class)->name('peserta.materi.batch');
    Route::livewire('/kelas-permanen/{course}/materi', MateriBelajar::class)->name('peserta.materi.permanen');
    Route::livewire('/kelas-berbayar/{course}/materi', MateriBelajar::class)->name('peserta.materi.berbayar');

    // Evaluasi & Kuis per Jenis (Slug-based & Type-aware)
    Route::livewire('/kelas-batch/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.batch');
    Route::livewire('/kelas-permanen/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.permanen');
    Route::livewire('/kelas-berbayar/{course}/evaluasi/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.kerjakan.berbayar');

    // Generic / Backward-compatible Redirects
    Route::get('/kelas/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course)));
    Route::get('/pelatihan/{course}/materi', fn (Course $course) => redirect()->to(route('peserta.materi', $course)));
    Route::get('/kelas/{course}/evaluasi/{quiz}', fn (Course $course, $quiz) => redirect()->to(route('peserta.evaluasi.kerjakan', ['course' => $course, 'quiz' => $quiz])));
    Route::livewire('/evaluasi/kerjakan/{quiz}', QuizKerjakan::class)->name('peserta.evaluasi.show');
});

// 4. Logout (Authenticated)
Route::post('/logout', function () {
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
});
