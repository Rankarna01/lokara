<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait HasStock
{
    // Sub-view toggle: 'catalog' (Saldo Persediaan Produk) vs 'ledger' (Kartu Mutasi & Audit)
    public string $stockActiveSubTab = 'catalog';
    public string $stockProductStatusFilter = 'all'; // 'all', 'safe', 'low', 'empty'

    // Filter & Search Properties
    public string $stockSearch = '';
    public string $stockTypeFilter = 'all'; // 'all', 'opname', 'sale_pos', 'purchase', 'waste', 'adjustment'
    public string $stockItemTypeFilter = 'all'; // 'all', 'product', 'ingredient'
    public string $stockDateFilter = 'all'; // 'all', 'today', 'yesterday', '7_days', '30_days', 'this_month', 'custom'
    public ?string $stockStartDate = null;
    public ?string $stockStartTime = '00:00';
    public ?string $stockEndDate = null;
    public ?string $stockEndTime = '23:59';

    // Opname Modal State
    public bool $showOpnameModal = false;
    public string $opnameItemType = 'product'; // 'product' (salon/retail direct) or 'ingredient' (f&b)
    public ?int $opnameProductId = null;
    public ?int $opnameIngredientId = null;
    public float $opnameCurrentStock = 0;
    public float $opnamePhysicalQty = 0;
    public string $opnameReasonCategory = 'Pemeriksaan Rutin';
    public string $opnameReason = 'Pemeriksaan rutin berkala display & gudang';
    public ?string $opnameRecordedAt = null; // Exact date and time Y-m-d\TH:i
    public ?int $opnameStaffId = null;

    public function openOpnameModal(?int $id = null, ?string $itemType = null): void
    {
        $tenant = Tenant::with('businessType')->find($this->tenantId);
        $isSalon = $tenant && ($tenant->businessType?->slug === 'salon' || $tenant->business_type === 'salon');

        // For salon & barbershop, default to direct products
        if ($itemType) {
            $this->opnameItemType = $itemType;
        } else {
            $this->opnameItemType = $isSalon ? 'product' : 'ingredient';
        }

        if ($this->opnameItemType === 'product') {
            $products = Product::where('tenant_id', $this->tenantId)
                ->where('type', 'item')
                ->where('is_active', true)
                ->get();

            if ($products->isEmpty()) {
                // Fallback to any active product if no 'item' type specifically
                $products = Product::where('tenant_id', $this->tenantId)->where('is_active', true)->get();
            }

            if ($id && $products->contains('id', $id)) {
                $target = $products->firstWhere('id', $id);
            } else {
                $target = $products->first();
            }

            if ($target) {
                $this->opnameProductId = $target->id;
                $this->opnameCurrentStock = (float) ($target->stock_quantity ?? 0);
            } else {
                $this->opnameProductId = null;
                $this->opnameCurrentStock = 0;
            }
            $this->opnameIngredientId = null;
        } else {
            $ingredients = Ingredient::where('tenant_id', $this->tenantId)->get();
            if ($id && $ingredients->contains('id', $id)) {
                $target = $ingredients->firstWhere('id', $id);
            } else {
                $target = $ingredients->first();
            }

            if ($target) {
                $this->opnameIngredientId = $target->id;
                $this->opnameCurrentStock = (float) ($target->current_stock ?? 0);
            } else {
                $this->opnameIngredientId = null;
                $this->opnameCurrentStock = 0;
            }
            $this->opnameProductId = null;
        }

        $this->opnamePhysicalQty = $this->opnameCurrentStock;
        $this->opnameReasonCategory = 'Pemeriksaan Rutin';
        $this->opnameReason = 'Pemeriksaan rutin berkala display & gudang';
        $this->opnameRecordedAt = Carbon::now()->format('Y-m-d\TH:i');
        $this->opnameStaffId = Auth::id();
        $this->showOpnameModal = true;
    }

    public function updatedOpnameItemType(string $value): void
    {
        $this->openOpnameModal(null, $value);
    }

    public function updatedOpnameProductId(int $productId): void
    {
        $product = Product::where('tenant_id', $this->tenantId)->find($productId);
        if ($product) {
            $this->opnameCurrentStock = (float) ($product->stock_quantity ?? 0);
            $this->opnamePhysicalQty = $this->opnameCurrentStock;
        }
    }

    public function updatedOpnameIngredientId(int $ingredientId): void
    {
        $ingredient = Ingredient::where('tenant_id', $this->tenantId)->find($ingredientId);
        if ($ingredient) {
            $this->opnameCurrentStock = (float) ($ingredient->current_stock ?? 0);
            $this->opnamePhysicalQty = $this->opnameCurrentStock;
        }
    }

    public function setOpnameReasonPreset(string $category, string $defaultNote): void
    {
        $this->opnameReasonCategory = $category;
        $this->opnameReason = $defaultNote;
    }

    public function executeOpname(): void
    {
        $this->validate([
            'opnamePhysicalQty' => 'required|numeric|min:0',
            'opnameReason' => 'required|string|min:3',
            'opnameRecordedAt' => 'required|date',
        ]);

        $recordedAt = !empty($this->opnameRecordedAt) ? Carbon::parse($this->opnameRecordedAt) : Carbon::now();
        $diff = round($this->opnamePhysicalQty - $this->opnameCurrentStock, 2);
        $diffSign = $diff >= 0 ? "+{$diff}" : "{$diff}";
        $staff = User::find($this->opnameStaffId ?? Auth::id());
        $staffName = $staff ? $staff->name : 'Petugas Toko';

        $outletId = $this->selectedOutletId;
        if (!$outletId) {
            $outletId = Tenant::find($this->tenantId)?->outlets()->first()?->id ?? 1;
        }

        if ($this->opnameItemType === 'product') {
            if (!$this->opnameProductId) {
                $this->addError('opnameProductId', 'Pilih produk langsung yang akan di-opname.');
                return;
            }

            $product = Product::where('tenant_id', $this->tenantId)->findOrFail($this->opnameProductId);
            $product->stock_quantity = $this->opnamePhysicalQty;
            $product->save();

            $unit = $product->unit ?: 'pcs';
            $refNote = "Stok Opname Produk: Selisih {$diffSign} {$unit}. Kategori: {$this->opnameReasonCategory}. Catatan: {$this->opnameReason} (PIC: {$staffName})";

            StockMovement::create([
                'tenant_id' => $this->tenantId,
                'outlet_id' => $outletId,
                'product_id' => $product->id,
                'ingredient_id' => null,
                'user_id' => $staff?->id,
                'type' => 'opname',
                'qty' => $diff,
                'balance_after' => $this->opnamePhysicalQty,
                'reference_note' => $refNote,
                'created_at' => $recordedAt,
                'updated_at' => $recordedAt,
            ]);

            $this->showToast("Stok Opname '{$product->name}' berhasil dicatat ({$recordedAt->format('d/m/Y H:i')}). Stok fisik disinkronkan ke {$this->opnamePhysicalQty} {$unit} (Selisih: {$diffSign}).");
        } else {
            if (!$this->opnameIngredientId) {
                $this->addError('opnameIngredientId', 'Pilih bahan baku yang akan di-opname.');
                return;
            }

            $ing = Ingredient::where('tenant_id', $this->tenantId)->findOrFail($this->opnameIngredientId);
            $ing->current_stock = $this->opnamePhysicalQty;
            $ing->save();

            $unit = $ing->usage_unit ?: 'gram';
            $refNote = "Stok Opname Bahan: Selisih {$diffSign} {$unit}. Kategori: {$this->opnameReasonCategory}. Catatan: {$this->opnameReason} (PIC: {$staffName})";

            StockMovement::create([
                'tenant_id' => $this->tenantId,
                'outlet_id' => $outletId,
                'product_id' => null,
                'ingredient_id' => $ing->id,
                'user_id' => $staff?->id,
                'type' => 'opname',
                'qty' => $diff,
                'balance_after' => $this->opnamePhysicalQty,
                'reference_note' => $refNote,
                'created_at' => $recordedAt,
                'updated_at' => $recordedAt,
            ]);

            $this->showToast("Stok Opname Bahan '{$ing->name}' berhasil dicatat ({$recordedAt->format('d/m/Y H:i')}). Stok fisik disinkronkan ke {$this->opnamePhysicalQty} {$unit} (Selisih: {$diffSign}).");
        }

        $this->showOpnameModal = false;
    }

    public function setStockSubTab(string $subTab): void
    {
        $this->stockActiveSubTab = $subTab;
    }

    public function viewProductMovements(int $productId): void
    {
        $product = Product::where('tenant_id', $this->tenantId)->find($productId);
        if ($product) {
            $this->stockActiveSubTab = 'ledger';
            $this->stockItemTypeFilter = 'product';
            $this->stockSearch = $product->name;
            $this->showToast("Menampilkan riwayat kartu mutasi untuk '{$product->name}'.", 'info');
        }
    }

    public function resetStockFilters(): void
    {
        $this->stockSearch = '';
        $this->stockTypeFilter = 'all';
        $this->stockItemTypeFilter = 'all';
        $this->stockDateFilter = 'all';
        $this->stockStartDate = null;
        $this->stockStartTime = '00:00';
        $this->stockEndDate = null;
        $this->stockEndTime = '23:59';
    }
}
