<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'gender')) {
                $table->string('gender', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('customers', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('customers', 'address')) {
                $table->text('address')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('customers', 'membership_tier')) {
                $table->string('membership_tier', 30)->default('silver')->after('points');
            }
            if (!Schema::hasColumn('customers', 'notes')) {
                $table->text('notes')->nullable()->after('membership_tier');
            }
            if (!Schema::hasColumn('customers', 'total_spent')) {
                $table->decimal('total_spent', 12, 2)->default(0)->after('notes');
            }
            if (!Schema::hasColumn('customers', 'visit_count')) {
                $table->integer('visit_count')->default(0)->after('total_spent');
            }
            if (!Schema::hasColumn('customers', 'last_visit_at')) {
                $table->dateTime('last_visit_at')->nullable()->after('visit_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['gender', 'birth_date', 'address', 'membership_tier', 'notes', 'total_spent', 'visit_count', 'last_visit_at'] as $col) {
                if (Schema::hasColumn('customers', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
