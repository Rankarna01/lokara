<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantBackofficeDemoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
        if (!$tenant) {
            return;
        }

        $mainOutlet = Outlet::where('tenant_id', $tenant->id)->first();
        if (!$mainOutlet) {
            $mainOutlet = Outlet::create([
                'tenant_id' => $tenant->id,
                'name' => 'Outlet Utama',
                'slug' => 'outlet-utama',
                'phone' => '081234567890',
                'address' => 'Jl. Panglima Polim No. 12, Jakarta Selatan',
                'tax_rate' => 11.00,
                'service_rate' => 0.00,
                'is_active' => true,
            ]);
        }

        // 1. Update Users with tenant_id & outlet_id
        User::where('email', 'rian@kopiruangpagi.id')->update([
            'tenant_id' => $tenant->id,
            'outlet_id' => $mainOutlet->id,
            'role' => 'owner',
            'pin_code' => '888888',
        ]);

        User::where('email', 'dina@kopiruangpagi.id')->update([
            'tenant_id' => $tenant->id,
            'outlet_id' => $mainOutlet->id,
            'role' => 'cashier',
            'pin_code' => '123456',
        ]);

        User::updateOrCreate(
            ['email' => 'budi@kopiruangpagi.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567801',
                'tenant_id' => $tenant->id,
                'outlet_id' => $mainOutlet->id,
                'pin_code' => '654321',
                'is_active' => true,
            ]
        );

        // 2. Update products with realistic HPP (Cost Price)
        $costMap = [
            'Es Kopi Susu Aren' => 7500,
            'Americano' => 4200,
            'Cafe Latte' => 8800,
            'Matcha Latte' => 9500,
            'Croissant Butter' => 7000,
            'Roti Panggang Cokelat' => 6800,
            'Nasi Ayam Sambal Matah' => 14500,
            'Kentang Goreng' => 6000,
            'Air Mineral' => 3000,
        ];

        foreach ($costMap as $name => $cost) {
            Product::where('tenant_id', $tenant->id)
                ->where('name', $name)
                ->update(['cost_price' => $cost]);
        }

        // 3. Raw Materials / Ingredients
        $rawMaterials = [
            [
                'name' => 'Biji Kopi Espresso Blend',
                'code' => 'RAW-001',
                'category' => 'Biji Kopi',
                'purchase_unit' => 'Kg',
                'usage_unit' => 'Gram',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 180,
                'current_stock' => 8500, // 8.5 kg
                'minimum_stock' => 3000, // 3 kg
                'storage_location' => 'Bar Depan',
            ],
            [
                'name' => 'Susu Fresh Milk UHT',
                'code' => 'RAW-002',
                'category' => 'Dairy & Susu',
                'purchase_unit' => 'Liter',
                'usage_unit' => 'ml',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 19,
                'current_stock' => 32000, // 32 liter
                'minimum_stock' => 15000, // 15 liter
                'storage_location' => 'Chiller Bar',
            ],
            [
                'name' => 'Gula Aren Cair Organik',
                'code' => 'RAW-003',
                'category' => 'Sirup & Pemanis',
                'purchase_unit' => 'Liter',
                'usage_unit' => 'ml',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 35,
                'current_stock' => 12000, // 12 liter
                'minimum_stock' => 5000,
                'storage_location' => 'Bar Depan',
            ],
            [
                'name' => 'Pure Uji Matcha Powder',
                'code' => 'RAW-004',
                'category' => 'Bubuk Minuman',
                'purchase_unit' => 'Kg',
                'usage_unit' => 'Gram',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 450,
                'current_stock' => 1800, // 1.8 kg
                'minimum_stock' => 1000,
                'storage_location' => 'Bar Depan',
            ],
            [
                'name' => 'Cup Cold 16oz & Lid',
                'code' => 'RAW-005',
                'category' => 'Packaging',
                'purchase_unit' => 'Dus (500 pcs)',
                'usage_unit' => 'Pcs',
                'conversion_factor' => 500,
                'cost_per_usage_unit' => 850,
                'current_stock' => 450,
                'minimum_stock' => 200,
                'storage_location' => 'Gudang Belakang',
            ],
            [
                'name' => 'Fillet Ayam Marinasi',
                'code' => 'RAW-006',
                'category' => 'Bahan Makanan',
                'purchase_unit' => 'Kg',
                'usage_unit' => 'Gram',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 55,
                'current_stock' => 2200, // 2.2 kg (LOW STOCK ALERT!)
                'minimum_stock' => 5000, // 5 kg
                'storage_location' => 'Freezer Dapur',
            ],
            [
                'name' => 'Kentang Beku Shoestring',
                'code' => 'RAW-007',
                'category' => 'Bahan Makanan',
                'purchase_unit' => 'Kg',
                'usage_unit' => 'Gram',
                'conversion_factor' => 1000,
                'cost_per_usage_unit' => 38,
                'current_stock' => 18000, // 18 kg
                'minimum_stock' => 10000,
                'storage_location' => 'Freezer Dapur',
            ],
        ];

        $ingredientModels = [];
        foreach ($rawMaterials as $mat) {
            $ingredientModels[$mat['name']] = Ingredient::updateOrCreate(
                ['tenant_id' => $tenant->id, 'code' => $mat['code']],
                array_merge($mat, ['tenant_id' => $tenant->id])
            );
        }

        // 4. Recipes (BOM) for key items
        $esKopi = Product::where('tenant_id', $tenant->id)->where('name', 'Es Kopi Susu Aren')->first();
        if ($esKopi && isset($ingredientModels['Biji Kopi Espresso Blend'])) {
            ProductRecipe::updateOrCreate(
                ['product_id' => $esKopi->id, 'ingredient_id' => $ingredientModels['Biji Kopi Espresso Blend']->id],
                ['quantity' => 18] // 18 gram
            );
            ProductRecipe::updateOrCreate(
                ['product_id' => $esKopi->id, 'ingredient_id' => $ingredientModels['Susu Fresh Milk UHT']->id],
                ['quantity' => 120] // 120 ml
            );
            ProductRecipe::updateOrCreate(
                ['product_id' => $esKopi->id, 'ingredient_id' => $ingredientModels['Gula Aren Cair Organik']->id],
                ['quantity' => 25] // 25 ml
            );
            ProductRecipe::updateOrCreate(
                ['product_id' => $esKopi->id, 'ingredient_id' => $ingredientModels['Cup Cold 16oz & Lid']->id],
                ['quantity' => 1] // 1 pcs
            );
        }

        $americano = Product::where('tenant_id', $tenant->id)->where('name', 'Americano')->first();
        if ($americano && isset($ingredientModels['Biji Kopi Espresso Blend'])) {
            ProductRecipe::updateOrCreate(
                ['product_id' => $americano->id, 'ingredient_id' => $ingredientModels['Biji Kopi Espresso Blend']->id],
                ['quantity' => 18]
            );
            ProductRecipe::updateOrCreate(
                ['product_id' => $americano->id, 'ingredient_id' => $ingredientModels['Cup Cold 16oz & Lid']->id],
                ['quantity' => 1]
            );
        }

        // 5. Suppliers
        $supKopi = Supplier::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'PT Kopi Nusantara Makmur'],
            [
                'contact_person' => 'Hendro Setiawan',
                'phone' => '081122334455',
                'email' => 'sales@kopinusantara.co.id',
                'address' => 'Kawasan Industri Sentul Kav. 12, Bogor',
                'terms_days' => 14,
                'is_active' => true,
            ]
        );

        $supDairy = Supplier::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'CV Dairy Fresh Sejahtera'],
            [
                'contact_person' => 'Siti Nurhaliza',
                'phone' => '081299887766',
                'email' => 'order@dairyfresh.com',
                'address' => 'Jl. Pasar Minggu Raya No. 88, Jakarta Selatan',
                'terms_days' => 7,
                'is_active' => true,
            ]
        );

        $supPack = Supplier::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Toko Kemasan Jaya Mandiri'],
            [
                'contact_person' => 'Aheng Salim',
                'phone' => '081765432100',
                'email' => 'sales@kemasanjaya.id',
                'address' => 'Glodok Plaza Blok B-14, Jakarta Barat',
                'terms_days' => 0, // COD
                'is_active' => true,
            ]
        );

        // 6. Purchase Orders (PO)
        PurchaseOrder::updateOrCreate(
            ['tenant_id' => $tenant->id, 'po_number' => 'PO-202610-001'],
            [
                'outlet_id' => $mainOutlet->id,
                'supplier_id' => $supKopi->id,
                'order_date' => now()->subDays(5)->toDateString(),
                'expected_date' => now()->subDays(3)->toDateString(),
                'status' => 'received',
                'total_amount' => 2450000,
                'notes' => 'Pengadaan Espresso Blend 15 Kg',
            ]
        );

        PurchaseOrder::updateOrCreate(
            ['tenant_id' => $tenant->id, 'po_number' => 'PO-202610-002'],
            [
                'outlet_id' => $mainOutlet->id,
                'supplier_id' => $supPack->id,
                'order_date' => now()->subDay()->toDateString(),
                'expected_date' => now()->addDays(2)->toDateString(),
                'status' => 'ordered',
                'total_amount' => 1250000,
                'notes' => 'Restock Cup Cold 16oz 3 Dus',
            ]
        );

        // 7. Expenses (OPEX Beban Operasional Toko)
        $expensesData = [
            [
                'category' => 'Sewa Tempat',
                'title' => 'Sewa Kios Outlet Bulan Oktober 2026',
                'amount' => 4500000,
                'expense_date' => now()->startOfMonth()->toDateString(),
                'payment_method' => 'Transfer Bank',
                'notes' => 'Dibayar lunas per awal bulan',
            ],
            [
                'category' => 'Gaji Karyawan',
                'title' => 'Gaji 2 Orang Barista & Kasir Shift Oktober',
                'amount' => 5000000,
                'expense_date' => now()->startOfMonth()->addDays(24)->toDateString(),
                'payment_method' => 'Transfer Bank',
                'notes' => 'Gaji pokok Dina & Budi',
            ],
            [
                'category' => 'Listrik & Air',
                'title' => 'Tagihan Listrik PLN & Air PDAM Oktober',
                'amount' => 1350000,
                'expense_date' => now()->subDays(9)->toDateString(),
                'payment_method' => 'Transfer Bank',
                'notes' => 'Daya 5500VA',
            ],
            [
                'category' => 'Marketing',
                'title' => 'Instagram Ads & Promo Food Delivery',
                'amount' => 450000,
                'expense_date' => now()->subDays(4)->toDateString(),
                'payment_method' => 'Transfer Bank',
                'notes' => 'Kampanye Weekend Promo',
            ],
            [
                'category' => 'Pemeliharaan',
                'title' => 'Servis & Kalibrasi Mesin Espresso Nuova Simonelli',
                'amount' => 350000,
                'expense_date' => now()->subDays(2)->toDateString(),
                'payment_method' => 'Kas Toko / Tunai',
                'notes' => 'Ganti gasket head group',
            ],
        ];

        foreach ($expensesData as $exp) {
            Expense::updateOrCreate(
                ['tenant_id' => $tenant->id, 'title' => $exp['title']],
                array_merge($exp, [
                    'tenant_id' => $tenant->id,
                    'outlet_id' => $mainOutlet->id,
                ])
            );
        }

        // 8. Stock Movement sample ledger
        if (isset($ingredientModels['Biji Kopi Espresso Blend'])) {
            StockMovement::updateOrCreate(
                ['tenant_id' => $tenant->id, 'ingredient_id' => $ingredientModels['Biji Kopi Espresso Blend']->id, 'type' => 'purchase'],
                [
                    'outlet_id' => $mainOutlet->id,
                    'qty' => 10000,
                    'balance_after' => 10000,
                    'reference_note' => 'Penerimaan PO-202610-001 dari PT Kopi Nusantara',
                ]
            );
            StockMovement::updateOrCreate(
                ['tenant_id' => $tenant->id, 'ingredient_id' => $ingredientModels['Biji Kopi Espresso Blend']->id, 'type' => 'sale_pos'],
                [
                    'outlet_id' => $mainOutlet->id,
                    'qty' => -1500,
                    'balance_after' => 8500,
                    'reference_note' => 'Penjualan POS harian akumulasi',
                ]
            );
        }
    }
}

