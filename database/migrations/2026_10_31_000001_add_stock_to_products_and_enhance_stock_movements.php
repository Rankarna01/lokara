<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add stock tracking columns to products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'stock_quantity')) {
                $table->decimal('stock_quantity', 12, 2)->default(0)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'minimum_stock')) {
                $table->decimal('minimum_stock', 12, 2)->default(5)->after('stock_quantity');
            }
            if (!Schema::hasColumn('products', 'track_stock')) {
                $table->boolean('track_stock')->default(true)->after('minimum_stock');
            }
            if (!Schema::hasColumn('products', 'unit')) {
                $table->string('unit', 50)->default('pcs')->after('track_stock');
            }
        });

        // 2. Enhance stock_movements table to support direct products and audit user
        Schema::table('stock_movements', function (Blueprint $table) {
            // Make ingredient_id nullable so movement can be for direct products
            $table->foreignId('ingredient_id')->nullable()->change();

            if (!Schema::hasColumn('stock_movements', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('outlet_id')->constrained('products')->nullOnDelete();
            }

            if (!Schema::hasColumn('stock_movements', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('product_id')->constrained('users')->nullOnDelete();
            }

            // Indexes for fast date & filter queries
            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'created_at']);
            $table->dropIndex(['tenant_id', 'product_id']);
            if (Schema::hasColumn('stock_movements', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('stock_movements', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'unit')) {
                $table->dropColumn('unit');
            }
            if (Schema::hasColumn('products', 'track_stock')) {
                $table->dropColumn('track_stock');
            }
            if (Schema::hasColumn('products', 'minimum_stock')) {
                $table->dropColumn('minimum_stock');
            }
            if (Schema::hasColumn('products', 'stock_quantity')) {
                $table->dropColumn('stock_quantity');
            }
        });
    }
};

