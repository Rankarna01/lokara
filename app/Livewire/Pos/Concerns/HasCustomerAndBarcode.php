<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Product;

trait HasCustomerAndBarcode
{
    // Orders Management State (activeNav = 'pesanan')
    public string $orderFilterStatus = 'all'; // 'all', 'completed', 'hold', 'processing', 'voided'
    public string $orderSearch = '';

    // Transactions Modal State
    public bool $showTransactionsModal = false;

    // Customer Selection Modal State
    public bool $showCustomerModal = false;
    public string $customerSearch = '';

    // Help & Shortcuts Modal State
    public bool $showHelpModal = false;

    // Barcode Scan Modal State
    public bool $showBarcodeModal = false;
    public string $barcodeInput = '';

    public function selectCustomer(string $name, string $phone, int $points)
    {
        $this->selectedCustomer = [
            'name' => $name,
            'phone' => $phone,
            'points' => $points,
        ];

        $this->showCustomerModal = false;
        $this->notify("Member {$name} dipilih untuk transaksi ini.");
    }

    public function removeSelectedCustomer()
    {
        $this->selectedCustomer = null;
        $this->notify('Member dilepas dari transaksi.');
    }

    public function scanBarcode()
    {
        $product = Product::where('barcode', $this->barcodeInput)
            ->orWhere('sku', $this->barcodeInput)
            ->first();

        if ($product) {
            $this->addToCart($product->id);
            $this->showBarcodeModal = false;
            $this->barcodeInput = '';
            $this->notify("Barcode terdeteksi: {$product->name}");
        } else {
            $this->notify('Produk barcode tidak ditemukan!', 'error');
        }
    }
}

