<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Alter Users to add tenant_id, outlet_id, pin_code
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'outlet_id')) {
                $table->foreignId('outlet_id')->nullable()->after('tenant_id')->constrained('outlets')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'pin_code')) {
                $table->string('pin_code', 10)->nullable()->after('phone');
            }
        });

        // 2. Alter Products to add cost_price (HPP)
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 12, 2)->default(0)->after('base_price');
            }
        });

        // 3. Ingredients (Bahan Baku Mentah)
        if (!Schema::hasTable('ingredients')) {
            Schema::create('ingredients', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('category')->default('Umum'); // Biji Kopi, Dairy, Sirup, Packaging, dll
                $table->string('purchase_unit')->default('Kg'); // Kg, Liter, Dus, Pack
                $table->string('usage_unit')->default('Gram'); // Gram, ml, Pcs
                $table->decimal('conversion_factor', 10, 2)->default(1000); // 1 Kg = 1000 Gram
                $table->decimal('cost_per_usage_unit', 12, 2)->default(0); // Rp per Gram / ml
                $table->decimal('current_stock', 12, 2)->default(0); // in usage unit
                $table->decimal('minimum_stock', 12, 2)->default(0); // alert threshold
                $table->string('storage_location')->default('Bar Depan');
                $table->timestamps();
            });
        }

        // 4. Product Recipes (BOM - Bill of Materials)
        if (!Schema::hasTable('product_recipes')) {
            Schema::create('product_recipes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
                $table->decimal('quantity', 10, 2); // e.g. 18 gram, 120 ml
                $table->timestamps();
            });
        }

        // 5. Suppliers
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->string('name');
                $table->string('contact_person')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->integer('terms_days')->default(0); // 0 = Cash/COD, 14, 30 days
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 6. Purchase Orders (PO)
        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
                $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete();
                $table->string('po_number', 50);
                $table->date('order_date');
                $table->date('expected_date')->nullable();
                $table->enum('status', ['draft', 'ordered', 'received', 'cancelled'])->default('draft');
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. Stock Movements (Kartu Stok & Audit Ledger)
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
                $table->foreignId('ingredient_id')->constrained('ingredients')->cascadeOnDelete();
                $table->string('type'); // 'purchase', 'sale_pos', 'opname', 'waste', 'transfer'
                $table->decimal('qty', 12, 2); // +/-
                $table->decimal('balance_after', 12, 2)->default(0);
                $table->string('reference_note')->nullable();
                $table->timestamps();
            });
        }

        // 8. Expenses (Beban Operasional / OPEX untuk Laba Rugi)
        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
                $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
                $table->string('category'); // 'Gaji Karyawan', 'Listrik & Air', 'Sewa Tempat', 'Marketing', 'Pemeliharaan', 'Lainnya'
                $table->string('title');
                $table->decimal('amount', 12, 2);
                $table->date('expense_date');
                $table->string('payment_method')->default('Kas Toko / Tunai');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('ingredients');

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('cost_price');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tenant_id', 'outlet_id', 'pin_code']);
        });
    }
};

