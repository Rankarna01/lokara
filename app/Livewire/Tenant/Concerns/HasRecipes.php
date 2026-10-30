<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Ingredient;
use App\Models\Product;
use App\Models\ProductRecipe;

trait HasRecipes
{
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

    public function openNewIngredientModal(): void
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

    public function saveIngredient(): void
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

    public function openRecipeModal(int $productId): void
    {
        $this->recipeProductId = $productId;
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->findOrFail($productId);
        $firstIng = Ingredient::where('tenant_id', $this->tenantId)->first();
        $this->selectedRecipeIngredientId = $firstIng?->id;
        $this->recipeQuantity = 15;
        $this->showRecipeModal = true;
    }

    public function addRecipeItem(): void
    {
        if (!$this->recipeProductId || !$this->selectedRecipeIngredientId || $this->recipeQuantity <= 0) {
            return;
        }

        ProductRecipe::updateOrCreate(
            ['product_id' => $this->recipeProductId, 'ingredient_id' => $this->selectedRecipeIngredientId],
            ['quantity' => $this->recipeQuantity]
        );

        $this->recalculateProductHpp($this->recipeProductId);

        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($this->recipeProductId);
        $this->showToast("Komposisi bahan berhasil ditambahkan & HPP diperbarui!");
    }

    public function removeRecipeItem(int $recipeId): void
    {
        $item = ProductRecipe::findOrFail($recipeId);
        $productId = $item->product_id;
        $item->delete();

        $this->recalculateProductHpp($productId);
        $this->selectedRecipeProduct = Product::with(['recipes.ingredient'])->where('tenant_id', $this->tenantId)->find($productId);
        $this->showToast("Bahan resep dihapus.");
    }

    private function recalculateProductHpp(int $productId): void
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
}

