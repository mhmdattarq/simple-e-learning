<?php

use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Admin\Perencanaan\PerencanaanData;
use App\Livewire\Auth\Login;
use App\Livewire\Landing\LandingIndex;
use Illuminate\Support\Facades\Route;

// 1. Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');

// 2. Authentication (Guest)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', Login::class)->name('login');
});

// 3. Logout (Authenticated)
Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->to(request('redirect', route('login')));
})->name('logout');

// 4. Admin & Internal Management Panel (Protected by Role)
Route::middleware(['auth', 'role:admin,mentor,verifikator,pimpinan'])->group(function () {
    Route::livewire('/admin', DashboardIndex::class)->name('admin.dashboard');
    Route::livewire('/dashboard', DashboardIndex::class)->name('dashboard');
    Route::livewire('/perencanaan', PerencanaanData::class)->name('perencanaan');
});
