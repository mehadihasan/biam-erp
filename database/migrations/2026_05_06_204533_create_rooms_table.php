<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number', 20)->unique();
            $table->unsignedSmallInteger('floor')->index();
            $table->enum('room_type', ['ac', 'non_ac', 'vip'])->index();
            $table->unsignedSmallInteger('capacity')->default(1);
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->enum('status', ['available', 'occupied', 'maintenance'])->index();
            $table->text('description')->nullable();
            $table->string('picture_path')->nullable();
            $table->timestamps();

            $table->index(['room_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
