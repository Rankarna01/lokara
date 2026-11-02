<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Expense;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
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

        $secondOutlet = Outlet::where('tenant_id', $tenant->id)->where('slug', 'cabang-kemang')->first();
        if (!$secondOutlet) {
            $secondOutlet = Outlet::create([
                'tenant_id' => $tenant->id,
                'name' => 'Cabang Kemang',
                'slug' => 'cabang-kemang',
                'phone' => '081298765432',
                'address' => 'Jl. Kemang Raya No. 45, Jakarta Selatan',
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

        User::updateOrCreate(
            ['email' => 'dina@kopiruangpagi.id'],
            [
                'name' => 'Dina Safitri',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant->id,
                'outlet_id' => $mainOutlet->id,
                'role' => 'cashier',
                'phone' => '081234567802',
                'pin_code' => '123456',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi@kopiruangpagi.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567801',
                'tenant_id' => $tenant->id,
                'outlet_id' => $secondOutlet->id,
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

        // 9. Discounts for Kopi Ruang Pagi
        Discount::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Promo Diskon Pelajar & Mahasiswa 15%'],
            [
                'code' => 'STUDENT15',
                'type' => 'percentage',
                'value' => 15,
                'min_purchase' => 30000,
                'max_discount' => 15000,
                'applicable_to' => 'all',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->addMonths(2)->toDateString(),
                'is_active' => true,
                'max_usage' => 200,
                'usage_count' => 34,
                'notes' => 'Tunjukkan kartu tanda mahasiswa/pelajar saat pembayaran',
            ]
        );

        Discount::updateOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'Potongan Langsung Rp 10.000'],
            [
                'code' => 'HEMAT10K',
                'type' => 'fixed',
                'value' => 10000,
                'min_purchase' => 50000,
                'applicable_to' => 'all',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->endOfMonth()->addMonth()->toDateString(),
                'is_active' => true,
                'max_usage' => 100,
                'usage_count' => 18,
                'notes' => 'Minimal belanja 50 ribu',
            ]
        );

        // 10. Seed Demo Data for Studio Rona (Salon & Barbershop)
        $salonTenant = Tenant::where('slug', 'studiorona')->first();
        if ($salonTenant) {
            $salonOutlet = Outlet::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'slug' => 'studio-rona-gubeng'],
                [
                    'name' => 'Studio Rona - Gubeng',
                    'phone' => '081377889901',
                    'address' => 'Jl. Raya Gubeng No. 88, Surabaya',
                    'tax_rate' => 11.00,
                    'service_rate' => 5.00,
                    'is_active' => true,
                ]
            );

            // Salon Owner User
            User::updateOrCreate(
                ['email' => 'contact@studiorona.id'],
                [
                    'name' => 'Rona Melinda',
                    'password' => Hash::make('password'),
                    'role' => 'owner',
                    'phone' => '081377889900',
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'pin_code' => '999999',
                    'is_active' => true,
                ]
            );

            // Salon Kapster / Stylist
            User::updateOrCreate(
                ['email' => 'doni.barber@studiorona.id'],
                [
                    'name' => 'Doni Barber (Senior Kapster)',
                    'password' => Hash::make('password'),
                    'role' => 'cashier',
                    'phone' => '081377889911',
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'pin_code' => '111111',
                    'is_active' => true,
                ]
            );

            // Categories
            $catCut = Category::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'slug' => 'haircut-shaving'],
                ['name' => 'Haircut & Shaving', 'icon' => 'fa-scissors', 'sort_order' => 1]
            );

            $catTreatment = Category::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'slug' => 'treatment-spa'],
                ['name' => 'Treatment & Spa', 'icon' => 'fa-spa', 'sort_order' => 2]
            );

            $catColoring = Category::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'slug' => 'coloring-styling'],
                ['name' => 'Coloring & Styling', 'icon' => 'fa-paintbrush', 'sort_order' => 3]
            );

            $catRetail = Category::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'slug' => 'produk-perawatan'],
                ['name' => 'Produk Retail', 'icon' => 'fa-bottle-droplet', 'sort_order' => 4]
            );

            // Services
            $servicesData = [
                [
                    'name' => 'Gentleman Haircut & Styling',
                    'category_id' => $catCut->id,
                    'category_tag' => 'Haircut • Pria',
                    'base_price' => 60000,
                    'cost_price' => 5000,
                    'duration_minutes' => 40,
                    'commission_type' => 'fixed',
                    'commission_value' => 20000,
                    'image_url' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Kids Haircut (Balita & Anak)',
                    'category_id' => $catCut->id,
                    'category_tag' => 'Haircut • Anak',
                    'base_price' => 45000,
                    'cost_price' => 3000,
                    'duration_minutes' => 30,
                    'commission_type' => 'percentage',
                    'commission_value' => 30, // 30%
                    'image_url' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Beard Shaving & Hot Towel Massage',
                    'category_id' => $catCut->id,
                    'category_tag' => 'Shaving • Grooming',
                    'base_price' => 35000,
                    'cost_price' => 4000,
                    'duration_minutes' => 25,
                    'commission_type' => 'fixed',
                    'commission_value' => 12000,
                    'image_url' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Creambath Spa & Head Massage',
                    'category_id' => $catTreatment->id,
                    'category_tag' => 'Treatment • Relaksasi',
                    'base_price' => 85000,
                    'cost_price' => 15000,
                    'duration_minutes' => 60,
                    'commission_type' => 'percentage',
                    'commission_value' => 25, // 25%
                    'image_url' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Hair Coloring Premium Black / Brown',
                    'category_id' => $catColoring->id,
                    'category_tag' => 'Coloring • Fashion',
                    'base_price' => 150000,
                    'cost_price' => 35000,
                    'duration_minutes' => 90,
                    'commission_type' => 'percentage',
                    'commission_value' => 30, // 30%
                    'image_url' => 'https://images.unsplash.com/photo-1562322140-8baeececf3df?auto=format&fit=crop&w=300&h=300&q=80',
                ],
            ];

            foreach ($servicesData as $srv) {
                Product::updateOrCreate(
                    ['tenant_id' => $salonTenant->id, 'name' => $srv['name']],
                    array_merge($srv, [
                        'tenant_id' => $salonTenant->id,
                        'type' => 'service',
                        'slug' => \Illuminate\Support\Str::slug($srv['name']),
                        'sku' => 'SRV-' . strtoupper(\Illuminate\Support\Str::random(4)),
                        'is_active' => true,
                        'is_available' => true,
                    ])
                );
            }

            // Retail Items in Salon
            $pPomade = Product::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Pomade Strong Matte Clay 100g'],
                [
                    'category_id' => $catRetail->id,
                    'category_tag' => 'Produk • Styling',
                    'type' => 'item',
                    'slug' => 'pomade-matte-clay-100g',
                    'sku' => 'PRD-POMADE',
                    'base_price' => 85000,
                    'cost_price' => 45000,
                    'stock_quantity' => 24,
                    'minimum_stock' => 5,
                    'unit' => 'jar',
                    'track_stock' => true,
                    'image_url' => 'https://images.unsplash.com/photo-1597354984706-aec992b7d526?auto=format&fit=crop&w=300&h=300&q=80',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            $pTonic = Product::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Tonic Hair & Scalp Refresher 100ml'],
                [
                    'category_id' => $catRetail->id,
                    'category_tag' => 'Produk • Perawatan',
                    'type' => 'item',
                    'slug' => 'tonic-hair-refresher-100ml',
                    'sku' => 'PRD-TONIC',
                    'base_price' => 65000,
                    'cost_price' => 30000,
                    'stock_quantity' => 15,
                    'minimum_stock' => 5,
                    'unit' => 'botol',
                    'track_stock' => true,
                    'image_url' => 'https://images.unsplash.com/photo-1608248597359-5975e533037f?auto=format&fit=crop&w=300&h=300&q=80',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            $pShampoo = Product::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Shampoo Anti-Dandruff Professional 500ml'],
                [
                    'category_id' => $catRetail->id,
                    'category_tag' => 'Produk • Perawatan',
                    'type' => 'item',
                    'slug' => 'shampoo-anti-dandruff-pro-500ml',
                    'sku' => 'PRD-SHAMPOO',
                    'base_price' => 110000,
                    'cost_price' => 55000,
                    'stock_quantity' => 8,
                    'minimum_stock' => 5,
                    'unit' => 'botol',
                    'track_stock' => true,
                    'image_url' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=300&h=300&q=80',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            $pSerum = Product::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Serum Keratin Hair Repair 50ml'],
                [
                    'category_id' => $catRetail->id,
                    'category_tag' => 'Produk • Treatment',
                    'type' => 'item',
                    'slug' => 'serum-keratin-hair-repair-50ml',
                    'sku' => 'PRD-SERUM',
                    'base_price' => 95000,
                    'cost_price' => 48000,
                    'stock_quantity' => 3,
                    'minimum_stock' => 5,
                    'unit' => 'botol',
                    'track_stock' => true,
                    'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&h=300&q=80',
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            // Salon Discounts
            Discount::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Promo Diskon Grand Opening 20%'],
                [
                    'code' => 'OPENING20',
                    'type' => 'percentage',
                    'value' => 20,
                    'min_purchase' => 50000,
                    'max_discount' => 25000,
                    'applicable_to' => 'services',
                    'start_date' => now()->startOfMonth()->toDateString(),
                    'end_date' => now()->endOfMonth()->addMonths(1)->toDateString(),
                    'is_active' => true,
                    'max_usage' => 150,
                    'usage_count' => 42,
                    'notes' => 'Khusus layanan potong rambut dan treatment',
                ]
            );

            Discount::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Potongan Langsung Haircut Rp 15.000'],
                [
                    'code' => 'CUKUR15K',
                    'type' => 'fixed',
                    'value' => 15000,
                    'min_purchase' => 60000,
                    'applicable_to' => 'services',
                    'start_date' => now()->startOfMonth()->toDateString(),
                    'end_date' => now()->endOfMonth()->addMonths(2)->toDateString(),
                    'is_active' => true,
                    'max_usage' => 80,
                    'usage_count' => 19,
                    'notes' => 'Potongan Rp 15.000 untuk paket Gentleman Cut',
                ]
            );

            // Salon Suppliers & Purchase Orders
            $supLoreal = Supplier::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => "PT L'Oreal Professional Indonesia"],
                [
                    'contact_person' => 'Maya Indriani',
                    'phone' => '081233445566',
                    'email' => 'order@loreal-pro.co.id',
                    'address' => 'Jl. TB Simatupang No. 88, Jakarta Selatan',
                    'terms_days' => 14,
                    'is_active' => true,
                ]
            );

            $supBarber = Supplier::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'CV Barbershop Supplies Nusantara'],
                [
                    'contact_person' => 'Budi Santoso',
                    'phone' => '081988776655',
                    'email' => 'sales@barbersupplies.id',
                    'address' => 'Komp. Ruko Darmo Park II Blok B, Surabaya',
                    'terms_days' => 7,
                    'is_active' => true,
                ]
            );

            $supHanduk = Supplier::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'name' => 'Toko Handuk & Aksesoris Salon Berkah'],
                [
                    'contact_person' => 'Bu Hj. Nur',
                    'phone' => '081344556677',
                    'email' => 'toko.berkah@gmail.com',
                    'address' => 'Pasar Turi Baru Blok C-12, Surabaya',
                    'terms_days' => 0,
                    'is_active' => true,
                ]
            );

            // Purchase Orders for Studio Rona
            PurchaseOrder::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'po_number' => 'PO-' . date('Ym') . '-001'],
                [
                    'outlet_id' => $salonOutlet->id,
                    'supplier_id' => $supLoreal->id,
                    'order_date' => now()->subDays(5)->toDateString(),
                    'expected_date' => now()->subDays(2)->toDateString(),
                    'status' => 'received',
                    'total_amount' => 2450000,
                    'notes' => 'Restock Shampoo Anti-Dandruff 500ml (20 btl) & Serum Keratin (15 btl)',
                ]
            );

            PurchaseOrder::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'po_number' => 'PO-' . date('Ym') . '-002'],
                [
                    'outlet_id' => $salonOutlet->id,
                    'supplier_id' => $supBarber->id,
                    'order_date' => now()->subDays(1)->toDateString(),
                    'expected_date' => now()->addDays(2)->toDateString(),
                    'status' => 'ordered',
                    'total_amount' => 1650000,
                    'notes' => 'Restock Pomade Matte Clay 100g (30 pcs) & Shaving Foam',
                ]
            );

            PurchaseOrder::updateOrCreate(
                ['tenant_id' => $salonTenant->id, 'po_number' => 'PO-' . date('Ym') . '-003'],
                [
                    'outlet_id' => $salonOutlet->id,
                    'supplier_id' => $supHanduk->id,
                    'order_date' => now()->subDays(10)->toDateString(),
                    'expected_date' => now()->subDays(8)->toDateString(),
                    'status' => 'received',
                    'total_amount' => 450000,
                    'notes' => 'Handuk hitam microfiber 50 pcs untuk area cuci rambut',
                ]
            );

            // Seed realistic Stock Movements for Salon Retail Items with clear timestamps
            $salonOwner = User::where('tenant_id', $salonTenant->id)->first();
            $movementsData = [
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pPomade->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'opname',
                    'qty' => 30,
                    'balance_after' => 30,
                    'reference_note' => 'Stok Opname Awal: Audit saldo fisik display rak styling pomade',
                    'created_at' => now()->subDays(6)->setTime(9, 15, 0),
                    'updated_at' => now()->subDays(6)->setTime(9, 15, 0),
                ],
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pPomade->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'sale_pos',
                    'qty' => -6,
                    'balance_after' => 24,
                    'reference_note' => 'Penjualan POS: 6 jar pomade ke pelanggan reguler',
                    'created_at' => now()->subDays(2)->setTime(16, 45, 0),
                    'updated_at' => now()->subDays(2)->setTime(16, 45, 0),
                ],
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pTonic->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'opname',
                    'qty' => 15,
                    'balance_after' => 15,
                    'reference_note' => 'Stok Opname: Pemeriksaan stok mingguan rak perawatan rambut',
                    'created_at' => now()->subDays(3)->setTime(10, 30, 0),
                    'updated_at' => now()->subDays(3)->setTime(10, 30, 0),
                ],
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pShampoo->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'purchase',
                    'qty' => 10,
                    'balance_after' => 10,
                    'reference_note' => 'Penerimaan PO-202610-001 dari PT L\'Oreal Professional Indonesia',
                    'created_at' => now()->subDays(5)->setTime(14, 20, 0),
                    'updated_at' => now()->subDays(5)->setTime(14, 20, 0),
                ],
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pShampoo->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'sale_pos',
                    'qty' => -2,
                    'balance_after' => 8,
                    'reference_note' => 'Penjualan Kasir POS: 2 botol shampoo anti-dandruff',
                    'created_at' => now()->subDays(1)->setTime(15, 10, 0),
                    'updated_at' => now()->subDays(1)->setTime(15, 10, 0),
                ],
                [
                    'tenant_id' => $salonTenant->id,
                    'outlet_id' => $salonOutlet->id,
                    'product_id' => $pSerum->id,
                    'user_id' => $salonOwner?->id,
                    'type' => 'opname',
                    'qty' => -2,
                    'balance_after' => 3,
                    'reference_note' => 'Stok Opname Produk: Selisih -2 botol. Alasan: 1 botol tester display habis, 1 botol kemasan pecah (PIC: ' . ($salonOwner?->name ?? 'Staf') . ')',
                    'created_at' => now()->subHours(4),
                    'updated_at' => now()->subHours(4),
                ],
            ];

            foreach ($movementsData as $mv) {
                StockMovement::updateOrCreate(
                    [
                        'tenant_id' => $mv['tenant_id'],
                        'product_id' => $mv['product_id'],
                        'type' => $mv['type'],
                        'created_at' => $mv['created_at'],
                    ],
                    $mv
                );
            }

            // Seed Realistic Completed Orders for Studio Rona (Services & Retail Products)
            $pGentleman = Product::where('tenant_id', $salonTenant->id)->where('name', 'like', '%Gentleman Haircut%')->first();
            $pKids = Product::where('tenant_id', $salonTenant->id)->where('name', 'like', '%Kids Haircut%')->first();
            $pShaving = Product::where('tenant_id', $salonTenant->id)->where('name', 'like', '%Beard Shaving%')->first();
            $pCreambath = Product::where('tenant_id', $salonTenant->id)->where('name', 'like', '%Creambath Spa%')->first();
            $pColoring = Product::where('tenant_id', $salonTenant->id)->where('name', 'like', '%Hair Coloring%')->first();

            $salonOrdersData = [
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-001',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Barber 01',
                    'created_at' => now()->subDays(3)->setTime(11, 30),
                    'items' => [
                        ['product' => $pGentleman, 'qty' => 1, 'price' => 60000],
                        ['product' => $pPomade, 'qty' => 1, 'price' => 85000],
                    ],
                ],
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-002',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Spa 01',
                    'created_at' => now()->subDays(2)->setTime(14, 15),
                    'items' => [
                        ['product' => $pCreambath, 'qty' => 1, 'price' => 85000],
                        ['product' => $pTonic, 'qty' => 1, 'price' => 65000],
                    ],
                ],
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-003',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Barber 02',
                    'created_at' => now()->subDays(2)->setTime(16, 45),
                    'items' => [
                        ['product' => $pGentleman, 'qty' => 2, 'price' => 60000],
                        ['product' => $pShaving, 'qty' => 1, 'price' => 35000],
                    ],
                ],
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-004',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Coloring 01',
                    'created_at' => now()->subDays(1)->setTime(13, 00),
                    'items' => [
                        ['product' => $pColoring, 'qty' => 1, 'price' => 150000],
                        ['product' => $pSerum, 'qty' => 1, 'price' => 95000],
                        ['product' => $pShampoo, 'qty' => 1, 'price' => 110000],
                    ],
                ],
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-005',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Barber 01',
                    'created_at' => now()->subHours(5),
                    'items' => [
                        ['product' => $pGentleman, 'qty' => 1, 'price' => 60000],
                        ['product' => $pPomade, 'qty' => 2, 'price' => 85000],
                    ],
                ],
                [
                    'order_number' => 'ORD-RONA-' . date('Ym') . '-006',
                    'order_type' => 'salon_order',
                    'table_number' => 'Kursi Barber 03',
                    'created_at' => now()->subHours(2),
                    'items' => [
                        ['product' => $pKids, 'qty' => 1, 'price' => 45000],
                    ],
                ],
            ];

            foreach ($salonOrdersData as $oData) {
                $subtotal = 0;
                foreach ($oData['items'] as $it) {
                    $subtotal += ($it['price'] * $it['qty']);
                }
                $tax = round($subtotal * 0.11);
                $total = $subtotal + $tax;

                $order = Order::updateOrCreate(
                    [
                        'tenant_id' => $salonTenant->id,
                        'order_number' => $oData['order_number'],
                    ],
                    [
                        'outlet_id' => $salonOutlet->id,
                        'user_id' => $salonOwner?->id ?? 1,
                        'order_type' => $oData['order_type'],
                        'table_number' => $oData['table_number'],
                        'subtotal' => $subtotal,
                        'discount_amount' => 0,
                        'tax_amount' => $tax,
                        'service_amount' => 0,
                        'total_amount' => $total,
                        'status' => 'completed',
                        'payment_status' => 'paid',
                        'paid_at' => $oData['created_at'],
                        'created_at' => $oData['created_at'],
                        'updated_at' => $oData['created_at'],
                    ]
                );

                foreach ($oData['items'] as $it) {
                    if ($it['product']) {
                        OrderItem::updateOrCreate(
                            [
                                'order_id' => $order->id,
                                'product_id' => $it['product']->id,
                            ],
                            [
                                'tenant_id' => $salonTenant->id,
                                'product_name' => $it['product']->name,
                                'variant_name' => $it['product']->type === 'service' ? 'Standar Sesi' : 'Reguler',
                                'qty' => $it['qty'],
                                'unit_price' => $it['price'],
                                'subtotal' => $it['price'] * $it['qty'],
                                'created_at' => $oData['created_at'],
                                'updated_at' => $oData['created_at'],
                            ]
                        );
                    }
                }

                OrderPayment::updateOrCreate(
                    [
                        'order_id' => $order->id,
                        'reference_number' => 'PAY-' . $order->order_number,
                    ],
                    [
                        'tenant_id' => $salonTenant->id,
                        'payment_method' => 'qris',
                        'amount' => $total,
                        'cash_tendered' => $total,
                        'change_due' => 0,
                        'created_at' => $oData['created_at'],
                        'updated_at' => $oData['created_at'],
                    ]
                );
            }

            // Seed Expenses for Studio Rona
            $salonExpenses = [
                [
                    'category' => 'Sewa Tempat',
                    'title' => 'Sewa Ruko Studio Rona Gubeng Oktober 2026',
                    'amount' => 3500000,
                    'expense_date' => now()->startOfMonth()->toDateString(),
                    'payment_method' => 'Transfer Bank',
                    'notes' => 'Pembayaran sewa ruko lantai 1',
                ],
                [
                    'category' => 'Listrik & Air',
                    'title' => 'Tagihan Listrik PLN 3 AC & Water Heater Salon',
                    'amount' => 850000,
                    'expense_date' => now()->subDays(8)->toDateString(),
                    'payment_method' => 'Transfer Bank',
                    'notes' => 'Listrik token 5500 VA',
                ],
                [
                    'category' => 'Operasional',
                    'title' => 'Laundry Handuk Microfiber & Jubah Keramas Mingguan',
                    'amount' => 220000,
                    'expense_date' => now()->subDays(3)->toDateString(),
                    'payment_method' => 'Kas Toko / Tunai',
                    'notes' => '120 pcs handuk salon',
                ],
            ];

            foreach ($salonExpenses as $se) {
                Expense::updateOrCreate(
                    [
                        'tenant_id' => $salonTenant->id,
                        'title' => $se['title'],
                    ],
                    array_merge($se, [
                        'tenant_id' => $salonTenant->id,
                        'outlet_id' => $salonOutlet->id,
                    ])
                );
            }
        }
    }
}

