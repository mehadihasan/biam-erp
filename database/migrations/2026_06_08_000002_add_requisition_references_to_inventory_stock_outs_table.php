<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_stock_outs', function (Blueprint $table) {
            $table
                ->foreignId('inventory_requisition_id')
                ->nullable()
                ->after('id')
                ->constrained('inventory_requisitions')
                ->nullOnDelete();

            $table
                ->foreignId('inventory_requisition_item_id')
                ->nullable()
                ->after('inventory_requisition_id')
                ->constrained('inventory_requisition_items')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_stock_outs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('inventory_requisition_item_id');
            $table->dropConstrainedForeignId('inventory_requisition_id');
        });
    }
};
