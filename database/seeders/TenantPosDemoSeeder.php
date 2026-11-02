<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantPosDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get Kopi Ruang Pagi tenant
        $tenant = Tenant::where('slug', 'kopiruangpagi')->first() ?? Tenant::first();
        if (! $tenant) {
            return;
        }

        // 2. Outlets
        $mainOutlet = Outlet::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'outlet-utama'],
            [
                'name' => 'Outlet Utama',
                'phone' => '081234567890',
                'address' => 'Jl. Panglima Polim No. 12, Jakarta Selatan',
                'tax_rate' => 11.00,
                'service_rate' => 0.00,
                'is_active' => true,
            ]
        );

        Outlet::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'cabang-tebet'],
            [
                'name' => 'Cabang Tebet',
                'phone' => '081234567891',
                'address' => 'Jl. Tebet Raya No. 45, Jakarta Selatan',
                'tax_rate' => 11.00,
                'is_active' => true,
            ]
        );

        // 3. Cashier User (Matching Image 3: Dina Putri - Kasir)
        $cashier = User::updateOrCreate(
            ['email' => 'dina@kopiruangpagi.id'],
            [
                'name' => 'Dina Putri',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567800',
                'tenant_id' => $tenant->id,
                'outlet_id' => $mainOutlet->id,
                'is_active' => true,
            ]
        );

        // Previous Closed Shift Record for Audit
        \App\Models\Shift::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'outlet_id' => $mainOutlet->id,
                'status' => 'closed',
            ],
            [
                'user_id' => $cashier->id,
                'opened_at' => now()->subDay()->startOfDay()->addHours(8),
                'closed_at' => now()->subDay()->startOfDay()->addHours(16),
                'opening_cash' => 200000.00,
                'expected_cash' => 200000.00,
                'actual_cash' => 200000.00,
                'difference' => 0.00,
                'status' => 'closed',
                'notes' => 'Shift Kemarin Selesai & Direkonsiliasi',
            ]
        );

        // 4. Categories (Matching Image 3)
        $catKopi = Category::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'kopi'],
            [
                'name' => 'Kopi',
                'icon' => 'fa-mug-hot',
                'sort_order' => 1,
            ]
        );

        $catNonKopi = Category::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'non-kopi'],
            [
                'name' => 'Non-Kopi',
                'icon' => 'fa-glass-water',
                'sort_order' => 2,
            ]
        );

        $catMakanan = Category::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'makanan'],
            [
                'name' => 'Makanan',
                'icon' => 'fa-utensils',
                'sort_order' => 3,
            ]
        );

        $catSnack = Category::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'snack'],
            [
                'name' => 'Snack',
                'icon' => 'fa-cookie-bite',
                'sort_order' => 4,
            ]
        );

        // 5. Products (Matching 9 products on Image 3)
        $productsData = [
            [
                'name' => 'Es Kopi Susu Aren',
                'category_id' => $catKopi->id,
                'category_tag' => 'Kopi • Ice',
                'price' => 22000,
                'image_url' => 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => [
                    ['name' => 'Regular', 'price' => 22000],
                    ['name' => 'Large', 'price' => 28000],
                ]
            ],
            [
                'name' => 'Americano',
                'category_id' => $catKopi->id,
                'category_tag' => 'Kopi • Hot / Ice',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => [
                    ['name' => 'Hot', 'price' => 18000],
                    ['name' => 'Iced', 'price' => 20000],
                ]
            ],
            [
                'name' => 'Cafe Latte',
                'category_id' => $catKopi->id,
                'category_tag' => 'Kopi • Hot / Ice',
                'price' => 25000,
                'image_url' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => [
                    ['name' => 'Regular Hot', 'price' => 25000],
                    ['name' => 'Large Hot', 'price' => 31000],
                    ['name' => 'Iced Latte', 'price' => 27000],
                ]
            ],
            [
                'name' => 'Matcha Latte',
                'category_id' => $catNonKopi->id,
                'category_tag' => 'Non-Kopi • Hot / Ice',
                'price' => 26000,
                'image_url' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => [
                    ['name' => 'Hot Matcha', 'price' => 26000],
                    ['name' => 'Iced Matcha', 'price' => 28000],
                ]
            ],
            [
                'name' => 'Croissant Butter',
                'category_id' => $catSnack->id,
                'category_tag' => 'Snack • Pastry',
                'price' => 16000,
                'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => [
                    ['name' => 'Original', 'price' => 16000],
                    ['name' => 'Almond Topping', 'price' => 22000],
                ]
            ],
            [
                'name' => 'Roti Panggang Cokelat',
                'category_id' => $catSnack->id,
                'category_tag' => 'Snack • Roti',
                'price' => 19000,
                'image_url' => 'https://images.unsplash.com/photo-1584776296944-ab6fb57b0bdd?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => []
            ],
            [
                'name' => 'Nasi Ayam Sambal Matah',
                'category_id' => $catMakanan->id,
                'category_tag' => 'Makanan • Nasi',
                'price' => 32000,
                'image_url' => 'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => []
            ],
            [
                'name' => 'Kentang Goreng',
                'category_id' => $catSnack->id,
                'category_tag' => 'Snack • Gorengan',
                'price' => 18000,
                'image_url' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => []
            ],
            [
                'name' => 'Air Mineral',
                'category_id' => $catNonKopi->id,
                'category_tag' => 'Non-Kopi • Dingin',
                'price' => 8000,
                'image_url' => 'https://images.unsplash.com/photo-1560023907-5f339617ea30?auto=format&fit=crop&w=300&h=300&q=80',
                'variants' => []
            ],
        ];

        foreach ($productsData as $item) {
            $product = Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => Str::slug($item['name'])],
                [
                    'category_id' => $item['category_id'],
                    'category_tag' => $item['category_tag'],
                    'name' => $item['name'],
                    'sku' => 'PRD-' . strtoupper(Str::random(5)),
                    'barcode' => '899' . rand(100000000, 999999999),
                    'base_price' => $item['price'],
                    'image_url' => $item['image_url'],
                    'is_active' => true,
                    'is_available' => true,
                ]
            );

            if (! empty($item['variants'])) {
                foreach ($item['variants'] as $var) {
                    ProductVariant::updateOrCreate(
                        ['tenant_id' => $tenant->id, 'product_id' => $product->id, 'name' => $var['name']],
                        [
                            'final_price' => $var['price'],
                            'additional_price' => $var['price'] - $product->base_price,
                        ]
                    );
                }
            }
        }

        // 6. Seed Demo Data Khusus Salon Kecantikan (Putri Beauty Care & Studio Rona)
        $this->seedSalonDemo();

        // 7. Seed Demo Data Khusus Barbershop (Gentleman Barbershop)
        $this->seedBarbershopDemo();
    }

    protected function seedSalonDemo(): void
    {
        $salonTenants = Tenant::whereIn('slug', ['putribeautycare', 'studiorona'])->get();

        foreach ($salonTenants as $tenant) {
            $outlet = Outlet::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'outlet-utama'],
                [
                    'name' => 'Outlet Utama',
                    'phone' => '081377889901',
                    'address' => 'Jl. Senopati No. 28, Kebayoran Baru, Jakarta Selatan',
                    'tax_rate' => 11.00,
                    'service_rate' => 0.00,
                    'is_active' => true,
                ]
            );

            // Assign Owner & Cashier Users to Tenant & Outlet
            if ($tenant->slug === 'putribeautycare') {
                User::where('email', 'hapis@putribeautycare.site')->update([
                    'tenant_id' => $tenant->id,
                    'outlet_id' => $outlet->id,
                    'pin_code' => '888888',
                ]);

                User::where('email', 'dina@putribeauty.com')->update([
                    'tenant_id' => $tenant->id,
                    'outlet_id' => $outlet->id,
                    'pin_code' => '123456',
                ]);
            }

            if ($tenant->slug === 'studiorona') {
                User::where('email', 'contact@studiorona.id')->update([
                    'tenant_id' => $tenant->id,
                    'outlet_id' => $outlet->id,
                    'pin_code' => '888888',
                ]);
            }

            // Kategori Khusus Template Salon Kecantikan
            $catFacial = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'facial-treatment'],
                ['name' => 'Facial Treatment', 'icon' => 'fa-spa', 'sort_order' => 1]
            );

            $catHair = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'hair-treatment'],
                ['name' => 'Hair Treatment', 'icon' => 'fa-scissors', 'sort_order' => 2]
            );

            $catNail = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'nail-art'],
                ['name' => 'Nail Art', 'icon' => 'fa-hand-sparkles', 'sort_order' => 3]
            );

            $catBody = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'body-spa'],
                ['name' => 'Body Spa', 'icon' => 'fa-hot-tub-person', 'sort_order' => 4]
            );

            $catSkincare = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'produk-perawatan'],
                ['name' => 'Produk Perawatan', 'icon' => 'fa-bottle-droplet', 'sort_order' => 5]
            );

            $salonItems = [
                [
                    'name' => 'Facial Ice Treatment',
                    'category_id' => $catFacial->id,
                    'category_tag' => 'Facial • Glowing',
                    'price' => 60000,
                    'cost_price' => 10000,
                    'type' => 'service',
                    'duration' => 45,
                    'comm_type' => 'percentage',
                    'comm_val' => 10,
                    'image' => 'https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Deep Cleansing Glow Facial',
                    'category_id' => $catFacial->id,
                    'category_tag' => 'Facial • Pori',
                    'price' => 95000,
                    'cost_price' => 15000,
                    'type' => 'service',
                    'duration' => 60,
                    'comm_type' => 'percentage',
                    'comm_val' => 10,
                    'image' => 'https://images.unsplash.com/photo-1512290900672-1f5be57a55c2?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Hair Spa Aromatherapy',
                    'category_id' => $catHair->id,
                    'category_tag' => 'Hair • Spa',
                    'price' => 85000,
                    'cost_price' => 12000,
                    'type' => 'service',
                    'duration' => 60,
                    'comm_type' => 'fixed',
                    'comm_val' => 25000,
                    'image' => 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Creambath Ginseng Tradisional',
                    'category_id' => $catHair->id,
                    'category_tag' => 'Hair • Creambath',
                    'price' => 65000,
                    'cost_price' => 8000,
                    'type' => 'service',
                    'duration' => 45,
                    'comm_type' => 'fixed',
                    'comm_val' => 20000,
                    'image' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Manicure & Gel Polish',
                    'category_id' => $catNail->id,
                    'category_tag' => 'Nail • Gel',
                    'price' => 75000,
                    'cost_price' => 10000,
                    'type' => 'service',
                    'duration' => 45,
                    'comm_type' => 'percentage',
                    'comm_val' => 10,
                    'image' => 'https://images.unsplash.com/photo-1632345031435-8727f6897d53?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Nail Art Korean Style 3D',
                    'category_id' => $catNail->id,
                    'category_tag' => 'Nail • Art',
                    'price' => 110000,
                    'cost_price' => 15000,
                    'type' => 'service',
                    'duration' => 60,
                    'comm_type' => 'fixed',
                    'comm_val' => 30000,
                    'image' => 'https://images.unsplash.com/photo-1604654894610-df63bc536371?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Full Body Aromatherapy Massage',
                    'category_id' => $catBody->id,
                    'category_tag' => 'Spa • Massage',
                    'price' => 135000,
                    'cost_price' => 20000,
                    'type' => 'service',
                    'duration' => 90,
                    'comm_type' => 'fixed',
                    'comm_val' => 40000,
                    'image' => 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Serum Glowing Brightening 30ml',
                    'category_id' => $catSkincare->id,
                    'category_tag' => 'Skincare • Serum',
                    'price' => 125000,
                    'cost_price' => 65000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Sunscreen Daily Protection SPF 50',
                    'category_id' => $catSkincare->id,
                    'category_tag' => 'Skincare • Sunscreen',
                    'price' => 85000,
                    'cost_price' => 45000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Hair Tonic Ginseng Treatment 100ml',
                    'category_id' => $catSkincare->id,
                    'category_tag' => 'Perawatan • Hair Tonic',
                    'price' => 45000,
                    'cost_price' => 22000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 3000,
                    'image' => 'https://images.unsplash.com/photo-1608248597359-bb5b67286392?auto=format&fit=crop&w=300&h=300&q=80',
                ],
            ];

            foreach ($salonItems as $item) {
                Product::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => Str::slug($item['name'])],
                    [
                        'category_id' => $item['category_id'],
                        'category_tag' => $item['category_tag'],
                        'name' => $item['name'],
                        'sku' => 'SLN-' . strtoupper(Str::random(5)),
                        'barcode' => '899' . rand(100000000, 999999999),
                        'base_price' => $item['price'],
                        'cost_price' => $item['cost_price'],
                        'type' => $item['type'],
                        'duration_minutes' => $item['duration'],
                        'commission_type' => $item['comm_type'],
                        'commission_value' => $item['comm_val'],
                        'image_url' => $item['image'],
                        'is_active' => true,
                        'is_available' => true,
                    ]
                );
            }
        }
    }

    protected function seedBarbershopDemo(): void
    {
        $barberTenants = Tenant::whereIn('slug', ['gentlemanbarbershop'])->get();

        foreach ($barberTenants as $tenant) {
            $outlet = Outlet::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'outlet-utama'],
                [
                    'name' => 'Outlet Utama Barbershop',
                    'phone' => '081298765411',
                    'address' => 'Jl. Mahakam No. 15, Blok M, Jakarta Selatan',
                    'tax_rate' => 11.00,
                    'service_rate' => 0.00,
                    'is_active' => true,
                ]
            );

            // Assign Owner & Kapster Users
            User::where('email', 'bambang@gentlemanbarber.id')->update([
                'tenant_id' => $tenant->id,
                'outlet_id' => $outlet->id,
                'pin_code' => '888888',
            ]);

            User::where('email', 'doni.barber@studiorona.id')->update([
                'tenant_id' => $tenant->id,
                'outlet_id' => $outlet->id,
                'pin_code' => '111111',
            ]);

            // Kategori Khusus Template Barbershop
            $catPotong = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'potong-styling'],
                ['name' => 'Potong & Styling', 'icon' => 'fa-scissors', 'sort_order' => 1]
            );

            $catShaving = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'shaving-grooming'],
                ['name' => 'Shaving & Grooming', 'icon' => 'fa-spray-can', 'sort_order' => 2]
            );

            $catTreatment = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'treatment-cuci'],
                ['name' => 'Treatment & Cuci', 'icon' => 'fa-shower', 'sort_order' => 3]
            );

            $catPomade = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'pomade-hair-care'],
                ['name' => 'Pomade & Hair Care', 'icon' => 'fa-jar', 'sort_order' => 4]
            );

            $barberItems = [
                [
                    'name' => 'Gentleman Haircut + Wash + Hot Towel',
                    'category_id' => $catPotong->id,
                    'category_tag' => 'Haircut • Pria',
                    'price' => 50000,
                    'cost_price' => 5000,
                    'type' => 'service',
                    'duration' => 40,
                    'comm_type' => 'fixed',
                    'comm_val' => 20000,
                    'image' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Classic Fade & Beard Trim',
                    'category_id' => $catPotong->id,
                    'category_tag' => 'Haircut • Fade',
                    'price' => 45000,
                    'cost_price' => 4000,
                    'type' => 'service',
                    'duration' => 35,
                    'comm_type' => 'fixed',
                    'comm_val' => 15000,
                    'image' => 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Royal Hot Towel Shave',
                    'category_id' => $catShaving->id,
                    'category_tag' => 'Shaving • Kumis & Jenggot',
                    'price' => 35000,
                    'cost_price' => 4000,
                    'type' => 'service',
                    'duration' => 25,
                    'comm_type' => 'fixed',
                    'comm_val' => 12000,
                    'image' => 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Hair Coloring Black / Espresso',
                    'category_id' => $catTreatment->id,
                    'category_tag' => 'Treatment • Cat Rambut',
                    'price' => 110000,
                    'cost_price' => 25000,
                    'type' => 'service',
                    'duration' => 60,
                    'comm_type' => 'percentage',
                    'comm_val' => 25,
                    'image' => 'https://images.unsplash.com/photo-1527799820374-dcf8d9d4a388?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Scalp Treatment Anti-Dandruff',
                    'category_id' => $catTreatment->id,
                    'category_tag' => 'Treatment • Ketombe',
                    'price' => 65000,
                    'cost_price' => 10000,
                    'type' => 'service',
                    'duration' => 45,
                    'comm_type' => 'fixed',
                    'comm_val' => 20000,
                    'image' => 'https://images.unsplash.com/photo-1519699047748-de8e457a634e?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Classic Pomade Oil-Based 100gr',
                    'category_id' => $catPomade->id,
                    'category_tag' => 'Pomade • Strong Hold',
                    'price' => 85000,
                    'cost_price' => 45000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Matte Clay Strong Hold 100gr',
                    'category_id' => $catPomade->id,
                    'category_tag' => 'Clay • Natural Finish',
                    'price' => 95000,
                    'cost_price' => 50000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 5000,
                    'image' => 'https://images.unsplash.com/photo-1608248597359-bb5b67286392?auto=format&fit=crop&w=300&h=300&q=80',
                ],
                [
                    'name' => 'Hair Tonic Ginseng Pria 150ml',
                    'category_id' => $catPomade->id,
                    'category_tag' => 'Tonic • Anti-Rontok',
                    'price' => 45000,
                    'cost_price' => 22000,
                    'type' => 'item',
                    'duration' => 0,
                    'comm_type' => 'fixed',
                    'comm_val' => 3000,
                    'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=300&h=300&q=80',
                ],
            ];

            foreach ($barberItems as $item) {
                Product::updateOrCreate(
                    ['tenant_id' => $tenant->id, 'slug' => Str::slug($item['name'])],
                    [
                        'category_id' => $item['category_id'],
                        'category_tag' => $item['category_tag'],
                        'name' => $item['name'],
                        'sku' => 'BRB-' . strtoupper(Str::random(5)),
                        'barcode' => '899' . rand(100000000, 999999999),
                        'base_price' => $item['price'],
                        'cost_price' => $item['cost_price'],
                        'type' => $item['type'],
                        'duration_minutes' => $item['duration'],
                        'commission_type' => $item['comm_type'],
                        'commission_value' => $item['comm_val'],
                        'image_url' => $item['image'],
                        'is_active' => true,
                        'is_available' => true,
                    ]
                );
            }
        }
    }
}

