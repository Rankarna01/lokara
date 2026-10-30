<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add tenant_id to order_items (Multi-Tenant Security & Fast Scoping)
        if (Schema::hasTable('order_items') && !Schema::hasColumn('order_items', 'tenant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id');
            });

            // Backfill tenant_id from parent order
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE order_items 
                    INNER JOIN orders ON order_items.order_id = orders.id 
                    SET order_items.tenant_id = orders.tenant_id
                ');
            } else {
                DB::statement('
                    UPDATE order_items 
                    SET tenant_id = (SELECT tenant_id FROM orders WHERE orders.id = order_items.order_id)
                ');
            }

            Schema::table('order_items', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id', 'created_at']);
                $table->index(['tenant_id', 'product_id']);
            });
        }

        // 2. Add tenant_id to order_payments (Multi-Tenant Financial Audit)
        if (Schema::hasTable('order_payments') && !Schema::hasColumn('order_payments', 'tenant_id')) {
            Schema::table('order_payments', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id');
            });

            // Backfill tenant_id from parent order
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE order_payments 
                    INNER JOIN orders ON order_payments.order_id = orders.id 
                    SET order_payments.tenant_id = orders.tenant_id
                ');
            } else {
                DB::statement('
                    UPDATE order_payments 
                    SET tenant_id = (SELECT tenant_id FROM orders WHERE orders.id = order_payments.order_id)
                ');
            }

            Schema::table('order_payments', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id', 'payment_method']);
            });
        }

        // 3. Add tenant_id to product_recipes (Multi-Tenant Recipe BOM Security)
        if (Schema::hasTable('product_recipes') && !Schema::hasColumn('product_recipes', 'tenant_id')) {
            Schema::table('product_recipes', function (Blueprint $table) {
                $table->foreignId('tenant_id')->nullable()->after('id');
            });

            // Backfill tenant_id from parent product
            if (DB::getDriverName() === 'mysql') {
                DB::statement('
                    UPDATE product_recipes 
                    INNER JOIN products ON product_recipes.product_id = products.id 
                    SET product_recipes.tenant_id = products.tenant_id
                ');
            } else {
                DB::statement('
                    UPDATE product_recipes 
                    SET tenant_id = (SELECT tenant_id FROM products WHERE products.id = product_recipes.product_id)
                ');
            }

            Schema::table('product_recipes', function (Blueprint $table) {
                $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
                $table->index(['tenant_id', 'product_id']);
            });
        }

        // 4. Add customer_id & payment_status to orders (Normalized CRM & Payment Tracking)
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'customer_id')) {
                    $table->foreignId('customer_id')->nullable()->after('outlet_id')->constrained('customers')->nullOnDelete();
                }
                if (!Schema::hasColumn('orders', 'payment_status')) {
                    $table->enum('payment_status', ['paid', 'unpaid', 'partial', 'refunded'])->default('paid')->after('status');
                }
                // Add composite indexes for security and reporting query performance
                $table->index(['tenant_id', 'status']);
                $table->index(['tenant_id', 'created_at']);
            });
        }

        // 5. Create purchase_order_items (Normalized 3NF for Purchase Order Line Items)
        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('purchase_order_id')->constrained('purchase_orders')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
                $table->decimal('qty_ordered', 10, 2);
                $table->decimal('qty_received', 10, 2)->default(0);
                $table->decimal('unit_price', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->index(['tenant_id', 'purchase_order_id']);
            });
        }

        // 6. Create product_modifiers & product_modifier_options (Normalized 3NF for Topping / Modifiers)
        if (!Schema::hasTable('product_modifiers')) {
            Schema::create('product_modifiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name', 100); // e.g. 'Sugar Level', 'Extra Topping', 'Pilihan Susu'
                $table->boolean('is_required')->default(false);
                $table->boolean('is_multiple')->default(false);
                $table->integer('min_select')->default(0);
                $table->integer('max_select')->default(1);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        if (!Schema::hasTable('product_modifier_options')) {
            Schema::create('product_modifier_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_modifier_id')->constrained('product_modifiers')->cascadeOnDelete();
                $table->string('name', 100); // e.g. 'Normal Sugar (100%)', 'Grass Jelly (+4k)'
                $table->decimal('additional_price', 12, 2)->default(0);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->index(['tenant_id', 'product_modifier_id']);
            });
        }

        if (!Schema::hasTable('product_modifier_assignments')) {
            Schema::create('product_modifier_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('product_modifier_id')->constrained('product_modifiers')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['product_id', 'product_modifier_id'], 'prod_mod_unique');
                $table->index(['tenant_id', 'product_id']);
            });
        }

        // 7. Create tenant_settings (Normalized key-value configuration per tenant)
        if (!Schema::hasTable('tenant_settings')) {
            Schema::create('tenant_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('group', 50)->default('general'); // 'pos', 'receipt', 'tax', 'notification'
                $table->string('key', 100);
                $table->text('value')->nullable();
                $table->timestamps();

                $table->unique(['tenant_id', 'key']);
                $table->index(['tenant_id', 'group']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
        Schema::dropIfExists('product_modifier_assignments');
        Schema::dropIfExists('product_modifier_options');
        Schema::dropIfExists('product_modifiers');
        Schema::dropIfExists('purchase_order_items');

        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (Schema::hasColumn('orders', 'customer_id')) {
                    $table->dropForeign(['customer_id']);
                    $table->dropColumn('customer_id');
                }
                if (Schema::hasColumn('orders', 'payment_status')) {
                    $table->dropColumn('payment_status');
                }
            });
        }

        if (Schema::hasTable('product_recipes') && Schema::hasColumn('product_recipes', 'tenant_id')) {
            Schema::table('product_recipes', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }

        if (Schema::hasTable('order_payments') && Schema::hasColumn('order_payments', 'tenant_id')) {
            Schema::table('order_payments', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }

        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'tenant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['tenant_id']);
                $table->dropColumn('tenant_id');
            });
        }
    }
};
