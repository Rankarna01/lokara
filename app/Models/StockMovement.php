<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'qty' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getItemNameAttribute(): string
    {
        return $this->product?->name ?? $this->ingredient?->name ?? 'Item #' . $this->id;
    }

    public function getItemSkuAttribute(): string
    {
        return $this->product?->sku ?? $this->ingredient?->sku ?? '-';
    }

    public function getItemUnitAttribute(): string
    {
        return $this->product?->unit ?? $this->ingredient?->usage_unit ?? 'pcs';
    }

    public function getItemTypeLabelAttribute(): string
    {
        if ($this->product_id) {
            return 'Produk Langsung';
        }
        if ($this->ingredient_id) {
            return 'Bahan Baku';
        }
        return 'Item';
    }
}

