<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Order;

trait HasOrdersAndVoid
{
    public bool $showVoidModal = false;
    public ?int $voidOrderId = null;
    public string $voidPin = '';
    public string $voidReason = 'Kesalahan pesanan';

    public function openVoidModal(int $orderId): void
    {
        $this->voidOrderId = $orderId;
        $this->voidPin = '';
        $this->voidReason = 'Kesalahan pesanan';
        $this->showVoidModal = true;
    }

    public function executeVoidOrder(): void
    {
        if ($this->voidPin !== '888888' && $this->voidPin !== '123456') {
            $this->showToast('PIN Otorisasi Manajer salah!', 'error');
            return;
        }

        $order = Order::where('tenant_id', $this->tenantId)->findOrFail($this->voidOrderId);
        $order->update([
            'status' => 'voided',
            'notes' => ($order->notes ? $order->notes . ' | ' : '') . "VOID oleh Manajer. Alasan: {$this->voidReason}",
        ]);

        $this->showVoidModal = false;
        $this->showToast("Pesanan {$order->order_number} berhasil di-VOID.", 'info');
    }
}

