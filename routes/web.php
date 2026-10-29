<?php

use App\Livewire\Auth\Login;
use App\Livewire\SuperAdmin\PlatformDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', Login::class)->name('login');
Route::get('/admin/login', Login::class);

Route::match(['get', 'post'], '/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Super Admin Platform Dashboard (Modul 01)
Route::middleware(['web'])->group(function () {
    Route::get('/', PlatformDashboard::class)->name('superadmin.dashboard');
    Route::get('/admin', PlatformDashboard::class)->name('superadmin.admin');
    Route::get('/platform', PlatformDashboard::class)->name('superadmin.platform');
});
