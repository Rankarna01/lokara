<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Tenant;

trait HasPurchases
{
    // Purchase Order Modal & Filters
    public bool $showPoModal = false;
    public ?int $poSupplierId = null;
    public float $poTotalAmount = 0;
    public string $poNotes = '';
    public string $poExpectedDate = '';
    public string $searchPo = '';
    public string $poStatusFilter = 'all'; // 'all', 'ordered', 'received', 'cancelled'
    public string $poSupplierFilter = 'all';
    public string $poDateFilter = 'all'; // 'all', 'today', '7_days', '30_days', 'this_month', 'custom'
    public string $poStartDate = '';
    public string $poEndDate = '';
    public bool $showPoDetailModal = false;
    public ?int $viewingPoId = null;

    // Supplier Management Modal & Form
    public bool $showSupplierModal = false;
    public ?int $editingSupplierId = null;
    public string $supplierName = '';
    public string $supplierContactPerson = '';
    public string $supplierPhone = '';
    public string $supplierEmail = '';
    public string $supplierAddress = '';
    public int $supplierTermsDays = 0;
    public bool $supplierIsActive = true;
    public string $searchSupplier = '';
    public string $supplierStatusFilter = 'all'; // 'all', 'active', 'inactive'

    public function openNewSupplierModal(): void
    {
        $this->resetSupplierForm();
        $this->showSupplierModal = true;
    }

    public function editSupplier(int $id): void
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingSupplierId = $supplier->id;
        $this->supplierName = $supplier->name;
        $this->supplierContactPerson = $supplier->contact_person ?? '';
        $this->supplierPhone = $supplier->phone ?? '';
        $this->supplierEmail = $supplier->email ?? '';
        $this->supplierAddress = $supplier->address ?? '';
        $this->supplierTermsDays = (int) ($supplier->terms_days ?? 0);
        $this->supplierIsActive = (bool) $supplier->is_active;
        $this->showSupplierModal = true;
    }

    public function saveSupplier(): void
    {
        $this->validate([
            'supplierName' => 'required|min:2|max:100',
            'supplierContactPerson' => 'nullable|max:50',
            'supplierPhone' => 'nullable|max:30',
            'supplierEmail' => 'nullable|email|max:100',
            'supplierAddress' => 'nullable|max:255',
            'supplierTermsDays' => 'required|integer|min:0|max:180',
        ]);

        if ($this->editingSupplierId) {
            $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($this->editingSupplierId);
            $supplier->update([
                'name' => $this->supplierName,
                'contact_person' => $this->supplierContactPerson ?: null,
                'phone' => $this->supplierPhone ?: null,
                'email' => $this->supplierEmail ?: null,
                'address' => $this->supplierAddress ?: null,
                'terms_days' => $this->supplierTermsDays,
                'is_active' => $this->supplierIsActive,
            ]);
            $this->showToast("Data rekanan supplier '{$supplier->name}' berhasil diperbarui.");
        } else {
            $supplier = Supplier::create([
                'tenant_id' => $this->tenantId,
                'name' => $this->supplierName,
                'contact_person' => $this->supplierContactPerson ?: null,
                'phone' => $this->supplierPhone ?: null,
                'email' => $this->supplierEmail ?: null,
                'address' => $this->supplierAddress ?: null,
                'terms_days' => $this->supplierTermsDays,
                'is_active' => $this->supplierIsActive,
            ]);
            $this->showToast("Supplier '{$supplier->name}' berhasil ditambahkan!");

            if ($this->showPoModal) {
                $this->poSupplierId = $supplier->id;
            }
        }

        $this->showSupplierModal = false;
        $this->resetSupplierForm();
    }

    public function deleteSupplier(int $id): void
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $poCount = $supplier->purchaseOrders()->count();

        if ($poCount > 0) {
            $supplier->update(['is_active' => false]);
            $this->showToast("Supplier '{$supplier->name}' memiliki {$poCount} riwayat PO, status dinonaktifkan.", 'warning');
            return;
        }

        $name = $supplier->name;
        $supplier->delete();
        $this->showToast("Supplier '{$name}' berhasil dihapus.");
    }

    public function toggleSupplierStatus(int $id): void
    {
        $supplier = Supplier::where('tenant_id', $this->tenantId)->findOrFail($id);
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();
        $status = $supplier->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->showToast("Status supplier '{$supplier->name}' {$status}.");
    }

    public function resetSupplierForm(): void
    {
        $this->editingSupplierId = null;
        $this->supplierName = '';
        $this->supplierContactPerson = '';
        $this->supplierPhone = '';
        $this->supplierEmail = '';
        $this->supplierAddress = '';
        $this->supplierTermsDays = 0;
        $this->supplierIsActive = true;
    }

    public function openPoModal(?int $supplierId = null): void
    {
        $suppliers = Supplier::where('tenant_id', $this->tenantId)->where('is_active', true)->get();
        if ($suppliers->isEmpty()) {
            $this->showToast('Belum ada supplier terdaftar. Silakan tambahkan supplier terlebih dahulu.', 'warning');
            $this->openNewSupplierModal();
            return;
        }

        $this->poSupplierId = $supplierId ?? ($this->poSupplierId && $suppliers->contains('id', $this->poSupplierId) ? $this->poSupplierId : $suppliers->first()?->id);
        $this->poTotalAmount = 1500000;
        $this->poNotes = 'Pengadaan bahan baku & kebutuhan operasional';
        $this->poExpectedDate = now()->addDays(3)->toDateString();
        $this->showPoModal = true;
    }

    public function savePurchaseOrder(): void
    {
        $this->validate([
            'poSupplierId' => 'required|exists:suppliers,id',
            'poTotalAmount' => 'required|numeric|min:1000',
            'poExpectedDate' => 'nullable|date',
            'poNotes' => 'nullable|max:255',
        ]);

        $poCount = PurchaseOrder::where('tenant_id', $this->tenantId)->count() + 1;
        $poNumber = 'PO-' . date('Ym') . '-' . str_pad((string) $poCount, 3, '0', STR_PAD_LEFT);

        PurchaseOrder::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'supplier_id' => $this->poSupplierId,
            'po_number' => $poNumber,
            'order_date' => now()->toDateString(),
            'expected_date' => $this->poExpectedDate ?: now()->addDays(3)->toDateString(),
            'status' => 'ordered',
            'total_amount' => $this->poTotalAmount,
            'notes' => $this->poNotes ?: 'Pengadaan stok operasional',
        ]);

        $this->showPoModal = false;
        $this->showToast("Purchase Order {$poNumber} senilai Rp " . number_format($this->poTotalAmount, 0, ',', '.') . " berhasil diterbitkan!");
    }

    public function markPoReceived(int $id): void
    {
        $po = PurchaseOrder::where('tenant_id', $this->tenantId)->findOrFail($id);
        $po->update(['status' => 'received']);
        $this->showToast("PO {$po->po_number} berhasil ditandai Diterima (GRN). Stok operasional diperbarui!");
    }

    public function cancelPo(int $id): void
    {
        $po = PurchaseOrder::where('tenant_id', $this->tenantId)->findOrFail($id);
        if ($po->status === 'received') {
            $this->showToast("PO {$po->po_number} sudah berstatus Diterima, tidak dapat dibatalkan.", 'error');
            return;
        }
        $po->update(['status' => 'cancelled']);
        $this->showToast("PO {$po->po_number} berhasil dibatalkan.", 'info');
    }

    public function viewPoDetail(int $id): void
    {
        $this->viewingPoId = $id;
        $this->showPoDetailModal = true;
    }

    public function resetPoFilters(): void
    {
        $this->searchPo = '';
        $this->poStatusFilter = 'all';
        $this->poSupplierFilter = 'all';
        $this->poDateFilter = 'all';
        $this->poStartDate = '';
        $this->poEndDate = '';
    }

    public function seedDefaultSuppliers(): void
    {
        $tenant = Tenant::find($this->tenantId);
        $isSalon = $tenant?->isSalon();

        if ($isSalon) {
            $defaults = [
                [
                    'name' => "PT L'Oreal Professional Indonesia",
                    'contact_person' => 'Maya Indriani',
                    'phone' => '081233445566',
                    'email' => 'order@loreal-pro.co.id',
                    'address' => 'Jl. TB Simatupang No. 88, Jakarta Selatan',
                    'terms_days' => 14,
                ],
                [
                    'name' => 'CV Barbershop Supplies Nusantara',
                    'contact_person' => 'Budi Santoso',
                    'phone' => '081988776655',
                    'email' => 'sales@barbersupplies.id',
                    'address' => 'Komp. Ruko Darmo Park II Blok B, Surabaya',
                    'terms_days' => 7,
                ],
                [
                    'name' => 'Toko Handuk & Aksesoris Salon Berkah',
                    'contact_person' => 'Bu Hj. Nur',
                    'phone' => '081344556677',
                    'email' => 'toko.berkah@gmail.com',
                    'address' => 'Pasar Turi Baru Blok C-12, Surabaya',
                    'terms_days' => 0,
                ],
            ];
        } else {
            $defaults = [
                [
                    'name' => 'PT Kopi Nusantara Makmur',
                    'contact_person' => 'Hendro Setiawan',
                    'phone' => '081122334455',
                    'email' => 'sales@kopinusantara.co.id',
                    'address' => 'Kawasan Industri Sentul Kav. 12, Bogor',
                    'terms_days' => 14,
                ],
                [
                    'name' => 'CV Dairy Fresh Sejahtera',
                    'contact_person' => 'Siti Nurhaliza',
                    'phone' => '081299887766',
                    'email' => 'order@dairyfresh.com',
                    'address' => 'Jl. Pasar Minggu Raya No. 88, Jakarta Selatan',
                    'terms_days' => 7,
                ],
                [
                    'name' => 'Toko Kemasan Jaya Mandiri',
                    'contact_person' => 'Aheng Salim',
                    'phone' => '081765432100',
                    'email' => 'sales@kemasanjaya.id',
                    'address' => 'Glodok Plaza Blok B-14, Jakarta Barat',
                    'terms_days' => 0,
                ],
            ];
        }

        foreach ($defaults as $d) {
            Supplier::firstOrCreate(
                ['tenant_id' => $this->tenantId, 'name' => $d['name']],
                array_merge($d, ['tenant_id' => $this->tenantId, 'is_active' => true])
            );
        }

        $this->showToast('Rekomendasi pemasok standar berhasil ditambahkan!');
    }
}

