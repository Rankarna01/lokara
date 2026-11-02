<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Order;
use Carbon\Carbon;

trait HasHoldAndRecall
{
    // Hold Orders Modal State
    public bool $showHoldModal = false;
    public array $heldOrders = [];

    public function holdOrder()
    {
        if (empty($this->cart)) {
            $this->notify('Keranjang kosong untuk disimpan.', 'error');
            return;
        }

        $isSalon = method_exists($this, 'isSalonTenant') && $this->isSalonTenant();
        $totals = $this->calculateTotals();
        $this->heldOrders[] = [
            'id' => uniqid('HOLD-'),
            'table' => $isSalon ? 'Layanan Salon' : ($this->orderType === 'dine_in' ? $this->selectedTable : 'Take Away'),
            'customer' => $this->selectedCustomer['name'] ?? 'Pelanggan Umum',
            'items' => $this->cart,
            'total' => $totals['total'],
            'time' => Carbon::now()->format('H:i'),
        ];

        $this->cart = [];
        $this->selectedCustomer = null;
        $this->calculateTotals();
        $this->notify('Pesanan berhasil ditahan/disimpan.');
    }

    public function recallOrder(string $holdId)
    {
        foreach ($this->heldOrders as $key => $held) {
            if ($held['id'] === $holdId) {
                $this->cart = $held['items'];
                $this->selectedTable = str_contains($held['table'], 'Meja') ? $held['table'] : 'Meja 01';
                $this->orderType = str_contains($held['table'], 'Meja') ? 'dine_in' : 'take_away';
                unset($this->heldOrders[$key]);
                $this->heldOrders = array_values($this->heldOrders);
                break;
            }
        }

        $this->showHoldModal = false;
        $this->calculateTotals();
        $this->notify('Pesanan berhasil dimuat kembali ke keranjang.');
    }

    public function recallDbOrder(int $orderId)
    {
        $order = Order::with('items')->find($orderId);
        if (!$order) {
            $this->notify('Pesanan tidak ditemukan.', 'error');
            return;
        }

        $this->cart = $order->items->map(fn($it) => [
            'product_id' => $it->product_id,
            'name' => $it->product_name,
            'variant' => $it->variant_name ?? 'Regular',
            'price' => $it->unit_price,
            'qty' => $it->qty,
            'subtotal' => $it->subtotal,
            'image_url' => '',
        ])->toArray();

        $this->selectedTable = $order->table_number ?? 'Meja 01';
        $this->orderType = $order->order_type ?? 'dine_in';
        $this->activeNav = 'katalog';
        $this->calculateTotals();
        $this->notify("Pesanan {$order->order_number} dimuat kembali ke kasir untuk diproses.");
    }
}

