<?php

use App\Livewire\Auth\Login;
use App\Livewire\Pos\Terminal;
use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Livewire\Tenant\BackofficeDashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root Route: Always redirect guests to /login; authenticated users to their portal
Route::get('/', function () {
    if (! Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();
    if ($user && in_array($user->role, ['owner', 'manager'])) {
        return redirect()->route('tenant.dashboard');
    }

    if ($user && in_array($user->role, ['cashier', 'staff'])) {
        return redirect()->route('pos.terminal');
    }

    return redirect()->route('superadmin.dashboard');
});

// Authentication Routes
Route::get('/login', Login::class)->name('login');
Route::get('/admin/login', Login::class);

Route::match(['get', 'post'], '/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Progressive Web App (PWA) Manifest & Service Worker
Route::get('/manifest.json', function () {
    return response()->file(public_path('manifest.json'), [
        'Content-Type' => 'application/manifest+json; charset=utf-8',
    ]);
});

Route::get('/sw.js', function () {
    return response()->file(public_path('sw.js'), [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Service-Worker-Allowed' => '/',
    ]);
});

// Protected routes (Require Authentication)
Route::middleware(['web', 'auth'])->group(function () {
    // Super Admin Platform Dashboard (Modul 01)
    Route::get('/admin', PlatformDashboard::class)->name('superadmin.dashboard');
    Route::get('/platform', PlatformDashboard::class)->name('superadmin.platform');

    // Tenant Admin / Back Office (Modul 02, 03, 04, 05, 06, 08, 09)
    Route::get('/app', BackofficeDashboard::class)->name('tenant.dashboard');
    Route::get('/tenant', BackofficeDashboard::class);
    Route::get('/backoffice', BackofficeDashboard::class);

    // POS Terminal Kasir (Modul 07)
    Route::get('/pos', Terminal::class)->name('pos.terminal');
    Route::get('/app/pos', Terminal::class);
});
