<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter products table to add service attributes
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'duration_minutes')) {
                $table->integer('duration_minutes')->default(30)->after('cost_price');
            }
            if (!Schema::hasColumn('products', 'commission_type')) {
                $table->string('commission_type', 20)->default('fixed')->after('duration_minutes'); // 'fixed' or 'percentage'
            }
            if (!Schema::hasColumn('products', 'commission_value')) {
                $table->decimal('commission_value', 12, 2)->default(0)->after('commission_type');
            }
        });

        // 2. Create discounts table
        if (!Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->enum('type', ['percentage', 'fixed'])->default('percentage');
                $table->decimal('value', 12, 2)->default(0);
                $table->decimal('min_purchase', 12, 2)->default(0);
                $table->decimal('max_discount', 12, 2)->nullable();
                $table->enum('applicable_to', ['all', 'services', 'products'])->default('all');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->integer('max_usage')->nullable();
                $table->integer('usage_count')->default(0);
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'duration_minutes')) {
                $table->dropColumn('duration_minutes');
            }
            if (Schema::hasColumn('products', 'commission_type')) {
                $table->dropColumn('commission_type');
            }
            if (Schema::hasColumn('products', 'commission_value')) {
                $table->dropColumn('commission_value');
            }
        });
    }
};
