<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\BusinessType;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\PlatformActivityLog;
use App\Models\PlatformSetting;
use App\Models\Subscription;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SuperAdminPlatformSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Business Types
        $fnb = BusinessType::create([
            'name' => 'F&B (Kafe, Restoran, Bakery)',
            'slug' => 'fnb',
            'icon' => 'fa-utensils',
            'description' => 'Untuk kafe, coffee shop, restoran, bakery dengan fitur resep, bahan baku, meja, dan cetak dapur.',
            'default_settings' => [
                'has_recipes' => true,
                'has_tables' => true,
                'has_modifiers' => true,
                'default_units' => ['gram', 'ml', 'cup', 'porsi', 'pcs'],
            ],
            'is_active' => true,
        ]);

        $retail = BusinessType::create([
            'name' => 'Retail & Toko Kelontong',
            'slug' => 'retail',
            'icon' => 'fa-store',
            'description' => 'Untuk minimarket, toko baju, kelontong dengan barcode scanner cepat dan varian stok.',
            'default_settings' => [
                'has_barcode' => true,
                'has_variants' => true,
                'default_units' => ['pcs', 'pack', 'dus', 'lusin'],
            ],
            'is_active' => true,
        ]);

        $salon = BusinessType::create([
            'name' => 'Salon & Barbershop',
            'slug' => 'salon',
            'icon' => 'fa-scissors',
            'description' => 'Untuk barbershop, salon kecantikan dengan komisi kapster/terapis dan pencatatan bahan shampoo.',
            'default_settings' => [
                'has_commissions' => true,
                'has_services' => true,
            ],
            'is_active' => true,
        ]);

        $laundry = BusinessType::create([
            'name' => 'Laundry & Jasa',
            'slug' => 'laundry',
            'icon' => 'fa-shirt',
            'description' => 'Untuk laundry kiloan dan satuan dengan pelacakan status cuci, kering, dan setrika.',
            'default_settings' => [
                'has_order_status' => true,
                'default_units' => ['kg', 'pcs', 'meter'],
            ],
            'is_active' => true,
        ]);

        $grosir = BusinessType::create([
            'name' => 'Grosir & Distributor',
            'slug' => 'grosir',
            'icon' => 'fa-boxes-stacked',
            'description' => 'Untuk agen, grosir dengan harga bertingkat, penjualan tempo, dan piutang pelanggan.',
            'default_settings' => [
                'has_credit_sales' => true,
                'has_tiered_pricing' => true,
            ],
            'is_active' => true,
        ]);

        // 2. Plans
        $planTrial = Plan::create([
            'name' => 'Trial 14 Hari',
            'slug' => 'trial',
            'badge' => 'Uji Coba',
            'price_monthly' => 0,
            'price_yearly' => 0,
            'limits' => ['max_outlets' => 1, 'max_users' => 2, 'max_products' => 50],
            'features' => ['Kasir Cepat', 'Laporan Dasar', 'Struk Digital WhatsApp'],
            'is_active' => true,
        ]);

        $planBasic = Plan::create([
            'name' => 'Basic',
            'slug' => 'basic',
            'badge' => 'Pemula',
            'price_monthly' => 149000,
            'price_yearly' => 1490000,
            'limits' => ['max_outlets' => 1, 'max_users' => 5, 'max_products' => 500],
            'features' => ['Kasir Cepat', 'Manajemen Stok Sederhana', 'Multi-Metode Bayar', 'Laporan Penjualan Lengkap'],
            'is_active' => true,
        ]);

        $planPro = Plan::create([
            'name' => 'Pro',
            'slug' => 'pro',
            'badge' => 'Terpopuler',
            'price_monthly' => 299000,
            'price_yearly' => 2990000,
            'limits' => ['max_outlets' => 3, 'max_users' => 15, 'max_products' => 5000],
            'features' => ['Semua Fitur Basic', 'Modul Resep (BOM) & HPP Otomatis', 'Multi-Gudang & Transfer Stok', 'Peringatan Stok Tipis & Expired', 'Pusat Laporan Laba Rugi'],
            'is_active' => true,
            'is_featured' => true,
        ]);

        $planBusiness = Plan::create([
            'name' => 'Business',
            'slug' => 'business',
            'badge' => 'Enterprise',
            'price_monthly' => 599000,
            'price_yearly' => 5990000,
            'limits' => ['max_outlets' => 999, 'max_users' => 999, 'max_products' => 99999],
            'features' => ['Semua Fitur Pro', 'Unlimited Outlet & Kasir', 'API Terbuka & Webhook', 'Custom Domain', 'Support WhatsApp Khusus 24/7'],
            'is_active' => true,
        ]);

        // 3. Tenants (Matching Image 1 & 2)
        $t1 = Tenant::create([
            'name' => 'Kopi Ruang Pagi',
            'slug' => 'kopiruangpagi',
            'business_type_id' => $fnb->id,
            'plan_id' => $planPro->id,
            'owner_name' => 'Rian Adiputra',
            'owner_email' => 'rian@kopiruangpagi.id',
            'owner_phone' => '081234567890',
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => Carbon::now()->addMonths(6),
            'city' => 'Jakarta Selatan',
            'icon' => 'fa-mug-hot',
            'created_at' => Carbon::create(2026, 10, 28, 10, 30),
        ]);

        $t2 = Tenant::create([
            'name' => 'Toko Berkah Jaya',
            'slug' => 'tokoberkahjaya',
            'business_type_id' => $retail->id,
            'plan_id' => $planBasic->id,
            'owner_name' => 'H. Somad',
            'owner_email' => 'somad@berkahjaya.com',
            'owner_phone' => '081298765432',
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => Carbon::now()->addMonths(3),
            'city' => 'Bandung',
            'icon' => 'fa-store',
            'created_at' => Carbon::create(2026, 10, 27, 14, 15),
        ]);

        $t3 = Tenant::create([
            'name' => 'Studio Rona',
            'slug' => 'studiorona',
            'business_type_id' => $salon->id,
            'plan_id' => $planBusiness->id,
            'owner_name' => 'Rona Melinda',
            'owner_email' => 'contact@studiorona.id',
            'owner_phone' => '081377889900',
            'status' => 'trial',
            'trial_ends_at' => Carbon::now()->addDays(11),
            'subscription_ends_at' => null,
            'city' => 'Surabaya',
            'icon' => 'fa-scissors',
            'created_at' => Carbon::create(2026, 10, 26, 9, 0),
        ]);

        $t4 = Tenant::create([
            'name' => 'Dapur Tengah',
            'slug' => 'dapurtengah',
            'business_type_id' => $fnb->id,
            'plan_id' => $planPro->id,
            'owner_name' => 'Chef Dimas',
            'owner_email' => 'dimas@dapurtengah.com',
            'owner_phone' => '081122334455',
            'status' => 'active',
            'trial_ends_at' => null,
            'subscription_ends_at' => Carbon::now()->addMonths(5),
            'city' => 'Yogyakarta',
            'icon' => 'fa-utensils',
            'created_at' => Carbon::create(2026, 10, 25, 11, 45),
        ]);

        $t5 = Tenant::create([
            'name' => 'Laundry Kilat 99',
            'slug' => 'laundrykilat99',
            'business_type_id' => $laundry->id,
            'plan_id' => $planBasic->id,
            'owner_name' => 'Budi Santoso',
            'owner_email' => 'budi@laundrykilat.com',
            'owner_phone' => '081765432109',
            'status' => 'suspended',
            'trial_ends_at' => null,
            'subscription_ends_at' => Carbon::now()->subDays(5),
            'city' => 'Semarang',
            'icon' => 'fa-shirt',
            'created_at' => Carbon::create(2026, 10, 15, 8, 20),
        ]);

        $t6 = Tenant::create([
            'name' => 'Kafe Sudut Temu',
            'slug' => 'suduttemu',
            'business_type_id' => $fnb->id,
            'plan_id' => $planPro->id,
            'owner_name' => 'Andi Wijaya',
            'owner_email' => 'andi@suduttemu.com',
            'owner_phone' => '081987654321',
            'status' => 'expired',
            'trial_ends_at' => null,
            'subscription_ends_at' => Carbon::now()->subDays(10),
            'city' => 'Malang',
            'icon' => 'fa-mug-hot',
            'created_at' => Carbon::create(2026, 9, 10, 16, 0),
        ]);

        // 4. Invoices
        Invoice::create([
            'tenant_id' => $t1->id,
            'invoice_number' => 'INV-2048',
            'amount' => 1250000,
            'status' => 'paid',
            'due_date' => Carbon::create(2026, 10, 28),
            'paid_at' => Carbon::create(2026, 10, 28, 12, 10),
            'payment_method' => 'QRIS',
            'notes' => 'Langganan Paket Pro (6 Bulan)',
        ]);

        Invoice::create([
            'tenant_id' => $t2->id,
            'invoice_number' => 'INV-2047',
            'amount' => 447000,
            'status' => 'paid',
            'due_date' => Carbon::create(2026, 10, 27),
            'paid_at' => Carbon::create(2026, 10, 27, 15, 0),
            'payment_method' => 'Bank Transfer',
            'notes' => 'Perpanjangan Paket Basic',
        ]);

        Invoice::create([
            'tenant_id' => $t4->id,
            'invoice_number' => 'INV-2046',
            'amount' => 897000,
            'status' => 'paid',
            'due_date' => Carbon::create(2026, 10, 25),
            'paid_at' => Carbon::create(2026, 10, 25, 13, 30),
            'payment_method' => 'Virtual Account BCA',
            'notes' => 'Langganan Paket Pro (3 Bulan)',
        ]);

        Invoice::create([
            'tenant_id' => $t5->id,
            'invoice_number' => 'INV-2049',
            'amount' => 149000,
            'status' => 'overdue',
            'due_date' => Carbon::now()->subDays(5),
            'paid_at' => null,
            'payment_method' => null,
            'notes' => 'Tagihan Bulanan Laundry Kilat 99',
        ]);

        Invoice::create([
            'tenant_id' => $t6->id,
            'invoice_number' => 'INV-2050',
            'amount' => 299000,
            'status' => 'overdue',
            'due_date' => Carbon::now()->subDays(10),
            'paid_at' => null,
            'payment_method' => null,
            'notes' => 'Perpanjangan Kafe Sudut Temu',
        ]);

        // 5. Activity Logs (Matching Image 1)
        PlatformActivityLog::create([
            'action' => 'Pembayaran invoice INV-2048 diterima',
            'description' => 'Kopi Ruang Pagi • Rp 1.250.000',
            'icon' => 'fa-receipt',
            'icon_color' => 'text-emerald-500 bg-emerald-50',
            'badge_text' => '2 jam lalu',
            'created_at' => Carbon::now()->subHours(2),
        ]);

        PlatformActivityLog::create([
            'action' => 'Tenant Kopi Ruang Pagi dibuat',
            'description' => 'Paket Pro • 28 Okt 2026',
            'icon' => 'fa-plus',
            'icon_color' => 'text-orange-500 bg-orange-50',
            'badge_text' => '4 jam lalu',
            'created_at' => Carbon::now()->subHours(4),
        ]);

        PlatformActivityLog::create([
            'action' => 'Paket Toko Berkah Jaya diperpanjang',
            'description' => 'Dari Basic ke Pro • 27 Okt 2026',
            'icon' => 'fa-arrows-rotate',
            'icon_color' => 'text-blue-500 bg-blue-50',
            'badge_text' => '6 jam lalu',
            'created_at' => Carbon::now()->subHours(6),
        ]);

        PlatformActivityLog::create([
            'action' => 'Tenant Studio Rona memulai trial',
            'description' => 'Paket Business (14 hari masa uji)',
            'icon' => 'fa-clock',
            'icon_color' => 'text-amber-500 bg-amber-50',
            'badge_text' => '1 hari lalu',
            'created_at' => Carbon::now()->subDay(),
        ]);

        // 6. Announcements
        Announcement::create([
            'title' => 'Rilis Fitur Baru: Kalkulasi HPP Otomatis v2.4',
            'content' => 'Kini seluruh tenant F&B dapat menggunakan simulator perubahan harga bahan baku langsung dari menu resep.',
            'type' => 'update',
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(30),
        ]);

        Announcement::create([
            'title' => 'Jadwal Pemeliharaan Server Rutin',
            'content' => 'Akan dilaksanakan pemeliharaan server database pada hari Minggu pukul 02:00 - 04:00 WIB.',
            'type' => 'maintenance',
            'is_active' => true,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addDays(7),
        ]);

        // 7. Platform Settings
        PlatformSetting::set('app_name', 'Lokara POS');
        PlatformSetting::set('currency', 'IDR');
        PlatformSetting::set('tax_rate_default', '11');
        PlatformSetting::set('support_wa', '6281234567890');
        PlatformSetting::set('support_email', 'support@lokarapos.id');
    }
}

