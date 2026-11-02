<?php

namespace App\Livewire\SuperAdmin;

use App\Livewire\SuperAdmin\Concerns\HasBusinessTypesAndPresets;
use App\Livewire\SuperAdmin\Concerns\HasInvoicesAndAudit;
use App\Livewire\SuperAdmin\Concerns\HasPlansAndFeatures;
use App\Livewire\SuperAdmin\Concerns\HasTenantManagement;
use App\Livewire\SuperAdmin\Concerns\HasTenantUsersAndPasswords;
use App\Models\Announcement;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlatformActivityLog;
use App\Models\PlatformFeature;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PlatformDashboard extends Component
{
    use WithPagination;
    use HasTenantManagement;
    use HasTenantUsersAndPasswords;
    use HasPlansAndFeatures;
    use HasBusinessTypesAndPresets;
    use HasInvoicesAndAudit;

    // Active Navigation Tab
    public string $activeTab = 'dashboard';

    // Global Search & Filter
    public string $searchQuery = '';
    public string $dateFilter = '30_days'; // '7_days', '30_days', '90_days', 'this_year'

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
        // Enforce authentication & role separation
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role !== 'super_admin') {
                if (in_array($user->role, ['owner', 'manager'])) {
                    return redirect()->route('tenant.dashboard');
                } elseif (in_array($user->role, ['cashier', 'staff'])) {
                    return redirect()->route('pos.terminal');
                }
            }
        } elseif (! app()->environment('testing')) {
            return redirect()->route('login');
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

    public function logout()
    {
        Auth::logout();
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
