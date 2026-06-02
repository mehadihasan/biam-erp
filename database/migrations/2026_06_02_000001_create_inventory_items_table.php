<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('inventory_category_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('inventory_unit_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->string('location')->nullable();
            $table->unsignedInteger('min_threshold')->default(5);
            $table->unsignedInteger('initial_stock_quantity')->default(0);
            $table->unsignedInteger('current_stock_quantity')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
