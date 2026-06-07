<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_suppliers', function (Blueprint $table) {
            $table
                ->foreignId('category_id')
                ->nullable()
                ->after('category')
                ->constrained('inventory_categories')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
