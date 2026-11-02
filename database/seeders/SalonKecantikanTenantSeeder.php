<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
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

        $owner = User::updateOrCreate(
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
        $this->seedRiwayatTransaksi($tenant, $outlet, $owner);
    }

    private function seedRiwayatTransaksi(Tenant $tenant, Outlet $outlet, User $owner): void
    {
        $services = Product::where('tenant_id', $tenant->id)->where('type', 'service')->get()->keyBy('name');

        $transactions = [
            [
                'order_number' => 'ORD-PBC-001',
                'days_ago' => 6,
                'time' => '10:15',
                'discount' => 0,
                'payment_method' => 'cash',
                'items' => [['Facial Glow Brightening', 1], ['Hair Spa Aromatherapy', 1]],
            ],
            [
                'order_number' => 'ORD-PBC-002',
                'days_ago' => 4,
                'time' => '13:40',
                'discount' => 15000,
                'payment_method' => 'qris',
                'items' => [['Manicure & Gel Polish', 1]],
            ],
            [
                'order_number' => 'ORD-PBC-003',
                'days_ago' => 2,
                'time' => '11:05',
                'discount' => 0,
                'payment_method' => 'qris',
                'items' => [['Makeup Natural Harian', 1], ['Facial Glow Brightening', 2]],
            ],
            [
                'order_number' => 'ORD-PBC-004',
                'days_ago' => 1,
                'time' => '16:20',
                'discount' => 0,
                'payment_method' => 'cash',
                'items' => [['Hair Spa Aromatherapy', 2]],
            ],
            [
                'order_number' => 'ORD-PBC-005',
                'days_ago' => 0,
                'time' => '09:30',
                'discount' => 0,
                'payment_method' => 'qris',
                'items' => [['Manicure & Gel Polish', 1], ['Hair Spa Aromatherapy', 1]],
            ],
        ];

        foreach ($transactions as $data) {
            $createdAt = now()->subDays($data['days_ago'])->setTimeFromTimeString($data['time']);

            $subtotal = 0;
            foreach ($data['items'] as [$serviceName, $qty]) {
                $subtotal += (float) $services[$serviceName]->base_price * $qty;
            }
            $total = $subtotal - $data['discount'];

            $order = Order::updateOrCreate(
                ['tenant_id' => $tenant->id, 'order_number' => $data['order_number']],
                [
                    'outlet_id' => $outlet->id,
                    'user_id' => $owner->id,
                    'order_type' => 'salon_order',
                    'subtotal' => $subtotal,
                    'discount_amount' => $data['discount'],
                    'tax_amount' => 0,
                    'service_amount' => 0,
                    'total_amount' => $total,
                    'status' => 'completed',
                    'payment_status' => 'paid',
                    'paid_at' => $createdAt,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );

            foreach ($data['items'] as [$serviceName, $qty]) {
                $service = $services[$serviceName];
                OrderItem::updateOrCreate(
                    ['order_id' => $order->id, 'product_id' => $service->id],
                    [
                        'tenant_id' => $tenant->id,
                        'product_name' => $service->name,
                        'variant_name' => 'Standar Sesi',
                        'qty' => $qty,
                        'unit_price' => $service->base_price,
                        'subtotal' => (float) $service->base_price * $qty,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]
                );
            }

            OrderPayment::updateOrCreate(
                ['order_id' => $order->id, 'reference_number' => 'PAY-'.$data['order_number']],
                [
                    'tenant_id' => $tenant->id,
                    'payment_method' => $data['payment_method'],
                    'amount' => $total,
                    'cash_tendered' => $total,
                    'change_due' => 0,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }
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
