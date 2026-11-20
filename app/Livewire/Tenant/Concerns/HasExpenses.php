<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
    public string $financeSubTab = 'products'; // 'products' (penjualan produk master), 'services' (penjualan master layanan), 'commission' (komisi pegawai)

    // Filter periode tambahan per tabel (irisan dengan filter periode global). Default 'all' = tampilan tidak berubah.
    /** @var array<string, array{mode: string, date: string, month: string, start: string, end: string}> */
    public array $financePeriods = [];

    // Filtering & Searching in Laba Rugi
    public string $financeSearch = '';

    public string $financeCategoryFilter = 'all';

    public string $financeDateFilter = 'this_month'; // 'today', '7_days', 'this_month', 'all'

    public string $financeSortBy = 'profit_desc'; // 'profit_desc', 'qty_desc', 'sales_desc', 'margin_desc', 'name_asc'

    public int $financePerPage = 10;

    public int $financeExpensePerPage = 10;

    public function setFinanceSubTab(string $tab): void
    {
        $this->financeSubTab = in_array($tab, ['products', 'services', 'commission'], true) ? $tab : 'products';
        $this->financeCategoryFilter = 'all';
        $this->resetPage('financeProductsPage');
        $this->resetPage('financeServicesPage');
    }

    public function setFinancePeriodMode(string $key, string $mode): void
    {
        if (! in_array($key, $this->financePeriodKeys(), true) || ! in_array($mode, $this->financePeriodModes(), true)) {
            return;
        }

        $this->ensureFinancePeriods();
        $this->financePeriods[$key]['mode'] = $mode;
        $this->resetFinancePeriodPage($key);
    }

    public function updatedFinancePeriods(mixed $value, string $key): void
    {
        $this->resetFinancePeriodPage(explode('.', $key)[0]);
    }

    public function resetFinanceFilters(): void
    {
        $this->financeSearch = '';
        $this->financeCategoryFilter = 'all';
        $this->financeDateFilter = 'this_month';
        $this->financeSortBy = 'profit_desc';
        $this->financePeriods = [];
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
        $this->ensureFinancePeriods();

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

        $masterProducts = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', '!=', 'service')
            ->get();

        $masterServices = Product::with('category')
            ->where('tenant_id', $this->tenantId)
            ->where('type', 'service')
            ->get();

        // Ringkasan (mengikuti filter periode global, tidak terpengaruh filter tabel)
        $allProductRows = $this->buildProductRows($masterProducts, $this->soldItemsByProduct($dateStart, null));
        $allServiceRows = $this->buildServiceRows($masterServices, $this->soldItemsByProduct($dateStart, null));

        $productSummary = $this->summarizeProductRows($allProductRows);
        $serviceSummary = $this->summarizeServiceRows($allServiceRows);

        // Filter per tabel: irisan periode global dengan periode tabel
        $productWindow = $this->financeTableWindow('products', $dateStart);
        $productTableRows = $this->buildProductRows($masterProducts, $this->soldItemsByProduct($productWindow['start'], $productWindow['end']));
        $productTableSummary = $this->summarizeProductRows($productTableRows);
        $filteredProductRows = array_values($this->filterAndSortProductRows($productTableRows));

        $serviceWindow = $this->financeTableWindow('services', $dateStart);
        $serviceTableRows = $this->buildServiceRows($masterServices, $this->soldItemsByProduct($serviceWindow['start'], $serviceWindow['end']));
        $serviceTableSummary = $this->summarizeServiceRows($serviceTableRows);
        $filteredServiceRows = array_values($this->filterAndSortServiceRows($serviceTableRows));

        // Combined Salon P&L Waterfall
        $combinedSales = $productSummary['total_sales'] + $serviceSummary['total_sales'];
        $combinedDirectCost = $productSummary['total_cogs'] + $serviceSummary['total_material_cost'] + $serviceSummary['total_commission'];
        $combinedGrossProfit = $productSummary['total_profit'] + $serviceSummary['total_profit'];
        $combinedMargin = $combinedSales > 0 ? round(($combinedGrossProfit / $combinedSales) * 100, 1) : 0;

        // OPEX mengikuti periode yang sama dengan penjualan (ringkasan)
        $totalOpex = (float) $this->expenseQueryForPeriod($dateStart, null)->sum('amount');
        $combinedNetProfit = $combinedGrossProfit - $totalOpex;

        // Komisi per layanan (periode tabel komisi, irisan dengan periode global)
        $commissionWindow = $this->financeTableWindow('commission', $dateStart);
        $commissionRows = $this->buildServiceRows($masterServices, $this->soldItemsByProduct($commissionWindow['start'], $commissionWindow['end']));
        $commissionRows = array_values(array_filter($commissionRows, fn ($row) => $row['sessions_count'] > 0));
        usort($commissionRows, fn ($a, $b) => $b['total_commission'] <=> $a['total_commission']);
        $commissionSummary = $this->summarizeCommissionRows($commissionRows);

        // OPEX tabel (irisan periode global dengan periode tabel OPEX)
        $opexWindow = $this->financeTableWindow('opex', $dateStart);
        $expenseQuery = $this->expenseQueryForPeriod($opexWindow['start'], $opexWindow['end']);
        $opexPeriodTotal = (float) (clone $expenseQuery)->sum('amount');
        $totalExpenseRows = (clone $expenseQuery)->count();

        $expenseOrder = fn (Builder $query) => $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc');
        $expenseRowsToReturn = $forExport
            ? $expenseOrder(clone $expenseQuery)->get()
            : $expenseOrder(clone $expenseQuery)->paginate($this->financeExpensePerPage, ['*'], 'expensesPage');

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

        if ($forExport) {
            $productRowsToReturn = $filteredProductRows;
            $serviceRowsToReturn = $filteredServiceRows;
        } else {
            $productRowsToReturn = $this->paginateFinanceRows($filteredProductRows, 'financeProductsPage');
            $serviceRowsToReturn = $this->paginateFinanceRows($filteredServiceRows, 'financeServicesPage');
        }

        return [
            'dateFilterLabel' => $dateFilterLabel,
            'productRows' => $productRowsToReturn,
            'totalProductRows' => count($filteredProductRows),
            'productTableSummary' => $productTableSummary,
            'productPeriodLabel' => $this->financePeriodLabel('products'),
            'serviceRows' => $serviceRowsToReturn,
            'totalServiceRows' => count($filteredServiceRows),
            'serviceTableSummary' => $serviceTableSummary,
            'servicePeriodLabel' => $this->financePeriodLabel('services'),
            'productSummary' => $productSummary,
            'serviceSummary' => $serviceSummary,
            'combinedSales' => $combinedSales,
            'combinedDirectCost' => $combinedDirectCost,
            'combinedGrossProfit' => $combinedGrossProfit,
            'combinedMargin' => $combinedMargin,
            'commissionRows' => $commissionRows,
            'commissionSummary' => $commissionSummary,
            'commissionPeriodLabel' => $this->financePeriodLabel('commission'),
            'expenseRows' => $expenseRowsToReturn,
            'totalExpenseRows' => $totalExpenseRows,
            'opexPeriodTotal' => $opexPeriodTotal,
            'opexPeriodLabel' => $this->financePeriodLabel('opex'),
            'totalOpex' => $totalOpex,
            'combinedNetProfit' => $combinedNetProfit,
            'activeCategories' => $activeCategories,
        ];
    }

    /**
     * @return list<string>
     */
    private function financePeriodKeys(): array
    {
        return ['products', 'services', 'commission', 'opex'];
    }

    /**
     * @return list<string>
     */
    private function financePeriodModes(): array
    {
        return ['day', 'week', 'month', 'range', 'all'];
    }

    /**
     * @return array{mode: string, date: string, month: string, start: string, end: string}
     */
    private function financePeriodDefaults(): array
    {
        $today = now();

        return [
            'mode' => 'all',
            'date' => $today->toDateString(),
            'month' => $today->format('Y-m'),
            'start' => $today->copy()->startOfMonth()->toDateString(),
            'end' => $today->toDateString(),
        ];
    }

    private function ensureFinancePeriods(): void
    {
        $defaults = $this->financePeriodDefaults();

        foreach ($this->financePeriodKeys() as $key) {
            $this->financePeriods[$key] = array_merge($defaults, $this->financePeriods[$key] ?? []);
        }
    }

    private function resetFinancePeriodPage(string $key): void
    {
        match ($key) {
            'products' => $this->resetPage('financeProductsPage'),
            'services' => $this->resetPage('financeServicesPage'),
            'opex' => $this->resetPage('expensesPage'),
            default => null,
        };
    }

    /**
     * Irisan periode global (dateStart) dengan periode tabel. Mode 'all' = tanpa batas tambahan.
     *
     * @return array{start: ?Carbon, end: ?Carbon}
     */
    private function financeTableWindow(string $key, ?Carbon $globalStart): array
    {
        $period = $this->resolveFinancePeriod($key);

        $start = $globalStart;
        if ($period['start'] && (! $start || $period['start']->gt($start))) {
            $start = $period['start'];
        }

        return ['start' => $start, 'end' => $period['end']];
    }

    private function financePeriodLabel(string $key): string
    {
        return $this->resolveFinancePeriod($key)['label'];
    }

    /**
     * @return array{start: ?Carbon, end: ?Carbon, label: string}
     */
    private function resolveFinancePeriod(string $key): array
    {
        $defaults = $this->financePeriodDefaults();
        $stored = array_filter($this->financePeriods[$key] ?? [], fn ($value) => $value !== '' && $value !== null);
        $config = array_merge($defaults, $stored);
        $mode = in_array($config['mode'], $this->financePeriodModes(), true) ? $config['mode'] : 'all';

        return match ($mode) {
            'day' => $this->financePeriodFromRange(
                $this->parseFinanceDate($config['date'], now())->startOfDay(),
                $this->parseFinanceDate($config['date'], now())->endOfDay(),
                'Harian: '.$this->parseFinanceDate($config['date'], now())->translatedFormat('d M Y'),
            ),
            'week' => $this->resolveFinanceWeek($config['date']),
            'month' => $this->resolveFinanceMonth($config['month']),
            'range' => $this->resolveFinanceCustomRange($config['start'], $config['end']),
            default => ['start' => null, 'end' => null, 'label' => 'Semua Periode'],
        };
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function resolveFinanceWeek(string $date): array
    {
        $anchor = $this->parseFinanceDate($date, now());
        $start = $anchor->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $end = $anchor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();

        return $this->financePeriodFromRange(
            $start,
            $end,
            'Mingguan: '.$start->translatedFormat('d M').' - '.$end->translatedFormat('d M Y'),
        );
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function resolveFinanceMonth(string $month): array
    {
        try {
            $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        } catch (\Throwable) {
            $start = now()->startOfMonth();
        }

        return $this->financePeriodFromRange(
            $start,
            $start->copy()->endOfMonth(),
            'Bulanan: '.$start->translatedFormat('F Y'),
        );
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function resolveFinanceCustomRange(string $startDate, string $endDate): array
    {
        $start = $this->parseFinanceDate($startDate, now()->startOfMonth())->startOfDay();
        $end = $this->parseFinanceDate($endDate, now())->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return $this->financePeriodFromRange(
            $start,
            $end,
            'Rentang: '.$start->translatedFormat('d M Y').' - '.$end->translatedFormat('d M Y'),
        );
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string}
     */
    private function financePeriodFromRange(Carbon $start, Carbon $end, string $label): array
    {
        return ['start' => $start, 'end' => $end, 'label' => $label];
    }

    private function parseFinanceDate(string $value, Carbon $fallback): Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (\Throwable) {
            return $fallback;
        }
    }

    /**
     * @return array<int, array{qty: float, subtotal: float}>
     */
    private function soldItemsByProduct(?Carbon $start, ?Carbon $end): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.tenant_id', $this->tenantId)
            ->where('orders.status', 'completed');

        if ($start) {
            $query->where('orders.created_at', '>=', $start);
        }
        if ($end) {
            $query->where('orders.created_at', '<=', $end);
        }

        $sold = [];
        foreach ($query->selectRaw('order_items.product_id, SUM(order_items.qty) AS qty, SUM(order_items.subtotal) AS subtotal')
            ->groupBy('order_items.product_id')
            ->get() as $row) {
            $sold[$row->product_id] = ['qty' => (float) $row->qty, 'subtotal' => (float) $row->subtotal];
        }

        return $sold;
    }

    /**
     * @param  array<int, array{qty: float, subtotal: float}>  $soldByProduct
     * @return array<int, array<string, mixed>>
     */
    private function buildProductRows(Collection $masterProducts, array $soldByProduct): array
    {
        $rows = [];
        foreach ($masterProducts as $prod) {
            $soldData = $soldByProduct[$prod->id] ?? ['qty' => 0, 'subtotal' => 0];
            $qtySold = (float) $soldData['qty'];
            $grossSales = (float) $soldData['subtotal'];

            $unitPrice = (float) $prod->base_price;
            $unitCost = (float) ($prod->cost_price > 0 ? $prod->cost_price : ($unitPrice * 0.45));
            $totalCost = $qtySold * $unitCost;
            $grossProfit = $grossSales - $totalCost;
            $marginPct = $grossSales > 0 ? round(($grossProfit / $grossSales) * 100, 1) : ($unitPrice > 0 ? round((($unitPrice - $unitCost) / $unitPrice) * 100, 1) : 0);

            $rows[] = [
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

        return $rows;
    }

    /**
     * @param  array<int, array{qty: float, subtotal: float}>  $soldByProduct
     * @return array<int, array<string, mixed>>
     */
    private function buildServiceRows(Collection $masterServices, array $soldByProduct): array
    {
        $rows = [];
        foreach ($masterServices as $srv) {
            $soldData = $soldByProduct[$srv->id] ?? ['qty' => 0, 'subtotal' => 0];
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

            $rows[] = [
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

        return $rows;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{total_sales: float, total_cogs: float, total_profit: float, total_qty: float, avg_margin: float}
     */
    private function summarizeProductRows(array $rows): array
    {
        $summary = [
            'total_sales' => array_sum(array_column($rows, 'gross_sales')),
            'total_cogs' => array_sum(array_column($rows, 'total_cost')),
            'total_profit' => array_sum(array_column($rows, 'gross_profit')),
            'total_qty' => array_sum(array_column($rows, 'qty_sold')),
            'avg_margin' => 0,
        ];
        $summary['avg_margin'] = $summary['total_sales'] > 0
            ? round(($summary['total_profit'] / $summary['total_sales']) * 100, 1)
            : 0;

        return $summary;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{total_sales: float, total_material_cost: float, total_commission: float, total_profit: float, total_sessions: float, avg_margin: float}
     */
    private function summarizeServiceRows(array $rows): array
    {
        $summary = [
            'total_sales' => array_sum(array_column($rows, 'gross_sales')),
            'total_material_cost' => array_sum(array_column($rows, 'total_material_cost')),
            'total_commission' => array_sum(array_column($rows, 'total_commission')),
            'total_profit' => array_sum(array_column($rows, 'net_profit')),
            'total_sessions' => array_sum(array_column($rows, 'sessions_count')),
            'avg_margin' => 0,
        ];
        $summary['avg_margin'] = $summary['total_sales'] > 0
            ? round(($summary['total_profit'] / $summary['total_sales']) * 100, 1)
            : 0;

        return $summary;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{total_commission: float, total_sessions: float, avg_commission: float, service_count: int}
     */
    private function summarizeCommissionRows(array $rows): array
    {
        $totalCommission = (float) array_sum(array_column($rows, 'total_commission'));
        $totalSessions = (float) array_sum(array_column($rows, 'sessions_count'));

        return [
            'total_commission' => $totalCommission,
            'total_sessions' => $totalSessions,
            'avg_commission' => $totalSessions > 0 ? round($totalCommission / $totalSessions) : 0,
            'service_count' => count($rows),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function filterAndSortProductRows(array $rows): array
    {
        if ($this->financeCategoryFilter !== 'all') {
            $rows = array_filter($rows, fn ($r) => (string) $r['category_id'] === (string) $this->financeCategoryFilter);
        }
        if (! empty($this->financeSearch)) {
            $kw = strtolower(trim($this->financeSearch));
            $rows = array_filter($rows, fn ($r) => str_contains(strtolower($r['name']), $kw) ||
                str_contains(strtolower($r['sku']), $kw) ||
                str_contains(strtolower($r['category_name']), $kw)
            );
        }

        usort($rows, function ($a, $b) {
            return match ($this->financeSortBy) {
                'profit_desc' => $b['gross_profit'] <=> $a['gross_profit'],
                'qty_desc' => $b['qty_sold'] <=> $a['qty_sold'],
                'sales_desc' => $b['gross_sales'] <=> $a['gross_sales'],
                'margin_desc' => $b['margin_pct'] <=> $a['margin_pct'],
                'name_asc' => strcasecmp($a['name'], $b['name']),
                default => $b['gross_profit'] <=> $a['gross_profit'],
            };
        });

        return $rows;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    private function filterAndSortServiceRows(array $rows): array
    {
        if ($this->financeCategoryFilter !== 'all') {
            $rows = array_filter($rows, fn ($r) => (string) $r['category_id'] === (string) $this->financeCategoryFilter);
        }
        if (! empty($this->financeSearch)) {
            $kw = strtolower(trim($this->financeSearch));
            $rows = array_filter($rows, fn ($r) => str_contains(strtolower($r['name']), $kw) ||
                str_contains(strtolower($r['category_tag']), $kw) ||
                str_contains(strtolower($r['category_name']), $kw)
            );
        }

        usort($rows, function ($a, $b) {
            return match ($this->financeSortBy) {
                'profit_desc' => $b['net_profit'] <=> $a['net_profit'],
                'qty_desc' => $b['sessions_count'] <=> $a['sessions_count'],
                'sales_desc' => $b['gross_sales'] <=> $a['gross_sales'],
                'commission_desc' => $b['total_commission'] <=> $a['total_commission'],
                'name_asc' => strcasecmp($a['name'], $b['name']),
                default => $b['net_profit'] <=> $a['net_profit'],
            };
        });

        return $rows;
    }

    private function expenseQueryForPeriod(?Carbon $start, ?Carbon $end): Builder
    {
        $query = Expense::where('tenant_id', $this->tenantId);

        if ($start) {
            $query->whereDate('expense_date', '>=', $start->toDateString());
        }
        if ($end) {
            $query->whereDate('expense_date', '<=', $end->toDateString());
        }

        return $query;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function paginateFinanceRows(array $rows, string $pageName): LengthAwarePaginator
    {
        $perPage = max(1, $this->financePerPage);
        $page = (int) $this->getPage($pageName);

        return new LengthAwarePaginator(
            array_slice($rows, ($page - 1) * $perPage, $perPage),
            count($rows),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
}
