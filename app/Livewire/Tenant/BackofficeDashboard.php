<?php

namespace App\Livewire\Tenant;

use App\Livewire\Tenant\Concerns\HasCategories;
use App\Livewire\Tenant\Concerns\HasCustomers;
use App\Livewire\Tenant\Concerns\HasDiscounts;
use App\Livewire\Tenant\Concerns\HasExpenses;
use App\Livewire\Tenant\Concerns\HasFinanceExports;
use App\Livewire\Tenant\Concerns\HasOrdersAndVoid;
use App\Livewire\Tenant\Concerns\HasOutletsAndStaff;
use App\Livewire\Tenant\Concerns\HasProducts;
use App\Livewire\Tenant\Concerns\HasPurchases;
use App\Livewire\Tenant\Concerns\HasRecipes;
use App\Livewire\Tenant\Concerns\HasServices;
use App\Livewire\Tenant\Concerns\HasShifts;
use App\Livewire\Tenant\Concerns\HasStock;
use App\Livewire\Tenant\Concerns\HasStoreSettings;
use App\Livewire\Tenant\Concerns\HasTransactionExports;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Shift;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BackofficeDashboard extends Component
{
    use HasCategories;
    use HasCustomers;
    use HasDiscounts;
    use HasExpenses;
    use HasFinanceExports;
    use HasOrdersAndVoid;
    use HasOutletsAndStaff;
    use HasProducts;
    use HasPurchases;
    use HasRecipes;
    use HasServices;
    use HasShifts;
    use HasStock;
    use HasStoreSettings;
    use HasTransactionExports;
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'tab', keep: true)]
    public string $activeTab = 'dashboard';

    public ?int $tenantId = null;

    public ?int $selectedOutletId = null;

    // Search and filter states
    public string $searchProduct = '';

    public string $selectedCategoryFilter = 'all';

    public string $searchIngredient = '';

    // Toast
    public string $toastMessage = '';

    public string $toastType = 'success';

    public function mount()
    {
        // Check authenticated user
        $user = Auth::user();
        if ($user) {
            // Cashier / Staff are restricted from Back Office
            if (in_array($user->role, ['cashier', 'staff'])) {
                return redirect()->route('pos.terminal');
            }
            if ($user->tenant_id) {
                $this->tenantId = $user->tenant_id;
                $this->selectedOutletId = $user->outlet_id;
            }
        } else {
            $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
            $this->tenantId = $tenant?->id;
        }

        if (! $this->selectedOutletId && $this->tenantId) {
            $outlet = Outlet::where('tenant_id', $this->tenantId)->first();
            $this->selectedOutletId = $outlet?->id;
        }

        $tenantModel = Tenant::find($this->tenantId);
        if ($tenantModel && isset($tenantModel->settings['shift_policies'])) {
            $policies = $tenantModel->settings['shift_policies'];
            $this->policyDefaultCash = (float) ($policies['default_cash'] ?? 200000);
            $this->policyEnforceLock = (bool) ($policies['enforce_lock'] ?? true);
            $this->policyRequireReconciliation = (bool) ($policies['require_reconciliation'] ?? true);
        }

        $this->initStoreSettings();

        // Restore active tab from query string or session on refresh
        if (request()->has('tab') && ! empty(request()->query('tab'))) {
            $this->activeTab = (string) request()->query('tab');
            session(['tenant_active_tab' => $this->activeTab]);
        } elseif (session()->has('tenant_active_tab') && ! empty(session('tenant_active_tab'))) {
            $this->activeTab = (string) session('tenant_active_tab');
        }
    }

    public function updatedSearchProduct(): void
    {
        $this->resetPage('productsPage');
    }

    public function updatedSelectedCategoryFilter(): void
    {
        $this->resetPage('productsPage');
    }

    public function updatedSearchService(): void
    {
        $this->resetPage('servicesPage');
    }

    public function updatedSelectedServiceCategoryFilter(): void
    {
        $this->resetPage('servicesPage');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        session(['tenant_active_tab' => $tab]);
    }

    public function switchOutlet(int $outletId): void
    {
        $outlet = Outlet::where('tenant_id', $this->tenantId)->find($outletId);
        if ($outlet) {
            $this->selectedOutletId = $outlet->id;
            $this->showToast("Outlet aktif dialihkan ke '{$outlet->name}'.", 'info');
        }
    }

    public function switchTenant(int $tenantId): void
    {
        // Security check: Only Super Admin can switch between tenants in Back Office
        if (Auth::check() && ! Auth::user()->isSuperAdmin()) {
            $this->showToast('Akses ditolak: Anda hanya dapat mengelola bisnis Anda sendiri.', 'error');

            return;
        }

        $tenant = Tenant::with('businessType')->findOrFail($tenantId);
        $this->tenantId = $tenant->id;
        $firstOutlet = Outlet::where('tenant_id', $tenant->id)->first();
        $this->selectedOutletId = $firstOutlet?->id;
        $this->selectedCategoryFilter = 'all';
        $this->selectedServiceCategoryFilter = 'all';

        if ($tenant->isSalon() && $this->activeTab === 'recipes') {
            $this->activeTab = 'services';
            session(['tenant_active_tab' => 'services']);
        }

        $this->showToast("Mode Super Admin: Menampilkan tenant '{$tenant->name}' ({$tenant->businessType?->name}).", 'info');
    }

    public function showToast(string $message, string $type = 'success'): void
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function closeToast(): void
    {
        $this->toastMessage = '';
    }

    public function render()
    {
        $tenant = Tenant::with('businessType')->find($this->tenantId);
        $isSuperAdmin = Auth::check() && Auth::user()->isSuperAdmin();
        $allTenants = $isSuperAdmin ? Tenant::with('businessType')->get() : collect($tenant ? [$tenant] : []);
        $outlets = Outlet::withCount('users')->where('tenant_id', $this->tenantId)->get();
        $selectedOutlet = Outlet::find($this->selectedOutletId) ?? $outlets->first();

        // 1. Dashboard Stats
        $todayOrders = Order::where('tenant_id', $this->tenantId)
            ->where('status', 'completed')
            ->whereDate('created_at', now()->today())
            ->get();

        $todaySales = $todayOrders->sum('total_amount');
        $todayTxCount = $todayOrders->count();
        $avgTicket = $todayTxCount > 0 ? ($todaySales / $todayTxCount) : 0;

        // Estimated COGS (HPP) and Gross Margin
        $allCompletedOrders = Order::with('items.product')->where('tenant_id', $this->tenantId)->where('status', 'completed')->get();
        $monthGrossSales = $allCompletedOrders->sum('total_amount');
        $estimatedCogs = 0;
        foreach ($allCompletedOrders as $ord) {
            foreach ($ord->items as $it) {
                $cost = $it->product?->cost_price ?? ($it->unit_price * 0.35);
                $estimatedCogs += ($cost * $it->qty);
            }
        }
        $grossProfit = max(0, $monthGrossSales - $estimatedCogs);
        $grossMarginPct = $monthGrossSales > 0 ? round(($grossProfit / $monthGrossSales) * 100, 1) : 68.5;

        // Total OPEX this month
        $totalOpex = Expense::where('tenant_id', $this->tenantId)
            ->whereMonth('expense_date', now()->month)
            ->sum('amount');
        $netProfit = $grossProfit - $totalOpex;

        // Low stock items count
        $lowStockIngredients = Ingredient::where('tenant_id', $this->tenantId)
            ->whereColumn('current_stock', '<=', 'minimum_stock')
            ->get();

        // 2. Products & Services Data
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('type', '!=', 'service');
        }])->where('tenant_id', $this->tenantId)->orderBy('sort_order')->get();

        $serviceCategories = Category::withCount(['products' => function ($q) {
            $q->where('type', 'service');
        }])->where('tenant_id', $this->tenantId)->orderBy('sort_order')->get();

        $totalProductsCount = Product::where('tenant_id', $this->tenantId)->where('type', '!=', 'service')->count();
        $productsQuery = Product::with(['category', 'recipes.ingredient'])
            ->where('tenant_id', $this->tenantId)
            ->where('type', '!=', 'service');

        if ($this->selectedCategoryFilter !== 'all') {
            $productsQuery->where('category_id', $this->selectedCategoryFilter);
        }

        if (! empty($this->searchProduct)) {
            $productsQuery->where('name', 'like', '%'.$this->searchProduct.'%');
        }
        $products = $productsQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'productsPage');

        // 2b. Services Data (Khusus Salon & Barbershop)
        $servicesQuery = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', 'service');

        if ($this->selectedServiceCategoryFilter !== 'all') {
            $servicesQuery->where('category_id', $this->selectedServiceCategoryFilter);
        }

        if (! empty($this->searchService)) {
            $servicesQuery->where('name', 'like', '%'.$this->searchService.'%');
        }
        // Statistik & badge memakai seluruh layanan, tabel hanya menampilkan 10 per halaman
        $services = (clone $servicesQuery)->orderBy('id', 'desc')->get();
        $servicesTable = $servicesQuery->orderBy('id', 'desc')->paginate(10, ['*'], 'servicesPage');

        // 2c. Discounts & Promos Data
        $discountsQuery = Discount::where('tenant_id', $this->tenantId);

        if ($this->discountStatusFilter === 'active') {
            $discountsQuery->where('is_active', true);
        } elseif ($this->discountStatusFilter === 'inactive') {
            $discountsQuery->where('is_active', false);
        }

        if (! empty($this->searchDiscount)) {
            $discountsQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchDiscount.'%')
                    ->orWhere('code', 'like', '%'.$this->searchDiscount.'%');
            });
        }
        $discounts = $discountsQuery->orderBy('id', 'desc')->get();

        // 3. Ingredients Data
        $ingredientsQuery = Ingredient::with(['recipes.product'])->where('tenant_id', $this->tenantId);
        if (! empty($this->searchIngredient)) {
            $ingredientsQuery->where('name', 'like', '%'.$this->searchIngredient.'%');
        }
        $ingredients = $ingredientsQuery->get();

        // 4. Transactions Data & Analytics
        $txData = $this->getFilteredTransactionsData();
        $transactions = $txData['transactions'];
        $totalFilteredTransactionsCount = $txData['totalFilteredCount'];
        $viewingTransaction = $txData['viewingTransaction'];
        $serviceTransactionsCount = $txData['serviceCount'] ?? 0;
        $productTransactionsCount = $txData['productCount'] ?? 0;
        $allTransactionsCount = $txData['allCount'] ?? 0;
        $transactionAnalytics = $this->getTransactionAnalyticsData();

        // 5. Suppliers & Purchase Orders Data (Filtered)
        $poQuery = PurchaseOrder::with(['supplier', 'outlet', 'items'])
            ->where('tenant_id', $this->tenantId);

        if (! empty($this->searchPo)) {
            $poQuery->where(function ($q) {
                $q->where('po_number', 'like', '%'.$this->searchPo.'%')
                    ->orWhere('notes', 'like', '%'.$this->searchPo.'%')
                    ->orWhereHas('supplier', function ($sq) {
                        $sq->where('name', 'like', '%'.$this->searchPo.'%')
                            ->orWhere('contact_person', 'like', '%'.$this->searchPo.'%');
                    });
            });
        }

        if ($this->poStatusFilter !== 'all') {
            $poQuery->where('status', $this->poStatusFilter);
        }

        if ($this->poSupplierFilter !== 'all') {
            $poQuery->where('supplier_id', $this->poSupplierFilter);
        }

        if ($this->poDateFilter === 'today') {
            $poQuery->whereDate('order_date', now()->today());
        } elseif ($this->poDateFilter === '7_days') {
            $poQuery->where('order_date', '>=', now()->subDays(7)->toDateString());
        } elseif ($this->poDateFilter === '30_days') {
            $poQuery->where('order_date', '>=', now()->subDays(30)->toDateString());
        } elseif ($this->poDateFilter === 'this_month') {
            $poQuery->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
        } elseif ($this->poDateFilter === 'custom' && ! empty($this->poStartDate) && ! empty($this->poEndDate)) {
            $poQuery->whereBetween('order_date', [$this->poStartDate, $this->poEndDate]);
        }

        $purchaseOrders = $poQuery->orderBy('id', 'desc')->get();

        // PO Statistics
        $allTenantPos = PurchaseOrder::where('tenant_id', $this->tenantId)->get();
        $totalPoCount = $allTenantPos->count();
        $totalPoAmount = $allTenantPos->sum('total_amount');
        $orderedPoCount = $allTenantPos->where('status', 'ordered')->count();
        $receivedPoCount = $allTenantPos->where('status', 'received')->count();
        $cancelledPoCount = $allTenantPos->where('status', 'cancelled')->count();

        // Suppliers query with search & filter
        $supplierQuery = Supplier::withCount('purchaseOrders')->where('tenant_id', $this->tenantId);
        if (! empty($this->searchSupplier)) {
            $supplierQuery->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchSupplier.'%')
                    ->orWhere('contact_person', 'like', '%'.$this->searchSupplier.'%')
                    ->orWhere('phone', 'like', '%'.$this->searchSupplier.'%')
                    ->orWhere('email', 'like', '%'.$this->searchSupplier.'%')
                    ->orWhere('address', 'like', '%'.$this->searchSupplier.'%');
            });
        }
        if ($this->supplierStatusFilter === 'active') {
            $supplierQuery->where('is_active', true);
        } elseif ($this->supplierStatusFilter === 'inactive') {
            $supplierQuery->where('is_active', false);
        }
        $suppliers = $supplierQuery->orderBy('name')->get();
        $allSuppliersList = Supplier::where('tenant_id', $this->tenantId)->orderBy('name')->get();
        $viewingPo = $this->viewingPoId ? PurchaseOrder::with(['supplier', 'outlet', 'items'])->where('tenant_id', $this->tenantId)->find($this->viewingPoId) : null;

        // 6. Expenses Data
        $expenses = Expense::where('tenant_id', $this->tenantId)->orderBy('expense_date', 'desc')->get();

        // 7. Customers / CRM Data
        $crmData = $this->getFilteredCustomersData();
        $customers = $crmData['customers'];
        $totalCustomersCount = $crmData['totalCustomersCount'];
        $viewingCustomer = $crmData['viewingCustomer'];
        $crmStats = $crmData['crmStats'];

        // 8. Staff / Cashiers Data
        $cashiers = User::with('outlet')->where('tenant_id', $this->tenantId)->orderBy('role')->get();

        // 9. Stock Movements (Kartu Stok & Audit Opname) with Comprehensive Filters
        $stockMovementsQuery = StockMovement::with(['ingredient', 'product.category', 'outlet', 'user'])
            ->where('tenant_id', $this->tenantId);

        // Search filter: Item Name, SKU, or Reference Note
        if (! empty($this->stockSearch)) {
            $term = '%'.trim($this->stockSearch).'%';
            $stockMovementsQuery->where(function ($q) use ($term) {
                $q->where('reference_note', 'like', $term)
                    ->orWhereHas('product', function ($pq) use ($term) {
                        $pq->where('name', 'like', $term)->orWhere('sku', 'like', $term);
                    })
                    ->orWhereHas('ingredient', function ($iq) use ($term) {
                        $iq->where('name', 'like', $term)->orWhere('sku', 'like', $term);
                    });
            });
        }

        // Mutation Type Filter
        if ($this->stockTypeFilter !== 'all') {
            $stockMovementsQuery->where('type', $this->stockTypeFilter);
        }

        // Item Type Filter (Direct Product vs Raw Ingredient)
        if ($this->stockItemTypeFilter === 'product') {
            $stockMovementsQuery->whereNotNull('product_id');
        } elseif ($this->stockItemTypeFilter === 'ingredient') {
            $stockMovementsQuery->whereNotNull('ingredient_id');
        }

        // Date & Time Filter
        if ($this->stockDateFilter === 'today') {
            $stockMovementsQuery->whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()]);
        } elseif ($this->stockDateFilter === 'yesterday') {
            $stockMovementsQuery->whereBetween('created_at', [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()]);
        } elseif ($this->stockDateFilter === '7_days') {
            $stockMovementsQuery->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
        } elseif ($this->stockDateFilter === '30_days') {
            $stockMovementsQuery->where('created_at', '>=', Carbon::now()->subDays(30)->startOfDay());
        } elseif ($this->stockDateFilter === 'this_month') {
            $stockMovementsQuery->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()]);
        } elseif ($this->stockDateFilter === 'custom') {
            if (! empty($this->stockStartDate)) {
                $startTime = ! empty($this->stockStartTime) ? $this->stockStartTime.':00' : '00:00:00';
                $startDateTime = Carbon::parse($this->stockStartDate.' '.$startTime);
                $stockMovementsQuery->where('created_at', '>=', $startDateTime);
            }
            if (! empty($this->stockEndDate)) {
                $endTime = ! empty($this->stockEndTime) ? $this->stockEndTime.':59' : '23:59:59';
                $endDateTime = Carbon::parse($this->stockEndDate.' '.$endTime);
                $stockMovementsQuery->where('created_at', '<=', $endDateTime);
            }
        }

        $stockMovements = $stockMovementsQuery->orderBy('created_at', 'desc')->orderBy('id', 'desc')->get();

        // Stock Summary Stats
        $isSalon = $tenant && ($tenant->businessType?->slug === 'salon' || $tenant->business_type === 'salon');
        $directProducts = Product::with('category')->where('tenant_id', $this->tenantId)->where('type', 'item')->get();
        if ($directProducts->isEmpty()) {
            // Fallback: If no 'item' type exists, get all products
            $directProducts = Product::with('category')->where('tenant_id', $this->tenantId)->get();
        }

        $totalDirectProductsCount = $directProducts->count();
        $totalDirectStockQty = (float) $directProducts->sum('stock_quantity');
        $lowStockDirectProducts = $directProducts->filter(fn ($p) => (float) $p->stock_quantity <= (float) ($p->minimum_stock ?: 5))->count();
        $totalInventoryAssetValue = (float) $directProducts->sum(fn ($p) => (float) $p->stock_quantity * (float) $p->cost_price);

        // Filter direct products for the Stock Inventory Catalog view
        $filteredDirectProducts = $directProducts;
        if ($this->stockProductStatusFilter === 'safe') {
            $filteredDirectProducts = $filteredDirectProducts->filter(fn ($p) => (float) $p->stock_quantity > (float) ($p->minimum_stock ?: 5));
        } elseif ($this->stockProductStatusFilter === 'low') {
            $filteredDirectProducts = $filteredDirectProducts->filter(fn ($p) => (float) $p->stock_quantity > 0 && (float) $p->stock_quantity <= (float) ($p->minimum_stock ?: 5));
        } elseif ($this->stockProductStatusFilter === 'empty') {
            $filteredDirectProducts = $filteredDirectProducts->filter(fn ($p) => (float) $p->stock_quantity <= 0);
        }

        if (! empty($this->stockSearch) && $this->stockActiveSubTab === 'catalog') {
            $term = strtolower(trim($this->stockSearch));
            $filteredDirectProducts = $filteredDirectProducts->filter(fn ($p) => str_contains(strtolower($p->name), $term) ||
                str_contains(strtolower($p->sku ?? ''), $term) ||
                str_contains(strtolower($p->category?->name ?? ''), $term)
            );
        }

        $totalOpnameCountThisMonth = StockMovement::where('tenant_id', $this->tenantId)
            ->where('type', 'opname')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $lastOpnameMovement = StockMovement::where('tenant_id', $this->tenantId)
            ->where('type', 'opname')
            ->latest('created_at')
            ->first();

        $financeData = $this->getFinanceBreakdownData();

        return view('livewire.tenant.backoffice-dashboard', [
            'tenant' => $tenant,
            'allTenants' => $allTenants,
            'outlets' => $outlets,
            'selectedOutlet' => $selectedOutlet,
            'todaySales' => $todaySales,
            'todayTxCount' => $todayTxCount,
            'avgTicket' => $avgTicket,
            'monthGrossSales' => $monthGrossSales,
            'estimatedCogs' => $estimatedCogs,
            'grossProfit' => $grossProfit,
            'grossMarginPct' => $grossMarginPct,
            'totalOpex' => $totalOpex,
            'netProfit' => $netProfit,
            'lowStockIngredients' => $lowStockIngredients,
            'categories' => $categories,
            'serviceCategories' => $serviceCategories,
            'totalProductsCount' => $totalProductsCount,
            'products' => $products,
            'services' => $services,
            'servicesTable' => $servicesTable,
            'discounts' => $discounts,
            'ingredients' => $ingredients,
            'transactions' => $transactions,
            'totalFilteredTransactionsCount' => $totalFilteredTransactionsCount,
            'viewingTransaction' => $viewingTransaction,
            'serviceTransactionsCount' => $serviceTransactionsCount,
            'productTransactionsCount' => $productTransactionsCount,
            'allTransactionsCount' => $allTransactionsCount,
            'transactionAnalytics' => $transactionAnalytics,
            'suppliers' => $suppliers,
            'allSuppliersList' => $allSuppliersList,
            'purchaseOrders' => $purchaseOrders,
            'totalPoCount' => $totalPoCount,
            'totalPoAmount' => $totalPoAmount,
            'orderedPoCount' => $orderedPoCount,
            'receivedPoCount' => $receivedPoCount,
            'cancelledPoCount' => $cancelledPoCount,
            'viewingPo' => $viewingPo,
            'expenses' => $expenses,
            'customers' => $customers,
            'totalCustomersCount' => $totalCustomersCount,
            'viewingCustomer' => $viewingCustomer,
            'crmStats' => $crmStats,
            'cashiers' => $cashiers,
            'stockMovements' => $stockMovements,
            'isSalon' => $isSalon,
            'directProducts' => $directProducts,
            'filteredDirectProducts' => $filteredDirectProducts,
            'totalDirectProductsCount' => $totalDirectProductsCount,
            'totalDirectStockQty' => $totalDirectStockQty,
            'lowStockDirectProducts' => $lowStockDirectProducts,
            'totalInventoryAssetValue' => $totalInventoryAssetValue,
            'totalOpnameCountThisMonth' => $totalOpnameCountThisMonth,
            'lastOpnameMovement' => $lastOpnameMovement,
            'shiftTemplates' => $this->getShiftTemplates(),
            'shiftHistory' => Shift::with(['user', 'outlet'])->where('tenant_id', $this->tenantId)->latest()->take(10)->get(),
            'financeData' => $financeData,
            'expenseCategoryOptions' => $this->tenantId ? ExpenseCategory::namesForTenant($this->tenantId) : [],
        ])->layout('layouts.app', ['title' => 'Admin Back Office - '.($tenant->name ?? 'Lokara POS')]);
    }
}
