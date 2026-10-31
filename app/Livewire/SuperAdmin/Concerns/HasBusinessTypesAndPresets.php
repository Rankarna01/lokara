<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\BusinessType;
use App\Models\PlatformActivityLog;
use Illuminate\Support\Str;

trait HasBusinessTypesAndPresets
{
    // Business Type / Template Usaha State
    public bool $showEditPresetModal = false;
    public ?int $editingBusinessTypeId = null;
    public string $editBtName = '';
    public string $editBtIcon = 'fa-store';
    public string $editBtDescription = '';
    public bool $editBtIsActive = true;
    public array $editBtPresetModules = [];
    public string $editBtDefaultUnits = '';
    public bool $editBtHasRecipes = false;
    public bool $editBtHasTables = false;
    public bool $editBtHasModifiers = false;
    public bool $editBtHasBarcode = false;
    public bool $editBtHasVariants = false;
    public bool $editBtHasCommissions = false;
    public bool $editBtHasServices = false;
    public bool $editBtHasOrderStatus = false;
    public bool $editBtHasCreditSales = false;
    public bool $editBtHasTieredPricing = false;
    public string $newPresetModuleName = '';

    // Create Business Type State
    public bool $showCreateBusinessTypeModal = false;
    public string $newBtName = '';
    public string $newBtIcon = 'fa-store';
    public string $newBtDescription = '';
    public string $newBtDefaultUnits = 'pcs, pack';
    public array $newBtPresetModules = ['Kasir Cepat & Struk WhatsApp', 'Manajemen Stok Standar'];

    // ==========================================
    // TEMPLATE USAHA / BUSINESS TYPE PRESET METHODS
    // ==========================================

    public function openEditPresetModal(int $id)
    {
        $bt = BusinessType::findOrFail($id);
        $this->editingBusinessTypeId = $bt->id;
        $this->editBtName = $bt->name;
        $this->editBtIcon = $bt->icon ?? 'fa-store';
        $this->editBtDescription = $bt->description ?? '';
        $this->editBtIsActive = (bool) $bt->is_active;

        $settings = is_array($bt->default_settings) ? $bt->default_settings : [];
        $this->editBtPresetModules = $bt->preset_modules;
        $this->editBtDefaultUnits = implode(', ', $settings['default_units'] ?? ['pcs']);
        $this->editBtHasRecipes = (bool) ($settings['has_recipes'] ?? false);
        $this->editBtHasTables = (bool) ($settings['has_tables'] ?? false);
        $this->editBtHasModifiers = (bool) ($settings['has_modifiers'] ?? false);
        $this->editBtHasBarcode = (bool) ($settings['has_barcode'] ?? false);
        $this->editBtHasVariants = (bool) ($settings['has_variants'] ?? false);
        $this->editBtHasCommissions = (bool) ($settings['has_commissions'] ?? false);
        $this->editBtHasServices = (bool) ($settings['has_services'] ?? false);
        $this->editBtHasOrderStatus = (bool) ($settings['has_order_status'] ?? false);
        $this->editBtHasCreditSales = (bool) ($settings['has_credit_sales'] ?? false);
        $this->editBtHasTieredPricing = (bool) ($settings['has_tiered_pricing'] ?? false);

        $this->newPresetModuleName = '';
        $this->resetErrorBag();
        $this->showEditPresetModal = true;
    }

    public function closeEditPresetModal()
    {
        $this->showEditPresetModal = false;
        $this->editingBusinessTypeId = null;
        $this->resetErrorBag();
    }

    public function togglePresetModule(string $moduleName)
    {
        if (in_array($moduleName, $this->editBtPresetModules)) {
            $this->editBtPresetModules = array_values(array_diff($this->editBtPresetModules, [$moduleName]));
        } else {
            $this->editBtPresetModules[] = $moduleName;
        }
    }

    public function addPresetModule()
    {
        $mod = trim($this->newPresetModuleName);
        if (empty($mod)) {
            $this->addError('newPresetModuleName', 'Nama modul preset tidak boleh kosong.');
            return;
        }

        if (!in_array($mod, $this->editBtPresetModules)) {
            $this->editBtPresetModules[] = $mod;
        }

        $this->newPresetModuleName = '';
        $this->resetErrorBag('newPresetModuleName');
        $this->notify("Modul '{$mod}' berhasil ditambahkan ke preset!");
    }

    public function removePresetModule(string $moduleName)
    {
        $this->editBtPresetModules = array_values(array_diff($this->editBtPresetModules, [$moduleName]));
    }

    public function saveBusinessTypePreset()
    {
        $this->validate([
            'editBtName' => 'required|min:3|max:100',
            'editBtIcon' => 'required',
            'editBtDescription' => 'required|min:5',
        ], [
            'editBtName.required' => 'Nama template usaha wajib diisi.',
            'editBtName.min' => 'Nama template usaha minimal 3 karakter.',
            'editBtIcon.required' => 'Ikon template wajib dipilih.',
            'editBtDescription.required' => 'Deskripsi template wajib diisi.',
            'editBtDescription.min' => 'Deskripsi minimal 5 karakter.',
        ]);

        $bt = BusinessType::findOrFail($this->editingBusinessTypeId);

        $units = array_values(array_filter(array_map('trim', explode(',', $this->editBtDefaultUnits))));
        if (empty($units)) {
            $units = ['pcs'];
        }

        $newSettings = [
            'preset_modules' => array_values(array_unique($this->editBtPresetModules)),
            'default_units' => $units,
            'has_recipes' => (bool) $this->editBtHasRecipes,
            'has_tables' => (bool) $this->editBtHasTables,
            'has_modifiers' => (bool) $this->editBtHasModifiers,
            'has_barcode' => (bool) $this->editBtHasBarcode,
            'has_variants' => (bool) $this->editBtHasVariants,
            'has_commissions' => (bool) $this->editBtHasCommissions,
            'has_services' => (bool) $this->editBtHasServices,
            'has_order_status' => (bool) $this->editBtHasOrderStatus,
            'has_credit_sales' => (bool) $this->editBtHasCreditSales,
            'has_tiered_pricing' => (bool) $this->editBtHasTieredPricing,
        ];

        $bt->update([
            'name' => $this->editBtName,
            'icon' => $this->editBtIcon,
            'description' => $this->editBtDescription,
            'is_active' => (bool) $this->editBtIsActive,
            'default_settings' => $newSettings,
        ]);

        PlatformActivityLog::create([
            'action' => "Preset Template {$bt->name} berhasil diperbarui",
            'description' => "Modul: " . count($this->editBtPresetModules) . " aktif • Satuan: " . implode(', ', $units),
            'icon' => $bt->icon,
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showEditPresetModal = false;
        $this->notify("Preset template '{$bt->name}' berhasil disimpan!");
    }

    public function toggleBusinessTypeActive(int $id)
    {
        $bt = BusinessType::findOrFail($id);
        $newStatus = !$bt->is_active;
        $bt->update(['is_active' => $newStatus]);

        PlatformActivityLog::create([
            'action' => "Status Template {$bt->name} diubah",
            'description' => "Status kini: " . ($newStatus ? 'AKTIF' : 'NONAKTIF'),
            'icon' => $newStatus ? 'fa-check' : 'fa-ban',
            'icon_color' => $newStatus ? 'text-emerald-500 bg-emerald-50' : 'text-slate-400 bg-slate-100',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Template '{$bt->name}' kini " . ($newStatus ? 'AKTIF' : 'NONAKTIF'));
    }

    public function openCreateBusinessTypeModal()
    {
        $this->newBtName = '';
        $this->newBtIcon = 'fa-store';
        $this->newBtDescription = '';
        $this->newBtDefaultUnits = 'pcs, pack';
        $this->newBtPresetModules = ['Kasir Cepat & Struk WhatsApp', 'Manajemen Stok Standar'];
        $this->resetErrorBag();
        $this->showCreateBusinessTypeModal = true;
    }

    public function closeCreateBusinessTypeModal()
    {
        $this->showCreateBusinessTypeModal = false;
        $this->resetErrorBag();
    }

    public function togglePresetModuleInNew(string $moduleName)
    {
        if (in_array($moduleName, $this->newBtPresetModules)) {
            $this->newBtPresetModules = array_values(array_diff($this->newBtPresetModules, [$moduleName]));
        } else {
            $this->newBtPresetModules[] = $moduleName;
        }
    }

    public function createBusinessType()
    {
        $this->validate([
            'newBtName' => 'required|min:3|max:100',
            'newBtIcon' => 'required',
            'newBtDescription' => 'required|min:5',
        ], [
            'newBtName.required' => 'Nama template usaha wajib diisi.',
            'newBtIcon.required' => 'Ikon template wajib dipilih.',
            'newBtDescription.required' => 'Deskripsi template wajib diisi.',
        ]);

        $baseSlug = Str::slug($this->newBtName);
        $slug = $baseSlug;
        $counter = 1;
        while (BusinessType::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $units = array_values(array_filter(array_map('trim', explode(',', $this->newBtDefaultUnits))));
        if (empty($units)) {
            $units = ['pcs'];
        }

        $bt = BusinessType::create([
            'name' => $this->newBtName,
            'slug' => $slug,
            'icon' => $this->newBtIcon,
            'description' => $this->newBtDescription,
            'is_active' => true,
            'default_settings' => [
                'preset_modules' => array_values(array_unique($this->newBtPresetModules)),
                'default_units' => $units,
            ],
        ]);

        PlatformActivityLog::create([
            'action' => "Template Usaha '{$bt->name}' berhasil dibuat",
            'description' => "Slug: {$bt->slug} • " . count($this->newBtPresetModules) . " modul preset",
            'icon' => $bt->icon,
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showCreateBusinessTypeModal = false;
        $this->notify("Template usaha '{$bt->name}' berhasil ditambahkan!");
    }
}

