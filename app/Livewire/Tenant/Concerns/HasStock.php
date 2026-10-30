<?php

namespace App\Livewire\Tenant\Concerns;

use App\Models\Ingredient;
use App\Models\StockMovement;

trait HasStock
{
    public bool $showOpnameModal = false;
    public ?int $opnameIngredientId = null;
    public float $opnamePhysicalQty = 0;
    public string $opnameReason = 'Pemeriksaan rutin mingguan';

    public function openOpnameModal(int $ingredientId): void
    {
        $this->opnameIngredientId = $ingredientId;
        $ing = Ingredient::findOrFail($ingredientId);
        $this->opnamePhysicalQty = (float) $ing->current_stock;
        $this->opnameReason = 'Pemeriksaan rutin mingguan';
        $this->showOpnameModal = true;
    }

    public function executeOpname(): void
    {
        $ing = Ingredient::where('tenant_id', $this->tenantId)->findOrFail($this->opnameIngredientId);
        $diff = $this->opnamePhysicalQty - $ing->current_stock;

        $ing->current_stock = $this->opnamePhysicalQty;
        $ing->save();

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
}

