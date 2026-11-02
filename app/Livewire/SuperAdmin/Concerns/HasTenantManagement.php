<?php

namespace App\Livewire\SuperAdmin\Concerns;

use App\Models\BusinessType;
use App\Models\Plan;
use App\Models\PlatformActivityLog;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait HasTenantManagement
{
    // Filter
    public string $tenantStatusFilter = 'all';

    // Create Tenant Modal State
    public bool $showCreateTenantModal = false;

    public string $newTenantName = '';

    public string $newTenantSlug = '';

    public ?int $newTenantBusinessType = null;

    public ?int $newTenantPlan = null;

    public string $newTenantOwnerName = '';

    public string $newTenantOwnerEmail = '';

    public string $newTenantOwnerPhone = '';

    public string $newTenantCity = '';

    public function updatedNewTenantName($value)
    {
        $this->newTenantSlug = Str::slug($value);
    }

    public function openCreateTenantModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['newTenantName', 'newTenantSlug', 'newTenantOwnerName', 'newTenantOwnerEmail', 'newTenantOwnerPhone', 'newTenantCity']);

        if (! $this->newTenantBusinessType) {
            $this->newTenantBusinessType = BusinessType::first()?->id;
        }
        if (! $this->newTenantPlan) {
            $this->newTenantPlan = Plan::where('slug', 'pro')->first()?->id ?? Plan::first()?->id;
        }

        $this->showCreateTenantModal = true;
    }

    public function closeCreateTenantModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showCreateTenantModal = false;
    }

    protected function rules(): array
    {
        return [
            'newTenantName' => 'required|min:3|max:100',
            'newTenantSlug' => 'required|alpha_dash|unique:tenants,slug',
            'newTenantCity' => 'required|min:2|max:50',
            'newTenantBusinessType' => 'required|exists:business_types,id',
            'newTenantPlan' => 'required|exists:plans,id',
            'newTenantOwnerName' => 'required|min:3|max:100',
            'newTenantOwnerEmail' => 'required|email|max:100',
            'newTenantOwnerPhone' => 'required|min:8|max:20',
        ];
    }

    protected function messages(): array
    {
        return [
            'newTenantName.required' => 'Nama bisnis / toko wajib diisi.',
            'newTenantName.min' => 'Nama bisnis minimal 3 karakter.',
            'newTenantName.max' => 'Nama bisnis maksimal 100 karakter.',
            'newTenantSlug.required' => 'Subdomain slug wajib diisi.',
            'newTenantSlug.alpha_dash' => 'Subdomain hanya boleh berupa huruf, angka, dan tanda hubung (-).',
            'newTenantSlug.unique' => 'Subdomain slug ini sudah terdaftar oleh tenant lain.',
            'newTenantCity.required' => 'Kota asal bisnis wajib diisi.',
            'newTenantCity.min' => 'Kota asal minimal 2 karakter.',
            'newTenantBusinessType.required' => 'Jenis usaha wajib dipilih.',
            'newTenantBusinessType.exists' => 'Jenis usaha yang dipilih tidak valid.',
            'newTenantPlan.required' => 'Pilihan paket wajib dipilih.',
            'newTenantPlan.exists' => 'Pilihan paket yang dipilih tidak valid.',
            'newTenantOwnerName.required' => 'Nama pemilik (owner) wajib diisi.',
            'newTenantOwnerName.min' => 'Nama pemilik minimal 3 karakter.',
            'newTenantOwnerEmail.required' => 'Alamat email pemilik wajib diisi.',
            'newTenantOwnerEmail.email' => 'Format email tidak valid (contoh: owner@bisnis.id).',
            'newTenantOwnerPhone.required' => 'Nomor WhatsApp pemilik wajib diisi.',
            'newTenantOwnerPhone.min' => 'Nomor WhatsApp minimal 8 digit angka.',
        ];
    }

    public function createTenant()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->notify('Gagal mendaftar: Mohon lengkapi semua kolom yang wajib diisi!', 'error');
            throw $e;
        }

        $bt = BusinessType::find($this->newTenantBusinessType);

        $tenant = Tenant::create([
            'name' => $this->newTenantName,
            'slug' => $this->newTenantSlug,
            'business_type_id' => $this->newTenantBusinessType,
            'plan_id' => $this->newTenantPlan,
            'owner_name' => $this->newTenantOwnerName,
            'owner_email' => $this->newTenantOwnerEmail,
            'owner_phone' => $this->newTenantOwnerPhone,
            'status' => 'active',
            'city' => $this->newTenantCity ?: 'Indonesia',
            'icon' => $bt ? $bt->icon : 'fa-store',
            'subscription_ends_at' => Carbon::now()->addMonth(),
        ]);

        PlatformActivityLog::create([
            'action' => "Tenant {$tenant->name} berhasil didaftarkan",
            'description' => 'Paket: '.($tenant->plan ? $tenant->plan->name : 'Basic')." • Owner: {$tenant->owner_name}",
            'icon' => 'fa-plus',
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->reset(['newTenantName', 'newTenantSlug', 'newTenantOwnerName', 'newTenantOwnerEmail', 'newTenantOwnerPhone', 'newTenantCity']);
        $this->resetErrorBag();
        $this->showCreateTenantModal = false;

        $this->notify("Tenant '{$tenant->name}' berhasil didaftarkan ke platform!");
    }

    public function deleteTenant(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($tenant->status !== 'suspended') {
            $this->notify("Tenant '{$tenant->name}' hanya dapat dihapus setelah berstatus Suspended.", 'error');

            return;
        }

        $name = $tenant->name;
        $tenant->delete();

        PlatformActivityLog::create([
            'action' => "Tenant {$name} dihapus permanen",
            'description' => 'Seluruh data tenant ikut terhapus',
            'icon' => 'fa-trash',
            'icon_color' => 'text-rose-500 bg-rose-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Tenant '{$name}' berhasil dihapus permanen.");
    }

    public function toggleTenantStatus(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        PlatformActivityLog::create([
            'action' => "Status Tenant {$tenant->name} diubah",
            'description' => 'Status baru: '.strtoupper($newStatus),
            'icon' => $newStatus === 'active' ? 'fa-check' : 'fa-ban',
            'icon_color' => $newStatus === 'active' ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Status {$tenant->name} kini menjadi ".strtoupper($newStatus));
    }

    public function impersonateTenant(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->notify("Sesi Impersonasi aktif: Anda kini dapat mengakses backoffice {$tenant->name}");
    }
}
