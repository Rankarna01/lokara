<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('platform_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial features from existing plans
        $initialFeatures = [
            ['name' => 'Kasir Cepat', 'category' => 'Kasir & Transaksi'],
            ['name' => 'Laporan Dasar', 'category' => 'Laporan'],
            ['name' => 'Struk Digital WhatsApp', 'category' => 'Kasir & Transaksi'],
            ['name' => 'Manajemen Stok Sederhana', 'category' => 'Inventori'],
            ['name' => 'Multi-Metode Bayar', 'category' => 'Kasir & Transaksi'],
            ['name' => 'Laporan Penjualan Lengkap', 'category' => 'Laporan'],
            ['name' => 'Semua Fitur Basic', 'category' => 'Paket Bundling'],
            ['name' => 'Modul Resep (BOM) & HPP Otomatis', 'category' => 'Inventori'],
            ['name' => 'Multi-Gudang & Transfer Stok', 'category' => 'Inventori'],
            ['name' => 'Peringatan Stok Tipis & Expired', 'category' => 'Inventori'],
            ['name' => 'Pusat Laporan Laba Rugi', 'category' => 'Laporan'],
            ['name' => 'Semua Fitur Pro', 'category' => 'Paket Bundling'],
            ['name' => 'Unlimited Outlet & Kasir', 'category' => 'Platform'],
            ['name' => 'API Terbuka & Webhook', 'category' => 'Integrasi'],
            ['name' => 'Custom Domain', 'category' => 'Platform'],
            ['name' => 'Support WhatsApp Khusus 24/7', 'category' => 'Layanan & Bantuan'],
        ];

        $order = 1;
        $now = now();
        foreach ($initialFeatures as $feat) {
            DB::table('platform_features')->insert([
                'name' => $feat['name'],
                'category' => $feat['category'],
                'is_active' => true,
                'sort_order' => $order++,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_features');
    }
};

