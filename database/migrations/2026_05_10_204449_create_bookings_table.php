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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('room_type', 20);
            $table->unsignedSmallInteger('number_of_rooms')->default(1);
            $table->date('check_in_date')->index();
            $table->date('check_out_date')->index();
            $table->text('notes')->nullable();
            $table->unsignedInteger('duration_nights')->default(0);
            $table->unsignedTinyInteger('rent_multiplier')->default(1);
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->decimal('calculated_rent', 10, 2)->default(0);
            $table->decimal('booking_money', 10, 2)->default(0);
            $table->decimal('total_rent', 10, 2)->default(0);
            $table->string('status', 30)->default('pending')->index();
            $table->timestamps();

            $table->index(['room_id', 'check_in_date', 'check_out_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
