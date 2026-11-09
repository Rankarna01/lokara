<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class ExpenseCategory extends Model
{
    protected $guarded = [];

    /**
     * Kategori awal yang dibuat otomatis untuk tenant yang belum punya kategori.
     *
     * @var array<int, string>
     */
    public const DEFAULT_NAMES = [
        'Sewa Tempat',
        'Gaji Karyawan',
        'Listrik & Air',
        'Marketing',
        'Pemeliharaan',
        'Operasional',
        'Lainnya',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Daftar nama kategori biaya milik tenant: kategori tersimpan, ditambah kategori
     * yang pernah dipakai pada pengeluaran lama agar data lama tetap bisa dipilih.
     *
     * @return array<int, string>
     */
    public static function namesForTenant(int $tenantId): array
    {
        if (! static::where('tenant_id', $tenantId)->exists()) {
            foreach (self::DEFAULT_NAMES as $name) {
                static::firstOrCreate(['tenant_id' => $tenantId, 'name' => $name]);
            }
        }

        $savedNames = static::where('tenant_id', $tenantId)->pluck('name');
        $usedNames = Expense::where('tenant_id', $tenantId)->distinct()->pluck('category');

        /** @var Collection<int, string> $names */
        $names = $savedNames->merge($usedNames)->filter()->unique();

        return $names->sortBy(fn (string $name) => mb_strtolower($name))->values()->all();
    }
}
