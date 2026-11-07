<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\PlatformActivityLog;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

trait HasTransactionHistoryCleanup
{
    public string $cleanupLoginUsername = '';

    public string $cleanupLoginPassword = '';

    public ?int $cleanupTenantId = null;

    public bool $cleanupConfirmed = false;

    public function isCleanupUnlocked(): bool
    {
        return session('superadmin_cleanup_unlocked', false) === true
            && Auth::user()?->isSuperAdmin() === true;
    }

    public function unlockCleanup(): void
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403);

        $configuredUsername = (string) config('transaction_cleanup.username');
        $configuredPassword = (string) config('transaction_cleanup.password');

        if ($configuredUsername === '' || $configuredPassword === '') {
            $this->notify('Menu ini belum dikonfigurasi. Isi TRANSACTION_CLEANUP_USERNAME dan TRANSACTION_CLEANUP_PASSWORD di .env.', 'error');

            return;
        }

        $throttleKey = 'cleanup-login:'.Auth::id();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->notify('Terlalu banyak percobaan gagal. Coba lagi beberapa menit lagi.', 'error');

            return;
        }

        $isValid = hash_equals($configuredUsername, $this->cleanupLoginUsername)
            && hash_equals($configuredPassword, $this->cleanupLoginPassword);

        if (! $isValid) {
            RateLimiter::hit($throttleKey, 300);
            $this->notify('Username atau password salah.', 'error');

            return;
        }

        RateLimiter::clear($throttleKey);
        session(['superadmin_cleanup_unlocked' => true]);
        $this->cleanupLoginUsername = '';
        $this->cleanupLoginPassword = '';
        $this->notify('Menu hapus riwayat transaksi berhasil dibuka.');
    }

    public function lockCleanup(): void
    {
        session()->forget('superadmin_cleanup_unlocked');
        $this->cleanupTenantId = null;
        $this->cleanupConfirmed = false;
    }

    public function deleteTenantTransactionHistory(): void
    {
        abort_unless($this->isCleanupUnlocked(), 403);

        $this->validate([
            'cleanupTenantId' => 'required|exists:tenants,id',
            'cleanupConfirmed' => 'accepted',
        ], [
            'cleanupTenantId.required' => 'Pilih tenant terlebih dahulu.',
            'cleanupConfirmed.accepted' => 'Centang konfirmasi bahwa tindakan ini permanen.',
        ]);

        $tenant = Tenant::findOrFail($this->cleanupTenantId);

        $deletedOrderCount = DB::transaction(function () use ($tenant) {
            $orderIds = Order::where('tenant_id', $tenant->id)->pluck('id');

            OrderPayment::whereIn('order_id', $orderIds)->delete();
            OrderItem::whereIn('order_id', $orderIds)->delete();

            return Order::whereIn('id', $orderIds)->delete();
        });

        PlatformActivityLog::create([
            'action' => "Riwayat transaksi {$tenant->name} dihapus",
            'description' => "{$deletedOrderCount} transaksi dihapus oleh ".Auth::user()->name,
            'icon' => 'fa-trash',
            'icon_color' => 'text-rose-500 bg-rose-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->cleanupTenantId = null;
        $this->cleanupConfirmed = false;
        $this->notify("{$deletedOrderCount} riwayat transaksi tenant '{$tenant->name}' berhasil dihapus.");
    }

    public function cleanupOrderCount(): int
    {
        if (! $this->isCleanupUnlocked() || ! $this->cleanupTenantId) {
            return 0;
        }

        return Order::where('tenant_id', $this->cleanupTenantId)->count();
    }
}
