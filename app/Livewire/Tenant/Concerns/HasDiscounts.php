<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Discount;

trait HasDiscounts
{
    public bool $showDiscountModal = false;
    public ?int $editingDiscountId = null;
    public string $discountName = '';
    public string $discountCode = '';
    public string $discountType = 'percentage'; // 'percentage' or 'fixed'
    public float $discountValue = 15;
    public float $discountMinPurchase = 50000;
    public ?float $discountMaxAmount = 25000;
    public string $discountApplicableTo = 'all'; // 'all', 'services', 'products'
    public ?string $discountStartDate = null;
    public ?string $discountEndDate = null;
    public ?int $discountMaxUsage = null;
    public bool $discountIsActive = true;
    public string $discountNotes = '';
    public string $searchDiscount = '';
    public string $discountStatusFilter = 'all'; // 'all', 'active', 'inactive'

    public function openNewDiscountModal(): void
    {
        $this->resetDiscountForm();
        $this->showDiscountModal = true;
    }

    public function editDiscount(int $id): void
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingDiscountId = $discount->id;
        $this->discountName = $discount->name;
        $this->discountCode = $discount->code ?? '';
        $this->discountType = $discount->type;
        $this->discountValue = (float) $discount->value;
        $this->discountMinPurchase = (float) $discount->min_purchase;
        $this->discountMaxAmount = $discount->max_discount ? (float) $discount->max_discount : null;
        $this->discountApplicableTo = $discount->applicable_to ?? 'all';
        $this->discountStartDate = $discount->start_date?->format('Y-m-d');
        $this->discountEndDate = $discount->end_date?->format('Y-m-d');
        $this->discountMaxUsage = $discount->max_usage;
        $this->discountIsActive = (bool) $discount->is_active;
        $this->discountNotes = $discount->notes ?? '';
        $this->showDiscountModal = true;
    }

    public function saveDiscount(): void
    {
        $this->validate([
            'discountName' => 'required|min:2',
            'discountType' => 'required|in:percentage,fixed',
            'discountValue' => 'required|numeric|min:1',
            'discountMinPurchase' => 'nullable|numeric|min:0',
        ]);

        if ($this->discountType === 'percentage' && $this->discountValue > 100) {
            $this->addError('discountValue', 'Diskon persentase maksimal 100%.');
            return;
        }

        $code = !empty($this->discountCode) ? strtoupper(trim($this->discountCode)) : null;

        $payload = [
            'tenant_id' => $this->tenantId,
            'name' => $this->discountName,
            'code' => $code,
            'type' => $this->discountType,
            'value' => $this->discountValue,
            'min_purchase' => $this->discountMinPurchase ?: 0,
            'max_discount' => ($this->discountType === 'percentage' && $this->discountMaxAmount) ? $this->discountMaxAmount : null,
            'applicable_to' => $this->discountApplicableTo,
            'start_date' => $this->discountStartDate ?: null,
            'end_date' => $this->discountEndDate ?: null,
            'max_usage' => $this->discountMaxUsage ?: null,
            'is_active' => $this->discountIsActive,
            'notes' => $this->discountNotes ?: null,
        ];

        if ($this->editingDiscountId) {
            $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($this->editingDiscountId);
            $discount->update($payload);
            $this->showToast("Diskon '{$discount->name}' berhasil diperbarui.");
        } else {
            Discount::create($payload);
            $this->showToast("Diskon '{$this->discountName}' berhasil dibuat!");
        }

        $this->showDiscountModal = false;
        $this->resetDiscountForm();
    }

    public function toggleDiscountStatus(int $id): void
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $discount->is_active = !$discount->is_active;
        $discount->save();
        $status = $discount->is_active ? 'Aktif' : 'Nonaktif';
        $this->showToast("Status promo {$discount->name} diubah menjadi '{$status}'");
    }

    public function deleteDiscount(int $id): void
    {
        $discount = Discount::where('tenant_id', $this->tenantId)->findOrFail($id);
        $name = $discount->name;
        $discount->delete();
        $this->showToast("Diskon '{$name}' berhasil dihapus.");
    }

    private function resetDiscountForm(): void
    {
        $this->editingDiscountId = null;
        $this->discountName = '';
        $this->discountCode = '';
        $this->discountType = 'percentage';
        $this->discountValue = 15;
        $this->discountMinPurchase = 50000;
        $this->discountMaxAmount = 25000;
        $this->discountApplicableTo = 'all';
        $this->discountStartDate = now()->toDateString();
        $this->discountEndDate = now()->addMonth()->toDateString();
        $this->discountMaxUsage = 100;
        $this->discountIsActive = true;
        $this->discountNotes = '';
    }
}

