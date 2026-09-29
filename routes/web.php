<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Kategori\KategoriCreate;
use App\Livewire\Admin\Kategori\KategoriData;
use App\Livewire\Admin\Kategori\KategoriEdit;
use App\Livewire\Admin\Kelas\KelasCreate;
use App\Livewire\Admin\Kelas\KelasData;
use App\Livewire\Admin\Kelas\KelasEdit;
use App\Livewire\Admin\Materi\MateriData;
use App\Livewire\Admin\Materi\MateriDetail;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Landing\JadwalIndex;
use App\Livewire\Landing\LandingIndex;
use App\Livewire\Landing\PelatihanIndex;
use App\Livewire\Peserta\Materi\MateriBelajar;
use App\Livewire\Peserta\Profile\ProfileIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');
Route::livewire('/jadwal', JadwalIndex::class)->name('jadwal');
Route::livewire('/pelatihan', PelatihanIndex::class)->name('pelatihan.index');

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->middleware('throttle:login')->name('login');
    Route::livewire('/register', Register::class)->name('register');
    Route::livewire('/email/verify/{token}', VerifyEmail::class)->name('verification.verify');
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');
});

// 3. Peserta / Siswa (Authenticated)
Route::middleware('auth')->group(function () {
    Route::livewire('/profil', ProfileIndex::class)->name('peserta.profil');
    Route::livewire('/pelatihan/{id}/materi', MateriBelajar::class)->name('peserta.materi');
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

    // Modul: Ruang Materi
    Route::prefix('materi')->name('materi.')->group(function () {
        Route::post('/upload-media', [MateriController::class, 'uploadMedia'])->middleware('throttle:uploads')->name('upload-media');
        Route::livewire('/data', MateriData::class)->name('data');
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
});
