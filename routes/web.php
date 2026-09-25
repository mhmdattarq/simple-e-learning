<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PenjadwalanController;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\VerifikasiController;
use App\Livewire\Admin\Absensi\AbsensiData;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Materi\MateriData;
use App\Livewire\Admin\Materi\MateriDetail;
use App\Livewire\Admin\Pendaftaran\PendaftaranData;
use App\Livewire\Admin\Penjadwalan\PenjadwalanCreate;
use App\Livewire\Admin\Penjadwalan\PenjadwalanData;
use App\Livewire\Admin\Penjadwalan\PenjadwalanEdit;
use App\Livewire\Admin\Perencanaan\PerencanaanCreate;
use App\Livewire\Admin\Perencanaan\PerencanaanData;
use App\Livewire\Admin\Perencanaan\PerencanaanEdit;
use App\Livewire\Admin\Verifikasi\VerifikasiData;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Landing\JadwalIndex;
use App\Livewire\Landing\LandingIndex;
use App\Livewire\Landing\PelatihanIndex;
use App\Livewire\Peserta\Pendaftaran\PendaftaranCreate;
use App\Livewire\Pimpinan\Dashboard\DashboardIndex as PimpinanDashboard;
use App\Livewire\Pimpinan\Laporan\LaporanIndex as PimpinanLaporan;
use App\Livewire\Pimpinan\Persetujuan\PersetujuanData;
use App\Livewire\Pimpinan\Persetujuan\PersetujuanReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');
Route::livewire('/jadwal', JadwalIndex::class)->name('jadwal');
Route::livewire('/pelatihan', PelatihanIndex::class)->name('pelatihan.index');

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

// 3. Peserta / Siswa Registration to Course (Authenticated)
Route::middleware('auth')->group(function () {
    Route::livewire('/pelatihan/{id}/daftar', PendaftaranCreate::class)->name('pelatihan.daftar');
});

// 4. Logout (Authenticated)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->to(request('redirect', route('login')));
})->name('logout');

// 5. Admin & Internal Management Panel (Protected by Role)
Route::middleware(['auth', 'role:admin,mentor,verifikator,pimpinan'])->group(function () {
    Route::livewire('/admin/dashboard', DashboardIndex::class)->name('admin.dashboard');

    // Modul 1: Tahap Perencanaan
    Route::prefix('perencanaan')->group(function () {
        Route::name('perencanaan.')->group(function () {
            Route::get('/datatable', [PerencanaanController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', PerencanaanData::class)->name('data');
            Route::livewire('/create', PerencanaanCreate::class)->name('create');
            Route::livewire('/edit/{id}', PerencanaanEdit::class)->name('edit');
        });
    });

    // Modul 2: Tahap Pendaftaran
    Route::prefix('pendaftaran')->group(function () {
        Route::name('pendaftaran.')->group(function () {
            Route::get('/datatable', [PendaftaranController::class, 'dataDt'])->name('dt');
            Route::get('/export-rekap', [PendaftaranController::class, 'exportRekap'])->name('export');
            Route::livewire('/data', PendaftaranData::class)->name('data');
        });
    });

    // Modul 3: Tahap Verifikasi
    Route::prefix('verifikasi')->group(function () {
        Route::name('verifikasi.')->group(function () {
            Route::get('/datatable', [VerifikasiController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', VerifikasiData::class)->name('data');
        });
    });

    // Modul 4: Tahap Penjadwalan
    Route::prefix('penjadwalan')->group(function () {
        Route::name('penjadwalan.')->group(function () {
            Route::get('/datatable', [PenjadwalanController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', PenjadwalanData::class)->name('data');
            Route::livewire('/create', PenjadwalanCreate::class)->name('create');
            Route::livewire('/edit/{id}', PenjadwalanEdit::class)->name('edit');
        });
    });

    // Modul 5: Tahap Absensi Elektronik
    Route::prefix('absensi')->group(function () {
        Route::name('absensi.')->group(function () {
            Route::get('/datatable', [AbsensiController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', AbsensiData::class)->name('data');
        });
    });

    // Modul 6: Tahap Ruang Materi (Kurikulum & Silabus)
    Route::prefix('materi')->group(function () {
        Route::name('materi.')->group(function () {
            Route::post('/upload-media', [MateriController::class, 'uploadMedia'])->name('upload-media');
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

    // Modul Eksekutif: Pimpinan
    Route::prefix('pimpinan')->group(function () {
        Route::name('pimpinan.')->group(function () {
            Route::livewire('/dashboard', PimpinanDashboard::class)->name('dashboard');
            Route::prefix('persetujuan')->group(function () {
                Route::name('persetujuan.')->group(function () {
                    Route::get('/datatable', [PimpinanController::class, 'persetujuanDt'])->name('dt');
                    Route::livewire('/data', PersetujuanData::class)->name('data');
                    Route::livewire('/review/{id}', PersetujuanReview::class)->name('review');
                });
            });
            Route::livewire('/laporan', PimpinanLaporan::class)->name('laporan.data');
        });
    });
});
