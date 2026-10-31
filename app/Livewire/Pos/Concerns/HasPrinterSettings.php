<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Order;

trait HasPrinterSettings
{
    // Thermal Printer Settings State
    public bool $showPrinterModal = false;
    public string $printerConnectionType = 'bluetooth'; // 'bluetooth', 'usb', 'rawbt', 'network'
    public string $printerDeviceName = 'Panda POS-58BT';
    public string $printerPaperWidth = '58mm'; // '58mm', '80mm'
    public string $printerPortOrIp = 'COM3';
    public string $thirdPartyApp = 'rawbt'; // 'rawbt', 'escpos_service'
    public bool $printerAutoCut = true;
    public bool $printerKickDrawer = true;
    public int $printerCopies = 1;
    public bool $printerPrintLogo = true;
    public bool $showTestPrintPreview = false;

    public function savePrinterSettings()
    {
        $this->showPrinterModal = false;
        $this->notify("Pengaturan printer thermal ({$this->printerDeviceName} via {$this->printerConnectionType}) berhasil disimpan!");
    }

    public function testPrintThermal()
    {
        $this->showTestPrintPreview = true;
        $this->notify("Perintah cetak thermal dikirim ke printer {$this->printerDeviceName} ({$this->printerPaperWidth})!");
    }

    public function reprintReceipt(int $orderId)
    {
        $order = Order::with(['items', 'payments'])->find($orderId);
        if (!$order) {
            $this->notify('Pesanan tidak ditemukan.', 'error');
            return;
        }

        $this->completedOrderData = [
            'order_number' => $order->order_number,
            'time' => $order->created_at->format('H:i:s'),
            'subtotal' => $order->subtotal,
            'discount' => $order->discount_amount,
            'tax' => $order->tax_amount,
            'total' => $order->total_amount,
            'payment_method' => $order->payments->first()?->payment_method ?? 'cash',
            'cash_tendered' => $order->payments->first()?->cash_tendered ?? $order->total_amount,
            'change_due' => $order->payments->first()?->change_due ?? 0,
            'items' => $order->items->map(fn($it) => [
                'name' => $it->product_name,
                'variant' => $it->variant_name,
                'qty' => $it->qty,
                'price' => $it->unit_price,
                'subtotal' => $it->subtotal,
            ])->toArray(),
        ];

        $this->showSuccessModal = true;
        $this->notify("Mencetak ulang struk thermal untuk {$order->order_number}...");
    }
}

