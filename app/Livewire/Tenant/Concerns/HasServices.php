<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

trait HasServices
{
    public bool $showServiceModal = false;

    public ?int $editingServiceId = null;

    public string $serviceName = '';

    public ?int $serviceCategoryId = null;

    public string $serviceCategoryTag = 'Haircut • Pria';

    public float $servicePrice = 60000;

    public int $serviceDuration = 40; // Menit

    public string $serviceCommissionType = 'fixed'; // 'fixed' or 'percentage'

    public float $serviceCommissionValue = 20000;

    public float $serviceCostPrice = 5000;

    public string $serviceImageUrl = '';

    public $serviceImageFile = null;

    public bool $serviceIsAvailable = true;

    public string $searchService = '';

    public string $selectedServiceCategoryFilter = 'all';

    public function openNewServiceModal(): void
    {
        $this->resetServiceForm();
        $this->showServiceModal = true;
    }

    public function editService(int $id): void
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $this->editingServiceId = $service->id;
        $this->serviceName = $service->name;
        $this->serviceCategoryId = $service->category_id;
        $this->serviceCategoryTag = $service->category_tag ?? 'Layanan Salon';
        $this->servicePrice = (float) $service->base_price;
        $this->serviceDuration = (int) ($service->duration_minutes ?? 40);
        $this->serviceCommissionType = $service->commission_type ?? 'fixed';
        $this->serviceCommissionValue = (float) ($service->commission_value ?? 0);
        $this->serviceCostPrice = (float) ($service->cost_price ?? 0);
        $this->serviceImageUrl = $service->image_url ?? '';
        $this->serviceImageFile = null;
        $this->serviceIsAvailable = (bool) $service->is_available;
        $this->showServiceModal = true;
    }

    public function removeServiceImage(): void
    {
        $this->serviceImageFile = null;
        $this->serviceImageUrl = '';
    }

    public function saveService(): void
    {
        $this->validate([
            'serviceName' => 'required|min:2',
            'servicePrice' => 'required|numeric|min:0',
            'serviceDuration' => 'required|integer|min:5',
            'serviceCommissionValue' => 'nullable|numeric|min:0',
            'serviceCostPrice' => 'nullable|numeric|min:0',
            'serviceImageFile' => 'nullable|image|max:3072',
        ]);

        $commissionVal = (float) ($this->serviceCommissionValue ?? 0);

        if ($this->serviceCommissionType === 'percentage' && $commissionVal > 100) {
            $this->addError('serviceCommissionValue', 'Komisi persentase tidak boleh melebihi 100%.');

            return;
        }

        if ($this->serviceImageFile) {
            $path = $this->serviceImageFile->store('services', 'public');
            $this->serviceImageUrl = '/storage/'.$path;
        }

        if ($this->editingServiceId) {
            $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($this->editingServiceId);
            $service->update([
                'name' => $this->serviceName,
                'category_id' => $this->serviceCategoryId,
                'category_tag' => $this->serviceCategoryTag ?: 'Layanan Salon',
                'base_price' => $this->servicePrice,
                'cost_price' => $this->serviceCostPrice,
                'duration_minutes' => $this->serviceDuration,
                'commission_type' => $this->serviceCommissionType,
                'commission_value' => $commissionVal,
                'image_url' => $this->serviceImageUrl ?: null,
                'is_available' => $this->serviceIsAvailable,
            ]);
            $this->showToast("Layanan '{$service->name}' berhasil diperbarui.");
        } else {
            Product::create([
                'tenant_id' => $this->tenantId,
                'type' => 'service',
                'category_id' => $this->serviceCategoryId,
                'category_tag' => $this->serviceCategoryTag ?: 'Layanan Salon',
                'name' => $this->serviceName,
                'slug' => Str::slug($this->serviceName).'-'.rand(100, 999),
                'sku' => 'SRV-'.strtoupper(Str::random(5)),
                'base_price' => $this->servicePrice,
                'cost_price' => $this->serviceCostPrice,
                'duration_minutes' => $this->serviceDuration,
                'commission_type' => $this->serviceCommissionType,
                'commission_value' => $commissionVal,
                'image_url' => $this->serviceImageUrl ?: 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=300&h=300&q=80',
                'is_active' => true,
                'is_available' => $this->serviceIsAvailable,
            ]);
            $this->showToast("Layanan '{$this->serviceName}' berhasil ditambahkan ke katalog!");
        }

        $this->showServiceModal = false;
        $this->resetServiceForm();
    }

    public function toggleServiceAvailability(int $id): void
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $service->is_available = ! $service->is_available;
        $service->save();
        $status = $service->is_available ? 'Tersedia' : 'Tidak Aktif / Libur';
        $this->showToast("Status layanan '{$service->name}' diubah menjadi '{$status}'");
    }

    public function deleteService(int $id): void
    {
        $service = Product::where('tenant_id', $this->tenantId)->where('type', 'service')->findOrFail($id);
        $name = $service->name;

        if ($service->orderItems()->exists()) {
            $this->showToast("Layanan '{$name}' sudah pernah dipakai di transaksi dan tidak bisa dihapus. Nonaktifkan saja ketersediaannya.", 'error');

            return;
        }

        $service->delete();
        $this->showToast("Layanan '{$name}' berhasil dihapus.");
    }

    private function resetServiceForm(): void
    {
        $this->editingServiceId = null;
        $this->serviceName = '';
        $this->serviceCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        $this->serviceCategoryTag = 'Haircut & Styling';
        $this->servicePrice = 60000;
        $this->serviceDuration = 45;
        $this->serviceCommissionType = 'fixed';
        $this->serviceCommissionValue = 20000;
        $this->serviceCostPrice = 5000;
        $this->serviceImageFile = null;
        $this->serviceImageUrl = '';
        $this->serviceIsAvailable = true;
    }
}
