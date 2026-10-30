<?php

namespace App\Livewire\Tenant;

use App\Livewire\Tenant\Concerns\HasCategories;
use App\Livewire\Tenant\Concerns\HasDiscounts;
use App\Livewire\Tenant\Concerns\HasExpenses;
use App\Livewire\Tenant\Concerns\HasOrdersAndVoid;
use App\Livewire\Tenant\Concerns\HasOutletsAndStaff;
use App\Livewire\Tenant\Concerns\HasProducts;
use App\Livewire\Tenant\Concerns\HasPurchases;
use App\Livewire\Tenant\Concerns\HasRecipes;
use App\Livewire\Tenant\Concerns\HasServices;
use App\Livewire\Tenant\Concerns\HasShifts;
use App\Livewire\Tenant\Concerns\HasStock;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Expense;
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
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class BackofficeDashboard extends Component
{
    use WithFileUploads;
    use HasProducts;
    use HasCategories;
    use HasServices;
    use HasDiscounts;
    use HasRecipes;
    use HasStock;
    use HasPurchases;
    use HasExpenses;
    use HasOrdersAndVoid;
    use HasOutletsAndStaff;
    use HasShifts;

    public string $activeTab = 'dashboard';
    public ?int $tenantId = null;
    public ?int $selectedOutletId = null;

    // Search and filter states
    public string $searchProduct = '';
    public string $selectedCategoryFilter = 'all';
    public string $searchIngredient = '';
    public string $searchTransaction = '';
    public string $transactionStatusFilter = 'all';

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

        if (!$this->selectedOutletId && $this->tenantId) {
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
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
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
        if (Auth::check() && !Auth::user()->isSuperAdmin()) {
            $this->showToast('Akses ditolak: Anda hanya dapat mengelola bisnis Anda sendiri.', 'error');
            return;
        }

        $tenant = Tenant::with('businessType')->findOrFail($tenantId);
        $this->tenantId = $tenant->id;
        $firstOutlet = Outlet::where('tenant_id', $tenant->id)->first();
        $this->selectedOutletId = $firstOutlet?->id;
        $this->selectedCategoryFilter = 'all';
        $this->selectedServiceCategoryFilter = 'all';

        if ($tenant->isSalon() && in_array($this->activeTab, ['recipes', 'stock'])) {
            $this->activeTab = 'services';
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

        if (!empty($this->searchProduct)) {
            $productsQuery->where('name', 'like', '%' . $this->searchProduct . '%');
        }
        $products = $productsQuery->orderBy('id', 'desc')->get();

        // 2b. Services Data (Khusus Salon & Barbershop)
        $servicesQuery = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', 'service');

        if ($this->selectedServiceCategoryFilter !== 'all') {
            $servicesQuery->where('category_id', $this->selectedServiceCategoryFilter);
        }

        if (!empty($this->searchService)) {
            $servicesQuery->where('name', 'like', '%' . $this->searchService . '%');
        }
        $services = $servicesQuery->orderBy('id', 'desc')->get();

        // 2c. Discounts & Promos Data
        $discountsQuery = Discount::where('tenant_id', $this->tenantId);

        if ($this->discountStatusFilter === 'active') {
            $discountsQuery->where('is_active', true);
        } elseif ($this->discountStatusFilter === 'inactive') {
            $discountsQuery->where('is_active', false);
        }

        if (!empty($this->searchDiscount)) {
            $discountsQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchDiscount . '%')
                    ->orWhere('code', 'like', '%' . $this->searchDiscount . '%');
            });
        }
        $discounts = $discountsQuery->orderBy('id', 'desc')->get();

        // 3. Ingredients Data
        $ingredientsQuery = Ingredient::with(['recipes.product'])->where('tenant_id', $this->tenantId);
        if (!empty($this->searchIngredient)) {
            $ingredientsQuery->where('name', 'like', '%' . $this->searchIngredient . '%');
        }
        $ingredients = $ingredientsQuery->get();

        // 4. Transactions Data
        $transactionsQuery = Order::with(['items', 'payments'])->where('tenant_id', $this->tenantId);
        if ($this->transactionStatusFilter !== 'all') {
            $transactionsQuery->where('status', $this->transactionStatusFilter);
        }
        if (!empty($this->searchTransaction)) {
            $transactionsQuery->where('order_number', 'like', '%' . $this->searchTransaction . '%');
        }
        $transactions = $transactionsQuery->orderBy('id', 'desc')->take(20)->get();

        // 5. Suppliers & Purchase Orders Data (Filtered)
        $poQuery = PurchaseOrder::with(['supplier', 'outlet', 'items'])
            ->where('tenant_id', $this->tenantId);

        if (!empty($this->searchPo)) {
            $poQuery->where(function ($q) {
                $q->where('po_number', 'like', '%' . $this->searchPo . '%')
                  ->orWhere('notes', 'like', '%' . $this->searchPo . '%')
                  ->orWhereHas('supplier', function ($sq) {
                      $sq->where('name', 'like', '%' . $this->searchPo . '%')
                        ->orWhere('contact_person', 'like', '%' . $this->searchPo . '%');
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
        } elseif ($this->poDateFilter === 'custom' && !empty($this->poStartDate) && !empty($this->poEndDate)) {
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
        if (!empty($this->searchSupplier)) {
            $supplierQuery->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('phone', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('email', 'like', '%' . $this->searchSupplier . '%')
                  ->orWhere('address', 'like', '%' . $this->searchSupplier . '%');
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
        $customers = Customer::where('tenant_id', $this->tenantId)->orderBy('points', 'desc')->get();

        // 8. Staff / Cashiers Data
        $cashiers = User::with('outlet')->where('tenant_id', $this->tenantId)->orderBy('role')->get();

        // 9. Stock Movements (Kartu Stok)
        $stockMovements = StockMovement::with(['ingredient', 'outlet'])->where('tenant_id', $this->tenantId)->orderBy('id', 'desc')->take(15)->get();

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
            'discounts' => $discounts,
            'ingredients' => $ingredients,
            'transactions' => $transactions,
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
            'cashiers' => $cashiers,
            'stockMovements' => $stockMovements,
            'shiftTemplates' => $this->getShiftTemplates(),
            'shiftHistory' => Shift::with(['user', 'outlet'])->where('tenant_id', $this->tenantId)->latest()->take(10)->get(),
        ])->layout('layouts.app', ['title' => 'Admin Back Office - ' . ($tenant->name ?? 'Lokara POS')]);
    }
}
