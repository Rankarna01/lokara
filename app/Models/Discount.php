<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Discount extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
        'min_purchase' => 'float',
        'max_discount' => 'float',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateAmount(float $subtotal): float
    {
        if (!$this->is_active) {
            return 0;
        }

        if ($subtotal < $this->min_purchase) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = ($subtotal * $this->value) / 100;
            if ($this->max_discount && $this->max_discount > 0) {
                $discount = min($discount, $this->max_discount);
            }
            return round($discount);
        }

        return min($this->value, $subtotal);
    }
}
