<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_orders', function (Blueprint $table) {
            $table->id();
            $table->string('cadre_reference', 50)->index();
            $table->string('reference', 20)->unique();
            $table->date('order_date')->index();
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner'])->index();
            $table->string('menu_item');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedInteger('unit_price')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_orders');
    }
};
