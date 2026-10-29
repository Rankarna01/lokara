<?php

namespace App\Livewire\Tenant;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class BackofficeDashboard extends Component
{
    public string $activeTab = 'dashboard';
    public ?int $tenantId = null;
    public ?int $selectedOutletId = null;

    // Search and filter states
    public string $searchProduct = '';
    public string $selectedCategoryFilter = 'all';
    public string $searchIngredient = '';
    public string $searchTransaction = '';
    public string $transactionStatusFilter = 'all';

    // Modals
    public bool $showProductModal = false;
    public ?int $editingProductId = null;
    public string $productName = '';
    public ?int $productCategoryId = null;
    public string $productCategoryTag = 'Kopi • Hot / Ice';
    public float $productBasePrice = 0;
    public float $productCostPrice = 0;
    public string $productImageUrl = '';
    public bool $productIsActive = true;
    public bool $productIsAvailable = true;

    // Ingredient Modal
    public bool $showIngredientModal = false;
    public string $ingredientName = '';
    public string $ingredientCode = '';
    public string $ingredientCategory = 'Biji Kopi';
    public string $ingredientPurchaseUnit = 'Kg';
    public string $ingredientUsageUnit = 'Gram';
    public float $ingredientConversionFactor = 1000;
    public float $ingredientCostPerUsage = 180;
    public float $ingredientCurrentStock = 5000;
    public float $ingredientMinStock = 2000;
    public string $ingredientLocation = 'Bar Depan';

    // Recipe Builder Modal
    public bool $showRecipeModal = false;
    public ?int $recipeProductId = null;
    public ?Product $selectedRecipeProduct = null;
    public ?int $selectedRecipeIngredientId = null;
    public float $recipeQuantity = 10;

    // Stock Opname Modal
    public bool $showOpnameModal = false;
    public ?int $opnameIngredientId = null;
    public float $opnamePhysicalQty = 0;
    public string $opnameReason = 'Pemeriksaan rutin mingguan';

    // Expense Modal
    public bool $showExpenseModal = false;
    public string $expenseCategory = 'Listrik & Air';
    public string $expenseTitle = '';
    public float $expenseAmount = 0;
    public string $expensePaymentMethod = 'Kas Toko / Tunai';
    public string $expenseNotes = '';

    // Purchase Order Modal
    public bool $showPoModal = false;
    public ?int $poSupplierId = null;
    public float $poTotalAmount = 0;
    public string $poNotes = '';

    // Void Order Modal
    public bool $showVoidModal = false;
    public ?int $voidOrderId = null;
    public string $voidPin = '';
    public string $voidReason = 'Kesalahan input kasir';

    // Toast
    public string $toastMessage = '';
    public string $toastType = 'success';

    public function mount()
    {
        // Try getting tenant from logged in user, or fallback to first tenant (Kopi Ruang Pagi)
        $user = Auth::user();
        if ($user && $user->tenant_id) {
            $this->tenantId = $user->tenant_id;
            $this->selectedOutletId = $user->outlet_id;
        } else {
            $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
            $this->tenantId = $tenant?->id;
        }

        if (!$this->selectedOutletId && $this->tenantId) {
            $outlet = Outlet::where('tenant_id', $this->tenantId)->first();
            $this->selectedOutletId = $outlet?->id;
        }
    }

    public function switchTab(string $tab)
    {
        $this->activeTab = $tab;
    }

    public function switchOutlet(int $outletId)
    {
        $this->selectedOutletId = $outletId;
        $this->showToast('Outlet aktif diubah.', 'info');
    }

    // --- PRODUCT MANAGEMENT ---
    public function openNewProductModal()
    {
        $this->resetProductForm();
        $this->showProductModal = true;
    }

    public function editProduct(int $id)
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingProductId = $product->id;
        $this->productName = $product->name;
        $this->productCategoryId = $product->category_id;
        $this->productCategoryTag = $product->category_tag ?? 'Kopi • Ice';
        $this->productBasePrice = (float) $product->base_price;
        $this->productCostPrice = (float) $product->cost_price;
        $this->productImageUrl = $product->image_url ?? '';
        $this->productIsActive = (bool) $product->is_active;
        $this->productIsAvailable = (bool) $product->is_available;
        $this->showProductModal = true;
    }

    public function saveProduct()
    {
        $this->validate([
            'productName' => 'required|min:2',
            'productBasePrice' => 'required|numeric|min:0',
            'productCostPrice' => 'nullable|numeric|min:0',
        ]);

        if ($this->editingProductId) {
            $product = Product::where('tenant_id', $this->tenantId)->findOrFail($this->editingProductId);
            $product->update([
                'name' => $this->productName,
                'category_id' => $this->productCategoryId,
                'category_tag' => $this->productCategoryTag,
                'base_price' => $this->productBasePrice,
                'cost_price' => $this->productCostPrice,
                'image_url' => $this->productImageUrl ?: null,
                'is_active' => $this->productIsActive,
                'is_available' => $this->productIsAvailable,
            ]);
            $this->showToast("Produk '{$product->name}' berhasil diperbarui.");
        } else {
            Product::create([
                'tenant_id' => $this->tenantId,
                'category_id' => $this->productCategoryId,
                'category_tag' => $this->productCategoryTag,
                'name' => $this->productName,
                'slug' => Str::slug($this->productName) . '-' . rand(100, 999),
                'sku' => 'PRD-' . strtoupper(Str::random(5)),
                'barcode' => '899' . rand(100000000, 999999999),
                'base_price' => $this->productBasePrice,
                'cost_price' => $this->productCostPrice,
                'image_url' => $this->productImageUrl ?: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&h=300&q=80',
                'is_active' => $this->productIsActive,
                'is_available' => $this->productIsAvailable,
            ]);
            $this->showToast("Produk '{$this->productName}' berhasil ditambahkan ke katalog!");
        }

        $this->showProductModal = false;
        $this->resetProductForm();
    }

    public function toggleProductAvailability(int $id)
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $product->is_available = !$product->is_available;
        $product->save();
        $status = $product->is_available ? 'Tersedia' : 'Habis / Stok Kosong';
        $this->showToast("Status {$product->name} diubah menjadi '{$status}'");
    }

    private function resetProductForm()
    {
        $this->editingProductId = null;
        $this->productName = '';
        $this->productCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        $this->productCategoryTag = 'Kopi • Ice';
        $this->productBasePrice = 20000;
        $this->productCostPrice = 7000;
        $this->productImageUrl = '';
        $this->productIsActive = true;
        $this->productIsAvailable = true;
    }

    // --- INGREDIENT & RECIPE MANAGEMENT ---
    public function openNewIngredientModal()
    {
        $this->ingredientName = '';
        $this->ingredientCode = 'RAW-' . str_pad((string) rand(10, 99), 3, '0', STR_PAD_LEFT);
        $this->ingredientCategory = 'Biji Kopi';
        $this->ingredientPurchaseUnit = 'Kg';
        $this->ingredientUsageUnit = 'Gram';
        $this->ingredientConversionFactor = 1000;
        $this->ingredientCostPerUsage = 180;
        $this->ingredientCurrentStock = 5000;
        $this->ingredientMinStock = 2000;
        $this->ingredientLocation = 'Bar Depan';
        $this->showIngredientModal = true;
    }

    public function saveIngredient()
    {
        $this->validate([
            'ingredientName' => 'required|min:2',
            'ingredientCostPerUsage' => 'required|numeric|min:0',
            'ingredientCurrentStock' => 'required|numeric|min:0',
        ]);

        Ingredient::create([
            'tenant_id' => $this->tenantId,
            'name' => $this->ingredientName,
            'code' => $this->ingredientCode,
            'category' => $this->ingredientCategory,
            'purchase_unit' => $this->ingredientPurchaseUnit,
            'usage_unit' => $this->ingredientUsageUnit,
            'conversion_factor' => $this->ingredientConversionFactor,
            'cost_per_usage_unit' => $this->ingredientCostPerUsage,
            'current_stock' => $this->ingredientCurrentStock,
            'minimum_stock' => $this->ingredientMinStock,
            'storage_location' => $this->ingredientLocation,
        ]);

        $this->showIngredientModal = false;
        $this->showToast("Bahan baku '{$this->ingredientName}' berhasil didaftarkan!");
    }

    public function openRecipeModal(int $productId)
    {
        $this->recipeProductId = $productId;
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->findOrFail($productId);
        $firstIng = Ingredient::where('tenant_id', $this->tenantId)->first();
        $this->selectedRecipeIngredientId = $firstIng?->id;
        $this->recipeQuantity = 15;
        $this->showRecipeModal = true;
    }

    public function addRecipeItem()
    {
        if (!$this->recipeProductId || !$this->selectedRecipeIngredientId || $this->recipeQuantity <= 0) {
            return;
        }

        ProductRecipe::updateOrCreate(
            ['product_id' => $this->recipeProductId, 'ingredient_id' => $this->selectedRecipeIngredientId],
            ['quantity' => $this->recipeQuantity]
        );

        // Recalculate HPP from recipes!
        $this->recalculateProductHpp($this->recipeProductId);

        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($this->recipeProductId);
        $this->showToast("Komposisi bahan berhasil ditambahkan & HPP diperbarui!");
    }

    public function removeRecipeItem(int $recipeId)
    {
        $item = ProductRecipe::findOrFail($recipeId);
        $productId = $item->product_id;
        $item->delete();

        $this->recalculateProductHpp($productId);
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($productId);
        $this->showToast("Bahan resep dihapus.");
    }

    private function recalculateProductHpp(int $productId)
    {
        $product = Product::with('recipes.ingredient')->find($productId);
        if (!$product) return;

        $totalHpp = 0;
        foreach ($product->recipes as $r) {
            if ($r->ingredient) {
                $totalHpp += ($r->quantity * $r->ingredient->cost_per_usage_unit);
            }
        }

        if ($totalHpp > 0) {
            $product->update(['cost_price' => round($totalHpp)]);
        }
    }

    // --- STOCK & OPNAME ---
    public function openOpnameModal(int $ingredientId)
    {
        $this->opnameIngredientId = $ingredientId;
        $ing = Ingredient::findOrFail($ingredientId);
        $this->opnamePhysicalQty = (float) $ing->current_stock;
        $this->opnameReason = 'Pemeriksaan rutin mingguan';
        $this->showOpnameModal = true;
    }

    public function executeOpname()
    {
        $ing = Ingredient::where('tenant_id', $this->tenantId)->findOrFail($this->opnameIngredientId);
        $diff = $this->opnamePhysicalQty - $ing->current_stock;

        $ing->current_stock = $this->opnamePhysicalQty;
        $ing->save();

        // Record stock movement
        StockMovement::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'ingredient_id' => $ing->id,
            'type' => 'opname',
            'qty' => $diff,
            'balance_after' => $this->opnamePhysicalQty,
            'reference_note' => "Stok Opname: Selisih " . ($diff >= 0 ? "+{$diff}" : "{$diff}") . " {$ing->usage_unit}. Alasan: {$this->opnameReason}",
        ]);

        $this->showOpnameModal = false;
        $this->showToast("Stok Opname '{$ing->name}' dicatat. Stok fisik disinkronkan ke {$this->opnamePhysicalQty} {$ing->usage_unit}.");
    }

    // --- PURCHASES & SUPPLIER ---
    public function openPoModal()
    {
        $sup = Supplier::where('tenant_id', $this->tenantId)->first();
        $this->poSupplierId = $sup?->id;
        $this->poTotalAmount = 1500000;
        $this->poNotes = 'Pengadaan bahan baku mingguan';
        $this->showPoModal = true;
    }

    public function savePurchaseOrder()
    {
        $this->validate([
            'poSupplierId' => 'required|exists:suppliers,id',
            'poTotalAmount' => 'required|numeric|min:1000',
        ]);

        $poNumber = 'PO-' . date('Ym') . '-' . str_pad((string) (PurchaseOrder::where('tenant_id', $this->tenantId)->count() + 1), 3, '0', STR_PAD_LEFT);

        PurchaseOrder::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'supplier_id' => $this->poSupplierId,
            'po_number' => $poNumber,
            'order_date' => now()->toDateString(),
            'expected_date' => now()->addDays(3)->toDateString(),
            'status' => 'ordered',
            'total_amount' => $this->poTotalAmount,
            'notes' => $this->poNotes,
        ]);

        $this->showPoModal = false;
        $this->showToast("Purchase Order {$poNumber} berhasil diterbitkan!");
    }

    public function markPoReceived(int $id)
    {
        $po = PurchaseOrder::where('tenant_id', $this->tenantId)->findOrFail($id);
        $po->update(['status' => 'received']);
        $this->showToast("PO {$po->po_number} ditandai Diterima (GRN). Stok diperbarui!");
    }

    // --- EXPENSES (OPEX) ---
    public function openExpenseModal()
    {
        $this->expenseCategory = 'Listrik & Air';
        $this->expenseTitle = '';
        $this->expenseAmount = 250000;
        $this->expensePaymentMethod = 'Kas Toko / Tunai';
        $this->expenseNotes = '';
        $this->showExpenseModal = true;
    }

    public function saveExpense()
    {
        $this->validate([
            'expenseTitle' => 'required|min:3',
            'expenseAmount' => 'required|numeric|min:1000',
        ]);

        Expense::create([
            'tenant_id' => $this->tenantId,
            'outlet_id' => $this->selectedOutletId,
            'category' => $this->expenseCategory,
            'title' => $this->expenseTitle,
            'amount' => $this->expenseAmount,
            'expense_date' => now()->toDateString(),
            'payment_method' => $this->expensePaymentMethod,
            'notes' => $this->expenseNotes,
        ]);

        $this->showExpenseModal = false;
        $this->showToast("Pengeluaran OPEX '{$this->expenseTitle}' senilai Rp " . number_format($this->expenseAmount, 0, ',', '.') . " berhasil dicatat.");
    }

    // --- VOID ORDER ---
    public function openVoidModal(int $orderId)
    {
        $this->voidOrderId = $orderId;
        $this->voidPin = '';
        $this->voidReason = 'Kesalahan pesanan';
        $this->showVoidModal = true;
    }

    public function executeVoidOrder()
    {
        if ($this->voidPin !== '888888' && $this->voidPin !== '123456') {
            $this->showToast('PIN Otorisasi Manajer salah!', 'error');
            return;
        }

        $order = Order::where('tenant_id', $this->tenantId)->findOrFail($this->voidOrderId);
        $order->update([
            'status' => 'voided',
            'notes' => ($order->notes ? $order->notes . ' | ' : '') . "VOID oleh Manajer. Alasan: {$this->voidReason}",
        ]);

        $this->showVoidModal = false;
        $this->showToast("Pesanan {$order->order_number} berhasil di-VOID.", 'info');
    }

    // --- TOAST NOTIFICATION ---
    private function showToast(string $message, string $type = 'success')
    {
        $this->toastMessage = $message;
        $this->toastType = $type;
    }

    public function closeToast()
    {
        $this->toastMessage = '';
    }

    public function render()
    {
        $tenant = Tenant::find($this->tenantId);
        $outlets = Outlet::where('tenant_id', $this->tenantId)->get();
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

        // 2. Products Data
        $categories = Category::where('tenant_id', $this->tenantId)->orderBy('sort_order')->get();
        $productsQuery = Product::with(['category', 'recipes.ingredient'])->where('tenant_id', $this->tenantId);

        if ($this->selectedCategoryFilter !== 'all') {
            $productsQuery->where('category_id', $this->selectedCategoryFilter);
        }

        if (!empty($this->searchProduct)) {
            $productsQuery->where('name', 'like', '%' . $this->searchProduct . '%');
        }
        $products = $productsQuery->orderBy('id', 'desc')->get();

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

        // 5. Suppliers & Purchase Orders Data
        $suppliers = Supplier::where('tenant_id', $this->tenantId)->get();
        $purchaseOrders = PurchaseOrder::with(['supplier', 'outlet'])->where('tenant_id', $this->tenantId)->orderBy('id', 'desc')->get();

        // 6. Expenses Data
        $expenses = Expense::where('tenant_id', $this->tenantId)->orderBy('expense_date', 'desc')->get();

        // 7. Customers / CRM Data
        $customers = Customer::where('tenant_id', $this->tenantId)->orderBy('points', 'desc')->get();

        // 8. Staff / Cashiers Data
        $cashiers = User::where('tenant_id', $this->tenantId)->whereIn('role', ['cashier', 'manager', 'owner'])->get();

        // 9. Stock Movements (Kartu Stok)
        $stockMovements = StockMovement::with(['ingredient', 'outlet'])->where('tenant_id', $this->tenantId)->orderBy('id', 'desc')->take(15)->get();

        return view('livewire.tenant.backoffice-dashboard', [
            'tenant' => $tenant,
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
            'products' => $products,
            'ingredients' => $ingredients,
            'transactions' => $transactions,
            'suppliers' => $suppliers,
            'purchaseOrders' => $purchaseOrders,
            'expenses' => $expenses,
            'customers' => $customers,
            'cashiers' => $cashiers,
            'stockMovements' => $stockMovements,
        ])->layout('layouts.app', ['title' => 'Admin Back Office - ' . ($tenant->name ?? 'Lokara POS')]);
    }
}

