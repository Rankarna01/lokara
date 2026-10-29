<?php

use App\Livewire\SuperAdmin\PlatformDashboard;
use Illuminate\Support\Facades\Route;

// Super Admin Platform Dashboard (Modul 01)
Route::get('/', PlatformDashboard::class)->name('superadmin.dashboard');
Route::get('/admin', PlatformDashboard::class)->name('superadmin.admin');
Route::get('/platform', PlatformDashboard::class)->name('superadmin.platform');
