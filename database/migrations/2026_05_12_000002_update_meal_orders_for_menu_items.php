<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meal_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('meal_orders', 'ref')) {
                $table->string('ref', 20)->nullable()->unique()->after('id');
            }

            if (! Schema::hasColumn('meal_orders', 'guest_id')) {
                $table->foreignId('guest_id')->nullable()->after('ref')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('meal_orders', 'menu_item_id')) {
                $table->foreignId('menu_item_id')->nullable()->after('meal_type')->constrained('menu_items')->nullOnDelete();
            }

            if (! Schema::hasColumn('meal_orders', 'total_price')) {
                $table->decimal('total_price', 8, 2)->default(0)->after('unit_price');
            }

            if (! Schema::hasColumn('meal_orders', 'coupon_code')) {
                $table->string('coupon_code', 50)->nullable()->after('total_price');
            }
        });

        if (Schema::hasColumn('meal_orders', 'reference')) {
            DB::table('meal_orders')
                ->whereNull('ref')
                ->update(['ref' => DB::raw('reference')]);
        }

        if (Schema::hasColumn('meal_orders', 'total')) {
            DB::table('meal_orders')
                ->where('total_price', 0)
                ->update(['total_price' => DB::raw('total')]);
        }

        DB::table('meal_orders')
            ->where('meal_type', 'dinner')
            ->update(['meal_type' => 'supper']);

        DB::table('meal_orders')
            ->where('status', 'confirmed')
            ->update(['status' => 'served']);

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE meal_orders MODIFY meal_type ENUM('breakfast', 'lunch', 'supper') NOT NULL");
            DB::statement("ALTER TABLE meal_orders MODIFY status ENUM('pending', 'served', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('meal_orders', function (Blueprint $table) {
            if (Schema::hasColumn('meal_orders', 'guest_id')) {
                $table->dropConstrainedForeignId('guest_id');
            }

            if (Schema::hasColumn('meal_orders', 'menu_item_id')) {
                $table->dropConstrainedForeignId('menu_item_id');
            }

            foreach (['ref', 'total_price', 'coupon_code'] as $column) {
                if (Schema::hasColumn('meal_orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
