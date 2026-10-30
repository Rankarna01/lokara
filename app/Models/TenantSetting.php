<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public static function get(int $tenantId, string $key, $default = null)
    {
        $setting = static::where('tenant_id', $tenantId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(int $tenantId, string $key, $value, string $group = 'general')
    {
        return static::updateOrCreate(
            ['tenant_id' => $tenantId, 'key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }
}

