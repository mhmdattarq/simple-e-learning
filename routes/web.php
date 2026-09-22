<?php

use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\PerencanaanController;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Pendaftaran\PendaftaranData;
use App\Livewire\Admin\Perencanaan\PerencanaanCreate;
use App\Livewire\Admin\Perencanaan\PerencanaanData;
use App\Livewire\Admin\Perencanaan\PerencanaanEdit;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Landing\LandingIndex;
use App\Livewire\Peserta\Pendaftaran\PendaftaranCreate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');

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
            Route::livewire('/data', PendaftaranData::class)->name('data');
        });
    });
});
