<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Outlet;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SalonKecantikanTenantSeeder extends Seeder
{
    private const OWNER_EMAIL = 'arsihapis@putribeautycare.site';

    private const DEFAULT_IMAGE_URL = 'https://images.unsplash.com/photo-1560066984-138dadb4c035?auto=format&fit=crop&w=300&h=300&q=80';

    public function run(): void
    {
        $businessType = BusinessType::where('slug', 'salon')->firstOrFail();
        $plan = Plan::where('slug', 'business')->firstOrFail();

        $tenant = Tenant::updateOrCreate(
            ['slug' => 'putrybeautycare'],
            [
                'name' => 'putrybeautycare',
                'business_type_id' => $businessType->id,
                'plan_id' => $plan->id,
                'owner_name' => 'Arsi Hapis',
                'owner_email' => self::OWNER_EMAIL,
                'owner_phone' => '085835116946',
                'status' => 'active',
                'trial_ends_at' => null,
                'subscription_ends_at' => Carbon::now()->addMonths(12),
                'city' => 'Indonesia',
                'icon' => $businessType->icon ?? 'fa-spa',
            ]
        );

        $outlet = Outlet::updateOrCreate(
            ['tenant_id' => $tenant->id, 'slug' => 'putrybeautycare-utama'],
            [
                'name' => 'Putrybeautycare Utama',
                'phone' => '085835116946',
                'address' => 'Alamat outlet utama',
                'tax_rate' => 11.00,
                'service_rate' => 0.00,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => self::OWNER_EMAIL],
            [
                'name' => 'Arsi Hapis',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'phone' => '085835116946',
                'tenant_id' => $tenant->id,
                'outlet_id' => $outlet->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->seedMasterLayanan($tenant);
    }

    private function seedMasterLayanan(Tenant $tenant): void
    {
        $categories = [
            'treatment-wajah' => ['name' => 'Treatment Wajah', 'icon' => 'fa-face-smile', 'sort_order' => 1],
            'hair-treatment' => ['name' => 'Hair Treatment', 'icon' => 'fa-spa', 'sort_order' => 2],
            'nail-art' => ['name' => 'Nail Art', 'icon' => 'fa-hand-sparkles', 'sort_order' => 3],
            'makeup' => ['name' => 'Makeup', 'icon' => 'fa-palette', 'sort_order' => 4],
        ];

        $categoryIds = [];
        foreach ($categories as $slug => $data) {
            $categoryIds[$slug] = Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => $slug],
                array_merge($data, ['is_active' => true])
            )->id;
        }

        $services = [
            [
                'name' => 'Facial Glow Brightening',
                'category' => 'treatment-wajah',
                'base_price' => 150000,
                'cost_price' => 30000,
                'duration_minutes' => 60,
                'commission_type' => 'percentage',
                'commission_value' => 20,
            ],
            [
                'name' => 'Hair Spa Aromatherapy',
                'category' => 'hair-treatment',
                'base_price' => 85000,
                'cost_price' => 15000,
                'duration_minutes' => 45,
                'commission_type' => 'fixed',
                'commission_value' => 15000,
            ],
            [
                'name' => 'Manicure & Gel Polish',
                'category' => 'nail-art',
                'base_price' => 75000,
                'cost_price' => 12000,
                'duration_minutes' => 50,
                'commission_type' => 'percentage',
                'commission_value' => 25,
            ],
            [
                'name' => 'Makeup Natural Harian',
                'category' => 'makeup',
                'base_price' => 250000,
                'cost_price' => 40000,
                'duration_minutes' => 90,
                'commission_type' => 'fixed',
                'commission_value' => 50000,
            ],
        ];

        foreach ($services as $service) {
            Product::updateOrCreate(
                ['tenant_id' => $tenant->id, 'name' => $service['name']],
                [
                    'category_id' => $categoryIds[$service['category']],
                    'category_tag' => 'Layanan Salon',
                    'type' => 'service',
                    'slug' => Str::slug($service['name']),
                    'sku' => 'SRV-'.strtoupper(Str::slug($service['name'], '')).'-PBC',
                    'base_price' => $service['base_price'],
                    'cost_price' => $service['cost_price'],
                    'duration_minutes' => $service['duration_minutes'],
                    'commission_type' => $service['commission_type'],
                    'commission_value' => $service['commission_value'],
                    'image_url' => self::DEFAULT_IMAGE_URL,
                    'is_active' => true,
                    'is_available' => true,
                ]
            );
        }
    }
}
