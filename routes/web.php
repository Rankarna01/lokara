<?php

use App\Livewire\Auth\Login;
use App\Livewire\Pos\Terminal;
use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Livewire\Tenant\BackofficeDashboard;
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

// POS Terminal Kasir (Modul 07)
Route::get('/pos', Terminal::class)->name('pos.terminal');
Route::get('/app/pos', Terminal::class);

// Tenant Admin / Back Office (Modul 02, 03, 04, 05, 06, 08, 09)
Route::get('/app', BackofficeDashboard::class)->name('tenant.dashboard');
Route::get('/tenant', BackofficeDashboard::class);
Route::get('/backoffice', BackofficeDashboard::class);

// Super Admin Platform Dashboard (Modul 01)
Route::middleware(['web'])->group(function () {
    Route::get('/', PlatformDashboard::class)->name('superadmin.dashboard');
    Route::get('/admin', PlatformDashboard::class)->name('superadmin.admin');
    Route::get('/platform', PlatformDashboard::class)->name('superadmin.platform');
});
