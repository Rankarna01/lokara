<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
                $table->foreignId('outlet_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->string('role_title')->default('Terapis / Stylist');
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['tenant_id', 'is_active']);
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'employee_id')) {
                $table->foreignId('employee_id')->nullable()->after('product_id')->constrained('employees')->nullOnDelete();
            }
            if (! Schema::hasColumn('order_items', 'employee_name')) {
                $table->string('employee_name')->nullable()->after('employee_id');
            }
            if (! Schema::hasColumn('order_items', 'commission_amount')) {
                $table->decimal('commission_amount', 12, 2)->default(0)->after('subtotal');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'employee_id')) {
                $table->dropForeign(['employee_id']);
                $table->dropColumn('employee_id');
            }
            if (Schema::hasColumn('order_items', 'employee_name')) {
                $table->dropColumn('employee_name');
            }
            if (Schema::hasColumn('order_items', 'commission_amount')) {
                $table->dropColumn('commission_amount');
            }
        });

        Schema::dropIfExists('employees');
    }
};
