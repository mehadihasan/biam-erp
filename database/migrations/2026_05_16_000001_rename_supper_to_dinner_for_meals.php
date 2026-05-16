<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meal_orders')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE meal_orders MODIFY meal_type ENUM('breakfast', 'lunch', 'supper', 'dinner') NOT NULL");
            }

            DB::table('meal_orders')
                ->where('meal_type', 'supper')
                ->update(['meal_type' => 'dinner']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE meal_orders MODIFY meal_type ENUM('breakfast', 'lunch', 'dinner') NOT NULL");
            }
        }

        if (Schema::hasTable('menu_items')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE menu_items MODIFY meal_type ENUM('breakfast', 'lunch', 'supper', 'dinner') NOT NULL");
            }

            DB::table('menu_items')
                ->where('meal_type', 'supper')
                ->update(['meal_type' => 'dinner']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE menu_items MODIFY meal_type ENUM('breakfast', 'lunch', 'dinner') NOT NULL");
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('meal_orders')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE meal_orders MODIFY meal_type ENUM('breakfast', 'lunch', 'dinner', 'supper') NOT NULL");
            }

            DB::table('meal_orders')
                ->where('meal_type', 'dinner')
                ->update(['meal_type' => 'supper']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE meal_orders MODIFY meal_type ENUM('breakfast', 'lunch', 'supper') NOT NULL");
            }
        }

        if (Schema::hasTable('menu_items')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE menu_items MODIFY meal_type ENUM('breakfast', 'lunch', 'dinner', 'supper') NOT NULL");
            }

            DB::table('menu_items')
                ->where('meal_type', 'dinner')
                ->update(['meal_type' => 'supper']);

            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE menu_items MODIFY meal_type ENUM('breakfast', 'lunch', 'supper') NOT NULL");
            }
        }
    }
};
