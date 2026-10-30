<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Announcement;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlatformActivityLog;
use App\Models\PlatformFeature;
use App\Models\PlatformSetting;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class PlatformDashboard extends Component
{
    use WithPagination;

    // Active Navigation Tab
    public string $activeTab = 'dashboard';

    // Global Search & Filter
    public string $searchQuery = '';
    public string $dateFilter = '30_days'; // '7_days', '30_days', '90_days', 'this_year'
    public string $tenantStatusFilter = 'all';
    public string $invoiceStatusFilter = 'all';

    // Modal State
    public bool $showCreateTenantModal = false;
    public string $newTenantName = '';
    public string $newTenantSlug = '';
    public ?int $newTenantBusinessType = null;
    public ?int $newTenantPlan = null;
    public string $newTenantOwnerName = '';
    public string $newTenantOwnerEmail = '';
    public string $newTenantOwnerPhone = '';
    public string $newTenantCity = '';

    // Reset Password Modal State
    public bool $showResetPasswordModal = false;
    public ?int $resetUserId = null;
    public string $resetUserName = '';
    public string $resetUserEmail = '';
    public string $resetTenantName = '';
    public string $resetUserRole = 'Owner';
    public string $newPassword = '';
    public string $newPassword_confirmation = '';
    public bool $showPasswordText = false;

    // Create User Modal State
    public bool $showCreateUserModal = false;
    public string $createUserName = '';
    public string $createUserEmail = '';
    public ?int $createUserTenantId = null;
    public string $createUserRole = 'cashier';
    public string $createUserPassword = '';
    public string $createUserPassword_confirmation = '';

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

    // Notification toast
    public string $toastMessage = '';
    public string $toastType = 'success';

    // Mobile Sidebar toggle
    public bool $sidebarOpen = false;

    protected $queryString = [
        'activeTab' => ['except' => 'dashboard'],
    ];

    public function mount()
    {
        // Enforce role separation for authenticated users
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();
            if ($user->role !== 'super_admin') {
                if (in_array($user->role, ['owner', 'manager'])) {
                    return redirect()->route('tenant.dashboard');
                } elseif (in_array($user->role, ['cashier', 'staff'])) {
                    return redirect()->route('pos.terminal');
                }
            }
        }

        // Default business type & plan for create modal
        $defaultBt = BusinessType::first();
        if ($defaultBt) {
            $this->newTenantBusinessType = $defaultBt->id;
        }

        $defaultPlan = Plan::where('slug', 'pro')->first() ?? Plan::first();
        if ($defaultPlan) {
            $this->newTenantPlan = $defaultPlan->id;
        }
    }

    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
        $this->sidebarOpen = false;
        $this->resetPage();
    }

    public function toggleSidebar()
    {
        $this->sidebarOpen = !$this->sidebarOpen;
    }

    public function setDateFilter(string $filter)
    {
        $this->dateFilter = $filter;
        $this->dispatch('dateFilterChanged', filter: $filter);
    }

    public function updatedNewTenantName($value)
    {
        $this->newTenantSlug = \Illuminate\Support\Str::slug($value);
    }

    public function openCreateTenantModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->reset(['newTenantName', 'newTenantSlug', 'newTenantOwnerName', 'newTenantOwnerEmail', 'newTenantOwnerPhone', 'newTenantCity']);

        if (!$this->newTenantBusinessType) {
            $this->newTenantBusinessType = BusinessType::first()?->id;
        }
        if (!$this->newTenantPlan) {
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
        } catch (\Illuminate\Validation\ValidationException $e) {
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
            'description' => "Paket: " . ($tenant->plan ? $tenant->plan->name : 'Basic') . " • Owner: {$tenant->owner_name}",
            'icon' => 'fa-plus',
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->reset(['newTenantName', 'newTenantSlug', 'newTenantOwnerName', 'newTenantOwnerEmail', 'newTenantOwnerPhone', 'newTenantCity']);
        $this->resetErrorBag();
        $this->showCreateTenantModal = false;

        $this->notify("Tenant '{$tenant->name}' berhasil didaftarkan ke platform!");
    }

    public function toggleTenantStatus(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        PlatformActivityLog::create([
            'action' => "Status Tenant {$tenant->name} diubah",
            'description' => "Status baru: " . strtoupper($newStatus),
            'icon' => $newStatus === 'active' ? 'fa-check' : 'fa-ban',
            'icon_color' => $newStatus === 'active' ? 'text-emerald-500 bg-emerald-50' : 'text-rose-500 bg-rose-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Status {$tenant->name} kini menjadi " . strtoupper($newStatus));
    }

    public function impersonateTenant(int $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $this->notify("Sesi Impersonasi aktif: Anda kini dapat mengakses backoffice {$tenant->name}");
    }

    public function markInvoicePaid(int $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'payment_method' => 'Manual Transfer (Admin Verification)',
        ]);

        PlatformActivityLog::create([
            'action' => "Invoice {$invoice->invoice_number} ditandai Lunas",
            'description' => "Tenant: {$invoice->tenant->name} • Rp " . number_format($invoice->amount, 0, ',', '.'),
            'icon' => 'fa-receipt',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->notify("Invoice {$invoice->invoice_number} berhasil ditandai LUNAS.");
    }

    // --- RESET & BUAT PASSWORD USER TENANT ---
    public function openResetPasswordModal(string $email, string $name = '', string $tenantName = '', string $role = 'Owner', ?int $userId = null)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->resetUserId = $userId;
        $this->resetUserEmail = $email;
        $this->resetUserName = $name ?: 'Pengguna Tenant';
        $this->resetTenantName = $tenantName ?: 'Tenant Lokara';
        $this->resetUserRole = $role ?: 'Owner';
        $this->newPassword = '';
        $this->newPassword_confirmation = '';
        $this->showPasswordText = false;
        $this->showResetPasswordModal = true;
    }

    public function closeResetPasswordModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showResetPasswordModal = false;
    }

    public function generateRandomPassword()
    {
        $randomPass = 'Lokara#' . rand(1000, 9999);
        $this->newPassword = $randomPass;
        $this->newPassword_confirmation = $randomPass;
        $this->showPasswordText = true;
        $this->notify("Password acak dibuat: {$randomPass}", 'info');
    }

    public function updatePassword()
    {
        try {
            $this->validate([
                'newPassword' => 'required|min:6|confirmed',
            ], [
                'newPassword.required' => 'Password baru wajib diisi.',
                'newPassword.min' => 'Password minimal 6 karakter demi keamanan akun.',
                'newPassword.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notify('Gagal: Periksa kembali password dan konfirmasi!', 'error');
            throw $e;
        }

        // Find user by email or ID
        $user = null;
        if ($this->resetUserId) {
            $user = User::find($this->resetUserId);
        }
        if (!$user) {
            $user = User::where('email', $this->resetUserEmail)->first();
        }

        if (!$user) {
            $tenant = Tenant::where('owner_email', $this->resetUserEmail)->first();
            $user = User::create([
                'name' => $this->resetUserName,
                'email' => $this->resetUserEmail,
                'password' => \Illuminate\Support\Facades\Hash::make($this->newPassword),
                'role' => strtolower($this->resetUserRole) === 'owner' ? 'owner' : 'cashier',
                'tenant_id' => $tenant?->id,
                'is_active' => true,
            ]);
        } else {
            $user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($this->newPassword),
            ]);
        }

        PlatformActivityLog::create([
            'action' => "Password akun {$this->resetUserEmail} direset",
            'description' => "Password baru telah diperbarui dan dienkripsi oleh Super Admin",
            'icon' => 'fa-key',
            'icon_color' => 'text-amber-500 bg-amber-50',
            'badge_text' => 'Baru saja',
        ]);

        $savedEmail = $this->resetUserEmail;
        $this->showResetPasswordModal = false;
        $this->reset(['newPassword', 'newPassword_confirmation']);
        $this->resetErrorBag();

        $this->notify("Password untuk akun '{$savedEmail}' berhasil direset!");
    }

    // --- TAMBAH / BUAT USER TENANT BARU ---
    public function openCreateUserModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->createUserName = '';
        $this->createUserEmail = '';
        $this->createUserTenantId = Tenant::first()?->id;
        $this->createUserRole = 'cashier';
        $this->createUserPassword = '';
        $this->createUserPassword_confirmation = '';
        $this->showCreateUserModal = true;
    }

    public function closeCreateUserModal()
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->showCreateUserModal = false;
    }

    public function generateCreateUserPassword()
    {
        $randomPass = 'Lokara#' . rand(1000, 9999);
        $this->createUserPassword = $randomPass;
        $this->createUserPassword_confirmation = $randomPass;
        $this->notify("Password acak dibuat: {$randomPass}", 'info');
    }

    public function createUser()
    {
        try {
            $this->validate([
                'createUserName' => 'required|min:3',
                'createUserEmail' => 'required|email|unique:users,email',
                'createUserTenantId' => 'required|exists:tenants,id',
                'createUserRole' => 'required|in:owner,manager,cashier',
                'createUserPassword' => 'required|min:6|confirmed',
            ], [
                'createUserName.required' => 'Nama lengkap pengguna wajib diisi.',
                'createUserName.min' => 'Nama pengguna minimal 3 karakter.',
                'createUserEmail.required' => 'Alamat email wajib diisi.',
                'createUserEmail.email' => 'Format email tidak valid.',
                'createUserEmail.unique' => 'Email ini sudah terdaftar sebagai pengguna lain.',
                'createUserTenantId.required' => 'Pilih salah satu tenant bisnis.',
                'createUserRole.required' => 'Pilih salah satu peran (role).',
                'createUserPassword.required' => 'Password wajib diisi.',
                'createUserPassword.min' => 'Password minimal 6 karakter.',
                'createUserPassword.confirmed' => 'Konfirmasi password tidak cocok.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notify('Gagal: Periksa kembali kolom pembuatan akun!', 'error');
            throw $e;
        }

        $tenant = Tenant::find($this->createUserTenantId);

        $user = User::create([
            'name' => $this->createUserName,
            'email' => $this->createUserEmail,
            'password' => \Illuminate\Support\Facades\Hash::make($this->createUserPassword),
            'role' => $this->createUserRole,
            'tenant_id' => $tenant?->id,
            'is_active' => true,
        ]);

        PlatformActivityLog::create([
            'action' => "Akun baru {$user->name} dibuat",
            'description' => "Role: " . strtoupper($user->role) . " • Tenant: " . ($tenant->name ?? '-'),
            'icon' => 'fa-user-plus',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => 'Baru saja',
        ]);

        $this->showCreateUserModal = false;
        $this->reset(['createUserName', 'createUserEmail', 'createUserPassword', 'createUserPassword_confirmation']);
        $this->resetErrorBag();

        $this->notify("Akun pengguna '{$user->name}' ({$user->email}) berhasil dibuat!");
    }

    public function logout()
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('login');
    }

    public function notify(string $message, string $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function clearToast()
    {
        $this->toastMessage = '';
    }

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
    // TAMBAH PAKET BARU METHODS (BONUS)
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
            'newPlanPriceYearly.required' => 'Tarif tahunan wajib diisi.',
            'newPlanMaxOutlets.required' => 'Batas maksimal outlet wajib diisi.',
            'newPlanMaxUsers.required' => 'Batas maksimal karyawan wajib diisi.',
            'newPlanMaxProducts.required' => 'Batas maksimal master produk wajib diisi.',
        ]);

        $baseSlug = \Illuminate\Support\Str::slug($this->newPlanName);
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

        $baseSlug = \Illuminate\Support\Str::slug($this->newBtName);
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

    public function render()
    {
        $now = Carbon::now();

        // 1. Dynamic Date Filter Range
        $dateStart = match ($this->dateFilter) {
            '7_days' => $now->copy()->subDays(7)->startOfDay(),
            '30_days' => $now->copy()->subDays(30)->startOfDay(),
            '90_days' => $now->copy()->subDays(90)->startOfDay(),
            'this_year' => $now->copy()->startOfYear(),
            default => $now->copy()->subDays(30)->startOfDay(),
        };

        $dateFilterLabel = match ($this->dateFilter) {
            '7_days' => '7 hari terakhir',
            '30_days' => '30 hari terakhir',
            '90_days' => '90 hari terakhir',
            'this_year' => 'Tahun ' . $now->year,
            default => '30 hari terakhir',
        };

        // 2. Dynamic Real Tenant Status Counts
        $totalTenantCount = Tenant::count();
        $activeTenantCount = Tenant::where('status', 'active')->count();
        $trialTenantCount = Tenant::where('status', 'trial')->count();
        $suspendedTenantCount = Tenant::where('status', 'suspended')->count();
        $expiredTenantCount = Tenant::where('status', 'expired')->count();

        // 3. Dynamic MRR Calculation (Active Tenants * Subscription Monthly Price)
        $activeTenantsWithPlan = Tenant::where('status', 'active')->with('plan')->get();
        $rawMrr = $activeTenantsWithPlan->sum(function ($t) {
            return (float) ($t->plan?->price_monthly ?? 0);
        });

        if ($rawMrr >= 1000000000) {
            $mrrValue = 'Rp ' . number_format($rawMrr / 1000000000, 1, ',', '.') . ' M';
        } elseif ($rawMrr >= 1000000) {
            $mrrValue = 'Rp ' . number_format($rawMrr / 1000000, 1, ',', '.') . ' jt';
        } else {
            $mrrValue = 'Rp ' . number_format($rawMrr, 0, ',', '.');
        }

        // 4. Dynamic Platform Transactions based on dateFilter
        $orderQuery = Order::where('created_at', '>=', $dateStart);
        $totalTransactionsCount = $orderQuery->count();
        $totalTransactionsAmount = $orderQuery->sum('total_amount');

        $paidInvoicesAmount = Invoice::where('status', 'paid')
            ->where('created_at', '>=', $dateStart)
            ->sum('amount');

        $totalTransactions = number_format($totalTransactionsCount, 0, ',', '.');
        $totalVolumeFormatted = 'Rp ' . number_format($totalTransactionsAmount + $paidInvoicesAmount, 0, ',', '.');

        // 5. Dynamic Attention Count (Expiring trials, expiring subs, overdue invoices, expired tenants)
        $expiringTrials = Tenant::where('status', 'trial')
            ->where(function ($q) use ($now) {
                $q->whereNull('trial_ends_at')
                  ->orWhere('trial_ends_at', '<=', $now->copy()->addDays(7));
            })->count();

        $expiringSubscriptions = Tenant::where('status', 'active')
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', $now->copy()->addDays(14))
            ->count();

        $overdueInvoices = Invoice::where('status', 'overdue')
            ->orWhere(function ($q) use ($now) {
                $q->where('status', 'unpaid')->where('due_date', '<', $now->toDateString());
            })->count();

        $attentionCount = $expiringTrials + $expiringSubscriptions + $expiredTenantCount + $overdueInvoices;

        // 6. Donut Chart Data Array
        $donutChartData = [
            $activeTenantCount,
            $trialTenantCount,
            $suspendedTenantCount,
            $expiredTenantCount,
        ];

        // 7. Dynamic Growth Chart Data (Last 6 Months)
        $growthLabels = [];
        $growthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = $now->copy()->subMonths($i);
            $growthLabels[] = $monthDate->translatedFormat('M');
            $growthData[] = Tenant::where('created_at', '<=', $monthDate->endOfMonth())->count();
        }

        // 8. Query Tenants for Recent List
        $recentTenants = Tenant::with(['businessType', 'plan'])
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        // 9. Query All Tenants with search & filter
        $allTenantsQuery = Tenant::with(['businessType', 'plan'])
            ->when($this->searchQuery, function ($q) {
                $q->where('name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('owner_name', 'like', "%{$this->searchQuery}%")
                  ->orWhere('city', 'like', "%{$this->searchQuery}%");
            })
            ->when($this->tenantStatusFilter !== 'all', function ($q) {
                $q->where('status', $this->tenantStatusFilter);
            })
            ->orderBy('id', 'asc');

        $allTenants = $allTenantsQuery->paginate(8);

        // 10. Invoices with status filter
        $invoiceTotalCount = Invoice::count();
        $invoicePaidCount = Invoice::where('status', 'paid')->count();
        $invoiceUnpaidCount = Invoice::where('status', 'unpaid')->count();
        $invoiceOverdueCount = Invoice::where('status', 'overdue')->count();

        $invoices = Invoice::with('tenant')
            ->when($this->invoiceStatusFilter !== 'all', function ($q) {
                $q->where('status', $this->invoiceStatusFilter);
            })
            ->orderBy('id', 'desc')
            ->paginate(8);

        // Plans
        $plans = Plan::withCount('tenants')->get();

        // Platform Features
        $allPlatformFeatures = PlatformFeature::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        // Map which plans use each feature
        $featurePlanUsage = [];
        foreach ($allPlatformFeatures as $feat) {
            $featurePlanUsage[$feat->name] = $plans->filter(function ($p) use ($feat) {
                return is_array($p->features) && in_array($feat->name, $p->features);
            })->pluck('name')->toArray();
        }

        // Business Types
        $businessTypes = BusinessType::withCount('tenants')->get();

        // Activity Logs
        $activityLogs = PlatformActivityLog::orderBy('id', 'asc')->take(5)->get();
        $allLogs = PlatformActivityLog::orderBy('id', 'desc')->paginate(10);

        // Announcements
        $announcements = Announcement::orderBy('id', 'desc')->get();

        return view('livewire.super-admin.platform-dashboard', [
            'totalTenantCount' => $totalTenantCount,
            'activeTenantCount' => $activeTenantCount,
            'trialTenantCount' => $trialTenantCount,
            'suspendedTenantCount' => $suspendedTenantCount,
            'expiredTenantCount' => $expiredTenantCount,
            'mrrValue' => $mrrValue,
            'rawMrr' => $rawMrr,
            'totalTransactions' => $totalTransactions,
            'totalTransactionsCount' => $totalTransactionsCount,
            'totalTransactionsAmount' => $totalTransactionsAmount,
            'totalVolumeFormatted' => $totalVolumeFormatted,
            'dateFilterLabel' => $dateFilterLabel,
            'attentionCount' => $attentionCount,
            'donutChartData' => $donutChartData,
            'growthLabels' => $growthLabels,
            'growthData' => $growthData,
            'recentTenants' => $recentTenants,
            'allTenants' => $allTenants,
            'invoices' => $invoices,
            'invoiceTotalCount' => $invoiceTotalCount,
            'invoicePaidCount' => $invoicePaidCount,
            'invoiceUnpaidCount' => $invoiceUnpaidCount,
            'invoiceOverdueCount' => $invoiceOverdueCount,
            'plans' => $plans,
            'allPlatformFeatures' => $allPlatformFeatures,
            'featurePlanUsage' => $featurePlanUsage,
            'businessTypes' => $businessTypes,
            'activityLogs' => $activityLogs,
            'allLogs' => $allLogs,
            'announcements' => $announcements,
        ])->layout('layouts.app', ['title' => 'Lokara POS - Platform Super Admin']);
    }
}

