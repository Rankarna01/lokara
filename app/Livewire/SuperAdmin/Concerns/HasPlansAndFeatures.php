<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\Plan;
use App\Models\PlatformActivityLog;
use App\Models\PlatformFeature;
use Illuminate\Support\Str;

trait HasPlansAndFeatures
{
    // Plans & Dynamic Pricing Modal State
    public bool $showEditPlanModal = false;
    public ?int $editingPlanId = null;
    public string $editPlanName = '';
    public string $editPlanBadge = '';
    public float|int|string $editPlanPriceMonthly = 0;
    public float|int|string $editPlanPriceYearly = 0;
    public int $editPlanMaxOutlets = 1;
    public int $editPlanMaxUsers = 1;
    public int $editPlanMaxProducts = 50;
    public bool $editPlanIsFeatured = false;
    public bool $editPlanIsActive = true;
    public array $editPlanFeatures = [];

    // Create Plan Modal State
    public bool $showCreatePlanModal = false;
    public string $newPlanName = '';
    public string $newPlanBadge = '';
    public float|int|string $newPlanPriceMonthly = 0;
    public float|int|string $newPlanPriceYearly = 0;
    public int $newPlanMaxOutlets = 1;
    public int $newPlanMaxUsers = 2;
    public int $newPlanMaxProducts = 100;
    public bool $newPlanIsFeatured = false;
    public array $newPlanFeatures = [];

    // Master Features Management Modal State
    public bool $showManageFeaturesModal = false;
    public string $newFeatureName = '';
    public string $newFeatureCategory = 'Kasir & Transaksi';

    // Edit Feature Modal State
    public bool $showEditFeatureModal = false;
    public ?int $editingFeatureId = null;
    public string $editingFeatureName = '';
    public string $editingFeatureCategory = 'Kasir & Transaksi';

    // Quick add feature inline state
    public string $quickAddFeatureName = '';
    public string $quickAddFeatureCategory = 'Kasir & Transaksi';

    protected function sanitizePrice($val): float
    {
        if (is_numeric($val)) {
            return (float) $val;
        }
        $clean = preg_replace('/[^0-9]/', '', (string) $val);
        return (float) ($clean ?: 0);
    }

    // ==========================================
    // PAKET & TARIF DINAMIS METHODS
    // ==========================================

    public function openEditPlanModal(int $planId)
    {
        $plan = Plan::findOrFail($planId);
        $this->editingPlanId = $plan->id;
        $this->editPlanName = $plan->name;
        $this->editPlanBadge = $plan->badge ?? '';
        $this->editPlanPriceMonthly = (float) $plan->price_monthly;
        $this->editPlanPriceYearly = (float) $plan->price_yearly;
        $this->editPlanMaxOutlets = (int) ($plan->limits['max_outlets'] ?? 1);
        $this->editPlanMaxUsers = (int) ($plan->limits['max_users'] ?? 1);
        $this->editPlanMaxProducts = (int) ($plan->limits['max_products'] ?? 50);
        $this->editPlanIsFeatured = (bool) $plan->is_featured;
        $this->editPlanIsActive = (bool) $plan->is_active;
        $this->editPlanFeatures = is_array($plan->features) ? $plan->features : [];

        $this->quickAddFeatureName = '';
        $this->quickAddFeatureCategory = 'Kasir & Transaksi';
        $this->resetErrorBag();
        $this->showEditPlanModal = true;
    }

    public function closeEditPlanModal()
    {
        $this->showEditPlanModal = false;
        $this->editingPlanId = null;
        $this->resetErrorBag();
    }

    public function toggleFeatureInPlan(string $featureName)
    {
        if (in_array($featureName, $this->editPlanFeatures)) {
            $this->editPlanFeatures = array_values(array_diff($this->editPlanFeatures, [$featureName]));
        } else {
            $this->editPlanFeatures[] = $featureName;
        }
    }

    public function selectAllPlanFeatures()
    {
        $this->editPlanFeatures = PlatformFeature::where('is_active', true)->pluck('name')->toArray();
    }

    public function deselectAllPlanFeatures()
    {
        $this->editPlanFeatures = [];
    }

    public function addFeatureInline()
    {
        $name = trim($this->quickAddFeatureName);
        if (empty($name)) {
            $this->addError('quickAddFeatureName', 'Nama fitur tidak boleh kosong.');
            return;
        }

        $existing = PlatformFeature::where('name', $name)->first();
        if (!$existing) {
            PlatformFeature::create([
                'name' => $name,
                'category' => $this->quickAddFeatureCategory ?: 'Kasir & Transaksi',
                'is_active' => true,
                'sort_order' => PlatformFeature::count() + 1,
            ]);
        }

        if (!in_array($name, $this->editPlanFeatures)) {
            $this->editPlanFeatures[] = $name;
        }

        $this->quickAddFeatureName = '';
        $this->resetErrorBag('quickAddFeatureName');
        $this->notify("Fitur '{$name}' berhasil ditambahkan ke checklist!", 'success');
    }

    public function savePlan()
    {
        $this->editPlanPriceMonthly = $this->sanitizePrice($this->editPlanPriceMonthly);
        $this->editPlanPriceYearly = $this->sanitizePrice($this->editPlanPriceYearly);

        $this->validate([
            'editPlanName' => 'required|min:2|max:100',
            'editPlanPriceMonthly' => 'required|numeric|min:0',
            'editPlanPriceYearly' => 'required|numeric|min:0',
            'editPlanMaxOutlets' => 'required|integer|min:1',
            'editPlanMaxUsers' => 'required|integer|min:1',
            'editPlanMaxProducts' => 'required|integer|min:1',
        ], [
            'editPlanName.required' => 'Nama paket wajib diisi.',
            'editPlanPriceMonthly.required' => 'Tarif bulanan wajib diisi.',
            'editPlanPriceMonthly.numeric' => 'Tarif bulanan harus berupa angka valid.',
            'editPlanPriceYearly.required' => 'Tarif tahunan wajib diisi.',
            'editPlanPriceYearly.numeric' => 'Tarif tahunan harus berupa angka valid.',
            'editPlanMaxOutlets.required' => 'Batas maksimal outlet wajib diisi.',
            'editPlanMaxUsers.required' => 'Batas maksimal karyawan wajib diisi.',
            'editPlanMaxProducts.required' => 'Batas maksimal master produk wajib diisi.',
        ]);

        $plan = Plan::findOrFail($this->editingPlanId);

        $plan->update([
            'name' => $this->editPlanName,
            'badge' => $this->editPlanBadge ? trim($this->editPlanBadge) : null,
            'price_monthly' => $this->editPlanPriceMonthly,
            'price_yearly' => $this->editPlanPriceYearly,
            'limits' => [
                'max_outlets' => (int) $this->editPlanMaxOutlets,
                'max_users' => (int) $this->editPlanMaxUsers,
                'max_products' => (int) $this->editPlanMaxProducts,
            ],
            'features' => array_values(array_unique($this->editPlanFeatures)),
            'is_featured' => (bool) $this->editPlanIsFeatured,
            'is_active' => (bool) $this->editPlanIsActive,
        ]);

        PlatformActivityLog::create([
            'action' => "Tarif & Fitur Paket {$plan->name} berhasil diperbarui",
            'description' => "Tarif Bulanan: Rp " . number_format($plan->price_monthly, 0, ',', '.') . " • " . count($plan->features) . " Fitur Aktif",
            'icon' => 'fa-credit-card',
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showEditPlanModal = false;
        $this->notify("Paket '{$plan->name}' berhasil diperbarui dengan tarif dinamis!");
    }

    // ==========================================
    // MASTER FITUR PLATFORM METHODS
    // ==========================================

    public function openManageFeaturesModal()
    {
        $this->showManageFeaturesModal = true;
        $this->newFeatureName = '';
        $this->newFeatureCategory = 'Kasir & Transaksi';
        $this->resetErrorBag();
    }

    public function closeManageFeaturesModal()
    {
        $this->showManageFeaturesModal = false;
        $this->resetErrorBag();
    }

    public function createMasterFeature()
    {
        $name = trim($this->newFeatureName);
        if (empty($name)) {
            $this->addError('newFeatureName', 'Nama fitur tidak boleh kosong.');
            return;
        }

        if (PlatformFeature::where('name', $name)->exists()) {
            $this->addError('newFeatureName', 'Fitur ini sudah ada di daftar master fitur.');
            return;
        }

        PlatformFeature::create([
            'name' => $name,
            'category' => $this->newFeatureCategory ?: 'Kasir & Transaksi',
            'is_active' => true,
            'sort_order' => PlatformFeature::count() + 1,
        ]);

        PlatformActivityLog::create([
            'action' => "Fitur baru ditambahkan ke platform",
            'description' => "Nama Fitur: {$name} • Kategori: {$this->newFeatureCategory}",
            'icon' => 'fa-list-check',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->newFeatureName = '';
        $this->resetErrorBag('newFeatureName');
        $this->notify("Fitur '{$name}' berhasil ditambahkan ke daftar master!");
    }

    public function openEditFeatureModal(int $featureId)
    {
        $feature = PlatformFeature::findOrFail($featureId);
        $this->editingFeatureId = $feature->id;
        $this->editingFeatureName = $feature->name;
        $this->editingFeatureCategory = $feature->category ?? 'Kasir & Transaksi';
        $this->resetErrorBag();
        $this->showEditFeatureModal = true;
    }

    public function closeEditFeatureModal()
    {
        $this->showEditFeatureModal = false;
        $this->editingFeatureId = null;
        $this->resetErrorBag();
    }

    public function updateFeature()
    {
        $newName = trim($this->editingFeatureName);
        if (empty($newName)) {
            $this->addError('editingFeatureName', 'Nama fitur tidak boleh kosong.');
            return;
        }

        $feature = PlatformFeature::findOrFail($this->editingFeatureId);
        $oldName = $feature->name;

        $exists = PlatformFeature::where('name', $newName)->where('id', '!=', $feature->id)->exists();
        if ($exists) {
            $this->addError('editingFeatureName', 'Nama fitur sudah digunakan oleh item lain.');
            return;
        }

        $feature->update([
            'name' => $newName,
            'category' => $this->editingFeatureCategory,
        ]);

        // Sync old name to new name in all existing plans
        if ($oldName !== $newName) {
            Plan::all()->each(function ($p) use ($oldName, $newName) {
                if (is_array($p->features) && in_array($oldName, $p->features)) {
                    $updated = array_map(fn($f) => $f === $oldName ? $newName : $f, $p->features);
                    $p->update(['features' => $updated]);
                }
            });

            // Update in currently open modal array if applicable
            if (in_array($oldName, $this->editPlanFeatures)) {
                $this->editPlanFeatures = array_map(fn($f) => $f === $oldName ? $newName : $f, $this->editPlanFeatures);
            }
        }

        $this->showEditFeatureModal = false;
        $this->editingFeatureId = null;
        $this->notify("Fitur '{$oldName}' berhasil diubah menjadi '{$newName}'!");
    }

    public function deleteFeature(int $featureId)
    {
        $feature = PlatformFeature::findOrFail($featureId);
        $featureName = $feature->name;

        $feature->delete();

        // Remove from all plans
        Plan::all()->each(function ($p) use ($featureName) {
            if (is_array($p->features) && in_array($featureName, $p->features)) {
                $p->update([
                    'features' => array_values(array_diff($p->features, [$featureName]))
                ]);
            }
        });

        // Remove from current editing plan array
        if (in_array($featureName, $this->editPlanFeatures)) {
            $this->editPlanFeatures = array_values(array_diff($this->editPlanFeatures, [$featureName]));
        }

        PlatformActivityLog::create([
            'action' => "Fitur dihapus dari platform",
            'description' => "Fitur '{$featureName}' telah dihapus dari seluruh paket",
            'icon' => 'fa-trash-can',
            'icon_color' => 'text-rose-500 bg-rose-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Fitur '{$featureName}' berhasil dihapus dari platform dan paket!");
    }

    // ==========================================
    // TAMBAH PAKET BARU METHODS
    // ==========================================

    public function openCreatePlanModal()
    {
        $this->newPlanName = '';
        $this->newPlanBadge = '';
        $this->newPlanPriceMonthly = 199000;
        $this->newPlanPriceYearly = 1990000;
        $this->newPlanMaxOutlets = 2;
        $this->newPlanMaxUsers = 5;
        $this->newPlanMaxProducts = 1000;
        $this->newPlanIsFeatured = false;
        $this->newPlanFeatures = ['Kasir Cepat', 'Laporan Dasar'];
        $this->resetErrorBag();
        $this->showCreatePlanModal = true;
    }

    public function closeCreatePlanModal()
    {
        $this->showCreatePlanModal = false;
        $this->resetErrorBag();
    }

    public function toggleFeatureInNewPlan(string $featureName)
    {
        if (in_array($featureName, $this->newPlanFeatures)) {
            $this->newPlanFeatures = array_values(array_diff($this->newPlanFeatures, [$featureName]));
        } else {
            $this->newPlanFeatures[] = $featureName;
        }
    }

    public function createPlan()
    {
        $this->newPlanPriceMonthly = $this->sanitizePrice($this->newPlanPriceMonthly);
        $this->newPlanPriceYearly = $this->sanitizePrice($this->newPlanPriceYearly);

        $this->validate([
            'newPlanName' => 'required|min:2|max:100',
            'newPlanPriceMonthly' => 'required|numeric|min:0',
            'newPlanPriceYearly' => 'required|numeric|min:0',
            'newPlanMaxOutlets' => 'required|integer|min:1',
            'newPlanMaxUsers' => 'required|integer|min:1',
            'newPlanMaxProducts' => 'required|integer|min:1',
        ], [
            'newPlanName.required' => 'Nama paket wajib diisi.',
            'newPlanPriceMonthly.required' => 'Tarif bulanan wajib diisi.',
            'newPlanPriceMonthly.numeric' => 'Tarif bulanan harus berupa angka valid.',
            'newPlanPriceYearly.required' => 'Tarif tahunan wajib diisi.',
            'newPlanPriceYearly.numeric' => 'Tarif tahunan harus berupa angka valid.',
            'newPlanMaxOutlets.required' => 'Batas maksimal outlet wajib diisi.',
            'newPlanMaxUsers.required' => 'Batas maksimal karyawan wajib diisi.',
            'newPlanMaxProducts.required' => 'Batas maksimal master produk wajib diisi.',
        ]);

        $baseSlug = Str::slug($this->newPlanName);
        $slug = $baseSlug;
        $counter = 1;
        while (Plan::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $plan = Plan::create([
            'name' => $this->newPlanName,
            'slug' => $slug,
            'badge' => $this->newPlanBadge ? trim($this->newPlanBadge) : null,
            'price_monthly' => $this->newPlanPriceMonthly,
            'price_yearly' => $this->newPlanPriceYearly,
            'limits' => [
                'max_outlets' => (int) $this->newPlanMaxOutlets,
                'max_users' => (int) $this->newPlanMaxUsers,
                'max_products' => (int) $this->newPlanMaxProducts,
            ],
            'features' => array_values(array_unique($this->newPlanFeatures)),
            'is_featured' => (bool) $this->newPlanIsFeatured,
            'is_active' => true,
        ]);

        PlatformActivityLog::create([
            'action' => "Paket baru '{$plan->name}' berhasil ditambahkan",
            'description' => "Tarif: Rp " . number_format($plan->price_monthly, 0, ',', '.') . "/bln",
            'icon' => 'fa-plus',
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showCreatePlanModal = false;
        $this->notify("Paket '{$plan->name}' berhasil dibuat!");
    }
}

