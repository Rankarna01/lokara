<?php

use App\Livewire\Auth\Login;
use App\Livewire\Pos\Terminal;
use App\Livewire\SuperAdmin\PlatformDashboard;
use App\Livewire\Tenant\BackofficeDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        $user = Auth::user();
        if ($user && in_array($user->role, ['owner', 'manager'])) {
            return redirect()->route('tenant.dashboard');
        } elseif ($user && in_array($user->role, ['cashier', 'staff'])) {
            return redirect()->route('pos.terminal');
        }

        return redirect()->route('superadmin.dashboard');
    }

    return back()->withErrors(['email' => 'Email atau kata sandi tidak sesuai dengan data kami.'])->withInput();
});

Route::match(['get', 'post'], '/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

// Direct bulletproof routes for Livewire assets (guarantees 200 OK regardless of hosting setup)
$serveLivewireAsset = function (string $filename) {
    $distPath = base_path('vendor/livewire/livewire/dist/'.$filename);
    $publicPath = public_path('js/livewire/'.$filename);
    $path = file_exists($distPath) ? $distPath : $publicPath;

    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/javascript; charset=utf-8',
        'Cache-Control' => 'public, max-age=31536000',
    ]);
};

Route::get('/livewire/livewire.min.js', fn () => $serveLivewireAsset('livewire.min.js'));
Route::get('/livewire/livewire.js', fn () => $serveLivewireAsset('livewire.js'));
Route::get('/js/livewire/livewire.min.js', fn () => $serveLivewireAsset('livewire.min.js'));
Route::get('/js/livewire/livewire.js', fn () => $serveLivewireAsset('livewire.js'));
Route::get('/vendor/livewire/livewire.min.js', fn () => $serveLivewireAsset('livewire.min.js'));
Route::get('/vendor/livewire/livewire.js', fn () => $serveLivewireAsset('livewire.js'));

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
    Route::get('/app/transactions/pdf/{key}', function (string $key) {
        $payload = Cache::pull('tenant-transactions-pdf:'.$key);
        abort_unless($payload && $payload['user_id'] === Auth::id(), 404);

        return response($payload['html']);
    })->name('tenant.transactions.pdf');

    // POS Terminal Kasir (Modul 07)
    Route::get('/pos', Terminal::class)->name('pos.terminal');
    Route::get('/app/pos', Terminal::class);
});
