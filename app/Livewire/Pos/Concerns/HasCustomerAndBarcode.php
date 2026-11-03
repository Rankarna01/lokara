<?php

namespace App\Livewire\Pos\Concerns;

use App\Models\Customer;
use App\Models\Product;
use Illuminate\Validation\Rule;

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

    // Form tambah member baru dari modal pelanggan
    public bool $showNewMemberForm = false;

    public string $newMemberName = '';

    public string $newMemberPhone = '';

    public string $newMemberTier = 'silver';

    // Help & Shortcuts Modal State
    public bool $showHelpModal = false;

    // Barcode Scan Modal State
    public bool $showBarcodeModal = false;

    public string $barcodeInput = '';

    public function selectCustomer(int $customerId)
    {
        $customer = Customer::where('tenant_id', $this->getCurrentTenant()?->id)->find($customerId);

        if (! $customer) {
            $this->notify('Pelanggan tidak ditemukan.', 'error');

            return;
        }

        $this->selectedCustomer = [
            'id' => $customer->id,
            'name' => $customer->name,
            'phone' => $customer->phone ?? '-',
            'points' => (int) $customer->points,
        ];

        $this->showCustomerModal = false;
        $this->customerSearch = '';
        $this->notify("Member {$customer->name} dipilih untuk transaksi ini.");
    }

    public function openNewMemberForm(): void
    {
        $this->resetErrorBag();
        $this->newMemberName = '';
        $this->newMemberPhone = '';
        $this->newMemberTier = 'silver';
        $this->showNewMemberForm = true;
    }

    public function saveNewMember(): void
    {
        $tenant = $this->getCurrentTenant();
        if (! $tenant) {
            $this->notify('Tenant tidak ditemukan.', 'error');

            return;
        }

        $this->validate([
            'newMemberName' => 'required|string|min:3|max:255',
            'newMemberPhone' => 'required|string|min:8|max:20',
            'newMemberTier' => ['required', Rule::in(['silver', 'gold', 'platinum'])],
        ], [
            'newMemberName.required' => 'Nama member wajib diisi.',
            'newMemberName.min' => 'Nama member minimal 3 karakter.',
            'newMemberPhone.required' => 'Nomor WhatsApp wajib diisi.',
            'newMemberPhone.min' => 'Nomor WhatsApp minimal 8 digit.',
        ]);

        $phone = trim($this->newMemberPhone);
        if (Customer::where('tenant_id', $tenant->id)->where('phone', $phone)->exists()) {
            $this->addError('newMemberPhone', 'Nomor WhatsApp ini sudah terdaftar sebagai member.');

            return;
        }

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'name' => trim($this->newMemberName),
            'phone' => $phone,
            'membership_tier' => $this->newMemberTier,
            'points' => 0,
        ]);

        $this->showNewMemberForm = false;
        $this->selectCustomer($customer->id);
        $this->notify("Member baru {$customer->name} berhasil ditambahkan dan dipilih.");
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
