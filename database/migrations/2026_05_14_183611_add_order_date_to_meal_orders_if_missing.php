<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('meal_orders', 'order_date')) {
            return;
        }

        Schema::table('meal_orders', function (Blueprint $table): void {
            $table->date('order_date')->nullable()->index()->after('reference');
        });

        DB::table('meal_orders')
            ->whereNull('order_date')
            ->update([
                'order_date' => DB::raw('DATE(created_at)'),
            ]);
    }

    public function down(): void
    {
        // Intentionally keep order_date because current application code depends on it.
    }
};
