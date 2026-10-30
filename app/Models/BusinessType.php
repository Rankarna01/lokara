<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'default_settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public static function getDefaultPresetModules(string $slug): array
    {
        return match ($slug) {
            'fnb' => [
                'Resep & Bill of Materials (BOM) Gram/ml',
                'Manajemen Meja & Cetak Dapur/Bar',
                'Peringatan Bahan Kedaluwarsa',
            ],
            'retail' => [
                'Barcode Scanning Cepat (EAN-13)',
                'Varian Ukuran & Warna',
                'Multi-Gudang & Grosir',
            ],
            'salon' => [
                'Komisi Pegawai / Kapster',
                'Layanan Jasa & Perawatan',
            ],
            'laundry' => [
                'Layanan Kiloan & Satuan',
                'Pelacakan Status Cuci-Kering-Setrika',
            ],
            'grosir' => [
                'Harga Bertingkat Grosir',
                'Piutang Penjualan Tempo',
            ],
            default => [
                'Kasir Cepat & Struk WhatsApp',
                'Manajemen Stok Standar',
            ],
        };
    }

    public function getPresetModulesAttribute(): array
    {
        if (isset($this->default_settings['preset_modules']) && is_array($this->default_settings['preset_modules'])) {
            return $this->default_settings['preset_modules'];
        }

        return static::getDefaultPresetModules($this->slug);
    }
}

