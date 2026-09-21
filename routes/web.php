<?php

use App\Http\Controllers\PerencanaanController;
use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Perencanaan\PerencanaanCreate;
use App\Livewire\Admin\Perencanaan\PerencanaanData;
use App\Livewire\Admin\Perencanaan\PerencanaanEdit;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Landing\LandingIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
    Route::livewire('/register', Register::class)->name('register');
});

// 3. Logout (Authenticated - POST Aman per PRD-LW.md)
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->to(request('redirect', route('login')));
})->name('logout');

// 4. Admin & Internal Management Panel (Protected by Role)
Route::middleware(['auth', 'role:admin,mentor,verifikator,pimpinan'])->group(function () {
    Route::livewire('/admin', DashboardIndex::class)->name('admin.dashboard');
    Route::livewire('/dashboard', DashboardIndex::class)->name('dashboard');

    // Modul 1: Tahap Perencanaan
    Route::prefix('perencanaan')->group(function () {
        Route::name('perencanaan.')->group(function () {
            Route::get('/datatable', [PerencanaanController::class, 'dataDt'])->name('dt');
            Route::livewire('/data', PerencanaanData::class)->name('data');
            Route::livewire('/create', PerencanaanCreate::class)->name('create');
            Route::livewire('/edit/{id}', PerencanaanEdit::class)->name('edit');
        });
    });
});
