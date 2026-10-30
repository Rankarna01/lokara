<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'duration_minutes' => 'integer',
        'commission_value' => 'decimal:2',
        'is_active' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function isService(): bool
    {
        return $this->type === 'service';
    }

    public function getEstimatedCommissionAttribute(): float
    {
        if ($this->commission_type === 'percentage') {
            return round(((float) $this->base_price * (float) $this->commission_value) / 100);
        }
        return (float) $this->commission_value;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function modifiers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ProductModifier::class, 'product_modifier_assignments');
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(ProductRecipe::class);
    }
}
