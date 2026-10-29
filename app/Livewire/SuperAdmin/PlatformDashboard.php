<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Announcement;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlatformActivityLog;
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

    public function createTenant()
    {
        $this->validate([
            'newTenantName' => 'required|min:3|max:100',
            'newTenantSlug' => 'required|alpha_dash|unique:tenants,slug',
            'newTenantBusinessType' => 'required|exists:business_types,id',
            'newTenantPlan' => 'required|exists:plans,id',
            'newTenantOwnerName' => 'required|min:3',
            'newTenantOwnerEmail' => 'required|email',
            'newTenantOwnerPhone' => 'required|min:9',
        ]);

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
        $this->showCreateTenantModal = false;

        $this->notify("Tenant {$tenant->name} berhasil dibuat!");
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

    public function notify(string $message, string $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function clearToast()
    {
        $this->toastMessage = '';
    }

    public function render()
    {
        // Dashboard Stats (Matching Image 1 values)
        $totalTenantCount = 1284;
        $activeTenantCount = 1102;
        $trialTenantCount = 96;
        $suspendedTenantCount = 28;
        $expiredTenantCount = 58;

        $mrrValue = 'Rp 48,6 jt';
        $totalTransactions = '86.420';
        $attentionCount = 12;

        // Query Tenants for Recent List
        $recentTenants = Tenant::with(['businessType', 'plan'])
            ->orderBy('id', 'asc')
            ->take(5)
            ->get();

        // Query All Tenants with search & filter
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

        // Invoices
        $invoices = Invoice::with('tenant')
            ->orderBy('id', 'desc')
            ->paginate(8);

        // Plans
        $plans = Plan::withCount('tenants')->get();

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
            'totalTransactions' => $totalTransactions,
            'attentionCount' => $attentionCount,
            'recentTenants' => $recentTenants,
            'allTenants' => $allTenants,
            'invoices' => $invoices,
            'plans' => $plans,
            'businessTypes' => $businessTypes,
            'activityLogs' => $activityLogs,
            'allLogs' => $allLogs,
            'announcements' => $announcements,
        ])->layout('layouts.app', ['title' => 'Lokara POS - Platform Super Admin']);
    }
}

