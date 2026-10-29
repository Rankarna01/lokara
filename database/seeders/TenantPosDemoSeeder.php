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
        User::updateOrCreate(
            ['email' => 'dina@kopiruangpagi.id'],
            [
                'name' => 'Dina Putri',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'phone' => '081234567800',
                'is_active' => true,
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
    }
}

