<?php

use App\Livewire\Admin\Dashboard\DashboardIndex;
use App\Livewire\Auth\Login;
use App\Livewire\Landing\LandingIndex;
use Illuminate\Support\Facades\Route;

// Landing Page (Public)
Route::livewire('/', LandingIndex::class)->name('landing');

// Authentication
Route::livewire('/login', Login::class)->name('login');

// Admin Panel (Protected / Management)
Route::livewire('/admin', DashboardIndex::class)->name('admin.dashboard');
Route::livewire('/dashboard', DashboardIndex::class)->name('dashboard');
