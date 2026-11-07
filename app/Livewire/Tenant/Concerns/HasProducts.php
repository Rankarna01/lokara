<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

trait HasProducts
{
    public bool $showProductModal = false;

    public ?int $editingProductId = null;

    public string $productName = '';

    public ?int $productCategoryId = null;

    public string $productCategoryTag = 'Kopi • Hot / Ice';

    public float $productBasePrice = 0;

    public ?float $productCostPrice = 0;

    public string $productImageUrl = '';

    public $productImageFile = null;

    public float $productStockQuantity = 0;

    public float $productMinStock = 5;

    public string $productUnit = 'pcs';

    public bool $productTrackStock = true;

    public bool $productIsActive = true;

    public bool $productIsAvailable = true;

    public function openNewProductModal(): void
    {
        $this->resetProductForm();
        $this->showProductModal = true;
    }

    public function editProduct(int $id): void
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $this->editingProductId = $product->id;
        $this->productName = $product->name;
        $this->productCategoryId = $product->category_id;
        $this->productCategoryTag = $product->category_tag ?? 'Kopi • Ice';
        $this->productBasePrice = (float) $product->base_price;
        $this->productCostPrice = (float) $product->cost_price;
        $this->productStockQuantity = (float) ($product->stock_quantity ?? 0);
        $this->productMinStock = (float) ($product->minimum_stock ?? 5);
        $this->productUnit = $product->unit ?? 'pcs';
        $this->productTrackStock = (bool) ($product->track_stock ?? true);
        $this->productImageUrl = $product->image_url ?? '';
        $this->productImageFile = null;
        $this->productIsActive = (bool) $product->is_active;
        $this->productIsAvailable = (bool) $product->is_available;
        $this->showProductModal = true;
    }

    public function removeProductImage(): void
    {
        $this->productImageFile = null;
        $this->productImageUrl = '';
    }

    public function saveProduct(): void
    {
        $this->validate([
            'productName' => 'required|min:2',
            'productBasePrice' => 'required|numeric|min:0',
            'productCostPrice' => 'nullable|numeric|min:0',
            'productImageFile' => 'nullable|image|max:3072',
        ]);

        if ($this->productImageFile) {
            $path = $this->productImageFile->store('products', 'public');
            $this->productImageUrl = '/storage/'.$path;
        }

        if ($this->editingProductId) {
            $product = Product::where('tenant_id', $this->tenantId)->findOrFail($this->editingProductId);
            $product->update([
                'name' => $this->productName,
                'category_id' => $this->productCategoryId,
                'category_tag' => $this->productCategoryTag,
                'base_price' => $this->productBasePrice,
                'cost_price' => (float) ($this->productCostPrice ?? 0),
                'stock_quantity' => $this->productStockQuantity,
                'minimum_stock' => $this->productMinStock,
                'unit' => $this->productUnit ?: 'pcs',
                'track_stock' => $this->productTrackStock,
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
                'type' => 'item',
                'name' => $this->productName,
                'slug' => Str::slug($this->productName).'-'.rand(100, 999),
                'sku' => 'PRD-'.strtoupper(Str::random(5)),
                'barcode' => '899'.rand(100000000, 999999999),
                'base_price' => $this->productBasePrice,
                'cost_price' => (float) ($this->productCostPrice ?? 0),
                'stock_quantity' => $this->productStockQuantity,
                'minimum_stock' => $this->productMinStock,
                'unit' => $this->productUnit ?: 'pcs',
                'track_stock' => $this->productTrackStock,
                'image_url' => $this->productImageUrl ?: 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&h=300&q=80',
                'is_active' => $this->productIsActive,
                'is_available' => $this->productIsAvailable,
            ]);
            $this->showToast("Produk '{$this->productName}' berhasil ditambahkan ke katalog!");
        }

        $this->showProductModal = false;
        $this->resetProductForm();
    }

    public function toggleProductAvailability(int $id): void
    {
        $product = Product::where('tenant_id', $this->tenantId)->findOrFail($id);
        $product->is_available = ! $product->is_available;
        $product->save();
        $status = $product->is_available ? 'Tersedia' : 'Habis / Stok Kosong';
        $this->showToast("Status {$product->name} diubah menjadi '{$status}'");
    }

    private function resetProductForm(): void
    {
        $this->editingProductId = null;
        $this->productName = '';
        $this->productCategoryId = Category::where('tenant_id', $this->tenantId)->first()?->id;
        $this->productCategoryTag = 'Kopi • Ice';
        $this->productBasePrice = 20000;
        $this->productCostPrice = 7000;
        $this->productStockQuantity = 0;
        $this->productMinStock = 5;
        $this->productUnit = 'pcs';
        $this->productTrackStock = true;
        $this->productImageFile = null;
        $this->productImageUrl = '';
        $this->productIsActive = true;
        $this->productIsAvailable = true;
    }
}
