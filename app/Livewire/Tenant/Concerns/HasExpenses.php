<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

trait HasExpenses
{
    public bool $showExpenseModal = false;

    public ?int $editingExpenseId = null;

    public string $expenseCategory = '';

    public string $expenseNewCategory = '';

    public bool $showNewExpenseCategory = false;

    public string $expenseDate = '';

    public string $expenseTitle = '';

    public float $expenseAmount = 0;

    public string $expensePaymentMethod = 'Kas Toko / Tunai';

    public string $expenseNotes = '';

    // Sub-tab for Salon / Barbershop Laba Rugi
    public string $financeSubTab = 'products'; // 'products' (penjualan produk master), 'services' (penjualan master layanan)

    // Filtering & Searching in Laba Rugi
    public string $financeSearch = '';

    public string $financeCategoryFilter = 'all';

    public string $financeDateFilter = 'this_month'; // 'today', '7_days', 'this_month', 'all'

    public string $financeSortBy = 'profit_desc'; // 'profit_desc', 'qty_desc', 'sales_desc', 'margin_desc', 'name_asc'

    public int $financePerPage = 10;

    public int $financeExpensePerPage = 10;

    public function setFinanceSubTab(string $tab): void
    {
        $this->financeSubTab = in_array($tab, ['products', 'services']) ? $tab : 'products';
        $this->financeCategoryFilter = 'all';
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function resetFinanceFilters(): void
    {
        $this->financeSearch = '';
        $this->financeCategoryFilter = 'all';
        $this->financeDateFilter = 'this_month';
        $this->financeSortBy = 'profit_desc';
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
        $this->resetPage('expensesPage');
    }

    public function updatedFinanceSearch(): void
    {
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function updatedFinanceCategoryFilter(): void
    {
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function updatedFinanceDateFilter(): void
    {
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
        $this->resetPage('expensesPage');
    }

    public function updatedFinanceSortBy(): void
    {
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function updatedFinancePerPage(): void
    {
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function updatedFinanceExpensePerPage(): void
    {
        $this->resetPage('expensesPage');
    }

    public function openExpenseModal(): void
    {
        $this->editingExpenseId = null;
        $this->expenseCategory = ExpenseCategory::namesForTenant($this->tenantId)[0] ?? 'Lainnya';
        $this->expenseDate = now()->toDateString();
        $this->expenseTitle = '';
        $this->expenseAmount = 250000;
        $this->expensePaymentMethod = 'Kas Toko / Tunai';
        $this->expenseNotes = '';
        $this->showNewExpenseCategory = false;
        $this->expenseNewCategory = '';
        $this->showExpenseModal = true;
    }

    public function editExpense(int $id): void
    {
        $expense = Expense::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingExpenseId = $expense->id;
        $this->expenseCategory = $expense->category;
        $this->expenseDate = $expense->expense_date ? $expense->expense_date->format('Y-m-d') : now()->toDateString();
        $this->expenseTitle = $expense->title;
        $this->expenseAmount = (float) $expense->amount;
        $this->expensePaymentMethod = $expense->payment_method ?? 'Kas Toko / Tunai';
        $this->expenseNotes = $expense->notes ?? '';
        $this->showNewExpenseCategory = false;
        $this->expenseNewCategory = '';
        $this->showExpenseModal = true;
    }

    public function deleteExpense(int $id): void
    {
        $expense = Expense::where('tenant_id', $this->tenantId)->find($id);
        if ($expense) {
            $title = $expense->title;
            $expense->delete();
            $this->showToast("Pengeluaran OPEX '{$title}' berhasil dihapus.", 'info');
        }
    }

    public function addExpenseCategory(): void
    {
        $this->validate([
            'expenseNewCategory' => 'required|string|min:2|max:100',
        ], [
            'expenseNewCategory.required' => 'Nama kategori wajib diisi.',
            'expenseNewCategory.min' => 'Nama kategori minimal 2 karakter.',
        ]);

        $name = trim($this->expenseNewCategory);
        ExpenseCategory::firstOrCreate(['tenant_id' => $this->tenantId, 'name' => $name]);

        $this->expenseCategory = $name;
        $this->expenseNewCategory = '';
        $this->showNewExpenseCategory = false;
        $this->showToast("Kategori biaya '{$name}' berhasil ditambahkan.");
    }

    public function saveExpense(): void
    {
        $this->validate([
            'expenseTitle' => 'required|min:3',
            'expenseAmount' => 'required|numeric|min:1000',
            'expenseCategory' => 'required|string|max:100',
            'expenseDate' => 'required|date|before_or_equal:today',
        ], [
            'expenseDate.before_or_equal' => 'Tanggal pengeluaran tidak boleh di masa depan.',
        ]);

        ExpenseCategory::firstOrCreate(['tenant_id' => $this->tenantId, 'name' => $this->expenseCategory]);

        if ($this->editingExpenseId) {
            $expense = Expense::where('tenant_id', $this->tenantId)->findOrFail($this->editingExpenseId);
            $expense->update([
                'category' => $this->expenseCategory,
                'title' => $this->expenseTitle,
                'amount' => $this->expenseAmount,
                'expense_date' => $this->expenseDate,
                'payment_method' => $this->expensePaymentMethod,
                'notes' => $this->expenseNotes,
            ]);

            $this->showExpenseModal = false;
            $this->showToast("Pengeluaran OPEX '{$this->expenseTitle}' berhasil diperbarui.");
        } else {
            Expense::create([
                'tenant_id' => $this->tenantId,
                'outlet_id' => $this->selectedOutletId,
                'category' => $this->expenseCategory,
                'title' => $this->expenseTitle,
                'amount' => $this->expenseAmount,
                'expense_date' => $this->expenseDate,
                'payment_method' => $this->expensePaymentMethod,
                'notes' => $this->expenseNotes,
            ]);

            $this->showExpenseModal = false;
            $this->showToast("Pengeluaran OPEX '{$this->expenseTitle}' senilai Rp ".number_format($this->expenseAmount, 0, ',', '.').' berhasil dicatat.');
        }

        $this->editingExpenseId = null;
    }

    public function getFinanceBreakdownData(bool $forExport = false): array
    {
        $now = Carbon::now();
        $dateStart = match ($this->financeDateFilter) {
            'today' => $now->copy()->startOfDay(),
            '7_days' => $now->copy()->subDays(7)->startOfDay(),
            'this_month' => $now->copy()->startOfMonth(),
            'all' => null,
            default => $now->copy()->startOfMonth(),
        };

        $dateFilterLabel = match ($this->financeDateFilter) {
            'today' => 'Hari Ini ('.$now->translatedFormat('d M Y').')',
            '7_days' => '7 Hari Terakhir',
            'this_month' => 'Bulan Ini ('.$now->translatedFormat('F Y').')',
            'all' => 'Semua Periode',
            default => 'Bulan Ini',
        };

        // Query completed orders
        $ordersQuery = Order::with(['items.product.category'])
            ->where('tenant_id', $this->tenantId)
            ->where('status', 'completed');

        if ($dateStart) {
            $ordersQuery->where('created_at', '>=', $dateStart);
        }
        $orders = $ordersQuery->get();

        // Group items by product_id
        $itemsByProduct = [];
        foreach ($orders as $order) {
            foreach ($order->items as $it) {
                $pId = $it->product_id;
                if (! isset($itemsByProduct[$pId])) {
                    $itemsByProduct[$pId] = [
                        'qty' => 0,
                        'subtotal' => 0,
                    ];
                }
                $itemsByProduct[$pId]['qty'] += (float) $it->qty;
                $itemsByProduct[$pId]['subtotal'] += (float) $it->subtotal;
            }
        }

        // 1. MASTER PRODUCTS BREAKDOWN (type != 'service')
        $masterProducts = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', '!=', 'service')
            ->get();

        $allProductRows = [];
        foreach ($masterProducts as $prod) {
            $soldData = $itemsByProduct[$prod->id] ?? ['qty' => 0, 'subtotal' => 0];
            $qtySold = (float) $soldData['qty'];
            $grossSales = (float) $soldData['subtotal'];

            $unitPrice = (float) $prod->base_price;
            $unitCost = (float) ($prod->cost_price > 0 ? $prod->cost_price : ($unitPrice * 0.45));
            $totalCost = $qtySold * $unitCost;
            $grossProfit = $grossSales - $totalCost;
            $marginPct = $grossSales > 0 ? round(($grossProfit / $grossSales) * 100, 1) : ($unitPrice > 0 ? round((($unitPrice - $unitCost) / $unitPrice) * 100, 1) : 0);

            $allProductRows[] = [
                'id' => $prod->id,
                'name' => $prod->name,
                'sku' => $prod->sku ?? 'PRD-'.$prod->id,
                'category_id' => $prod->category_id,
                'category_name' => $prod->category?->name ?? 'Uncategorized',
                'image_url' => $prod->image_url,
                'unit' => $prod->unit ?? 'pcs',
                'unit_cost' => $unitCost,
                'unit_price' => $unitPrice,
                'qty_sold' => $qtySold,
                'gross_sales' => $grossSales,
                'total_cost' => $totalCost,
                'gross_profit' => $grossProfit,
                'margin_pct' => $marginPct,
                'stock_quantity' => $prod->stock_quantity,
            ];
        }

        // 2. MASTER SERVICES BREAKDOWN (type == 'service')
        $masterServices = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', 'service')
            ->get();

        $allServiceRows = [];
        foreach ($masterServices as $srv) {
            $soldData = $itemsByProduct[$srv->id] ?? ['qty' => 0, 'subtotal' => 0];
            $sessionsCount = (float) $soldData['qty'];
            $grossSales = (float) $soldData['subtotal'];

            $tarif = (float) $srv->base_price;
            $unitMaterialCost = (float) ($srv->cost_price ?? 0);
            $totalMaterialCost = $sessionsCount * $unitMaterialCost;

            if ($srv->commission_type === 'percentage') {
                $commissionPerSession = round(($tarif * (float) $srv->commission_value) / 100);
                $commissionScheme = (int) $srv->commission_value.'% (Rp '.number_format($commissionPerSession, 0, ',', '.').')';
            } else {
                $commissionPerSession = (float) ($srv->commission_value ?: 20000);
                $commissionScheme = 'Rp '.number_format($commissionPerSession, 0, ',', '.').' (Tetap)';
            }

            $totalCommission = $sessionsCount * $commissionPerSession;
            $netProfit = $grossSales - $totalMaterialCost - $totalCommission;
            $marginPct = $grossSales > 0 ? round(($netProfit / $grossSales) * 100, 1) : ($tarif > 0 ? round((($tarif - $unitMaterialCost - $commissionPerSession) / $tarif) * 100, 1) : 0);

            $allServiceRows[] = [
                'id' => $srv->id,
                'name' => $srv->name,
                'category_id' => $srv->category_id,
                'category_name' => $srv->category?->name ?? 'Layanan Salon',
                'category_tag' => $srv->category_tag ?? 'Treatment',
                'duration_minutes' => $srv->duration_minutes ?: 30,
                'image_url' => $srv->image_url,
                'tarif' => $tarif,
                'unit_material_cost' => $unitMaterialCost,
                'commission_per_session' => $commissionPerSession,
                'commission_scheme' => $commissionScheme,
                'sessions_count' => $sessionsCount,
                'gross_sales' => $grossSales,
                'total_material_cost' => $totalMaterialCost,
                'total_commission' => $totalCommission,
                'net_profit' => $netProfit,
                'margin_pct' => $marginPct,
            ];
        }

        // Summary before filtering
        $productSummary = [
            'total_sales' => array_sum(array_column($allProductRows, 'gross_sales')),
            'total_cogs' => array_sum(array_column($allProductRows, 'total_cost')),
            'total_profit' => array_sum(array_column($allProductRows, 'gross_profit')),
            'total_qty' => array_sum(array_column($allProductRows, 'qty_sold')),
            'avg_margin' => 0,
        ];
        $productSummary['avg_margin'] = $productSummary['total_sales'] > 0
            ? round(($productSummary['total_profit'] / $productSummary['total_sales']) * 100, 1)
            : 0;

        $serviceSummary = [
            'total_sales' => array_sum(array_column($allServiceRows, 'gross_sales')),
            'total_material_cost' => array_sum(array_column($allServiceRows, 'total_material_cost')),
            'total_commission' => array_sum(array_column($allServiceRows, 'total_commission')),
            'total_profit' => array_sum(array_column($allServiceRows, 'net_profit')),
            'total_sessions' => array_sum(array_column($allServiceRows, 'sessions_count')),
            'avg_margin' => 0,
        ];
        $serviceSummary['avg_margin'] = $serviceSummary['total_sales'] > 0
            ? round(($serviceSummary['total_profit'] / $serviceSummary['total_sales']) * 100, 1)
            : 0;

        // Apply filtering on Products
        $filteredProductRows = $allProductRows;
        if ($this->financeCategoryFilter !== 'all') {
            $filteredProductRows = array_filter($filteredProductRows, fn ($r) => (string) $r['category_id'] === (string) $this->financeCategoryFilter);
        }
        if (! empty($this->financeSearch)) {
            $kw = strtolower(trim($this->financeSearch));
            $filteredProductRows = array_filter($filteredProductRows, fn ($r) => str_contains(strtolower($r['name']), $kw) ||
                str_contains(strtolower($r['sku']), $kw) ||
                str_contains(strtolower($r['category_name']), $kw)
            );
        }

        // Sort Products
        usort($filteredProductRows, function ($a, $b) {
            return match ($this->financeSortBy) {
                'profit_desc' => $b['gross_profit'] <=> $a['gross_profit'],
                'qty_desc' => $b['qty_sold'] <=> $a['qty_sold'],
                'sales_desc' => $b['gross_sales'] <=> $a['gross_sales'],
                'margin_desc' => $b['margin_pct'] <=> $a['margin_pct'],
                'name_asc' => strcasecmp($a['name'], $b['name']),
                default => $b['gross_profit'] <=> $a['gross_profit'],
            };
        });

        // Apply filtering on Services
        $filteredServiceRows = $allServiceRows;
        if ($this->financeCategoryFilter !== 'all') {
            $filteredServiceRows = array_filter($filteredServiceRows, fn ($r) => (string) $r['category_id'] === (string) $this->financeCategoryFilter);
        }
        if (! empty($this->financeSearch)) {
            $kw = strtolower(trim($this->financeSearch));
            $filteredServiceRows = array_filter($filteredServiceRows, fn ($r) => str_contains(strtolower($r['name']), $kw) ||
                str_contains(strtolower($r['category_tag']), $kw) ||
                str_contains(strtolower($r['category_name']), $kw)
            );
        }

        // Sort Services
        usort($filteredServiceRows, function ($a, $b) {
            return match ($this->financeSortBy) {
                'profit_desc' => $b['net_profit'] <=> $a['net_profit'],
                'qty_desc' => $b['sessions_count'] <=> $a['sessions_count'],
                'sales_desc' => $b['gross_sales'] <=> $a['gross_sales'],
                'commission_desc' => $b['total_commission'] <=> $a['total_commission'],
                'name_asc' => strcasecmp($a['name'], $b['name']),
                default => $b['net_profit'] <=> $a['net_profit'],
            };
        });

        // Combined Salon P&L Waterfall
        $combinedSales = $productSummary['total_sales'] + $serviceSummary['total_sales'];
        $combinedDirectCost = $productSummary['total_cogs'] + $serviceSummary['total_material_cost'] + $serviceSummary['total_commission'];
        $combinedGrossProfit = $productSummary['total_profit'] + $serviceSummary['total_profit'];
        $combinedMargin = $combinedSales > 0 ? round(($combinedGrossProfit / $combinedSales) * 100, 1) : 0;

        // OPEX mengikuti periode yang sama dengan penjualan
        $expenseQuery = Expense::where('tenant_id', $this->tenantId);
        if ($dateStart) {
            $expenseQuery->whereDate('expense_date', '>=', $dateStart->toDateString());
        }
        $expenseRows = (clone $expenseQuery)->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->get();
        $totalOpex = (float) $expenseRows->sum('amount');
        $combinedNetProfit = $combinedGrossProfit - $totalOpex;

        $expenseRowsToReturn = $forExport
            ? $expenseRows
            : (clone $expenseQuery)->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate($this->financeExpensePerPage, ['*'], 'expensesPage');

        // Categories list for active subtab filter
        $activeCategories = Category::where('tenant_id', $this->tenantId)
            ->whereHas('products', function ($q) {
                if ($this->financeSubTab === 'services') {
                    $q->where('type', 'service');
                } else {
                    $q->where('type', '!=', 'service');
                }
            })
            ->orderBy('name')
            ->get();

        $filteredProductRows = array_values($filteredProductRows);
        $totalProductRows = count($filteredProductRows);

        $filteredServiceRows = array_values($filteredServiceRows);
        $totalServiceRows = count($filteredServiceRows);

        if ($forExport) {
            $productRowsToReturn = $filteredProductRows;
            $serviceRowsToReturn = $filteredServiceRows;
        } else {
            $financePerPage = max(1, $this->financePerPage);
            $productPage = (int) $this->getPage('financeProductsPage');
            $productOffset = ($productPage - 1) * $financePerPage;
            $productRowsToReturn = new LengthAwarePaginator(
                array_slice($filteredProductRows, $productOffset, $financePerPage),
                $totalProductRows,
                $financePerPage,
                $productPage,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'financeProductsPage',
                ]
            );

            $servicePage = (int) $this->getPage('financeServicesPage');
            $serviceOffset = ($servicePage - 1) * $financePerPage;
            $serviceRowsToReturn = new LengthAwarePaginator(
                array_slice($filteredServiceRows, $serviceOffset, $financePerPage),
                $totalServiceRows,
                $financePerPage,
                $servicePage,
                [
                    'path' => Paginator::resolveCurrentPath(),
                    'pageName' => 'financeServicesPage',
                ]
            );
        }

        return [
            'dateFilterLabel' => $dateFilterLabel,
            'productRows' => $productRowsToReturn,
            'totalProductRows' => $totalProductRows,
            'serviceRows' => $serviceRowsToReturn,
            'totalServiceRows' => $totalServiceRows,
            'productSummary' => $productSummary,
            'serviceSummary' => $serviceSummary,
            'combinedSales' => $combinedSales,
            'combinedDirectCost' => $combinedDirectCost,
            'combinedGrossProfit' => $combinedGrossProfit,
            'combinedMargin' => $combinedMargin,
            'expenseRows' => $expenseRowsToReturn,
            'totalExpenseRows' => $expenseRows->count(),
            'totalOpex' => $totalOpex,
            'combinedNetProfit' => $combinedNetProfit,
            'activeCategories' => $activeCategories,
        ];
    }
}
