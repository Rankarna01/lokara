<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait HasReceiptPrinting
{
    public function printReceipt(int $orderId): void
    {
        $tenant = $this->getCurrentTenant();
        $order = $tenant
            ? Order::with(['items', 'payments', 'outlet', 'customer', 'user'])->where('tenant_id', $tenant->id)->find($orderId)
            : null;

        if (! $order) {
            $this->notify('Pesanan tidak ditemukan, struk tidak bisa dicetak.', 'error');

            return;
        }

        $html = view('livewire.pos.receipts.thermal', [
            'order' => $order,
            'settings' => $tenant->settings ?? [],
            'tenantName' => $tenant->name,
            'paperWidth' => $this->printerPaperWidth,
            'copies' => max(1, $this->printerCopies),
        ])->render();

        $key = Str::random(40);
        Cache::put('pos-receipt:'.$key, [
            'user_id' => Auth::id(),
            'html' => $html,
        ], now()->addMinutes(5));

        $this->dispatch('open-pos-receipt', url: route('pos.receipt', $key));
        $this->notify("Struk {$order->order_number} siap dicetak.");
    }

    public function reprintReceipt(int $orderId): void
    {
        $this->printReceipt($orderId);
    }
}
