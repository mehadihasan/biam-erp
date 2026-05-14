<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_availabilities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('starts_on')->index();
            $table->date('ends_on')->index();
            $table->string('status', 30)->default('blocked')->index();
            $table->string('source', 30)->default('booking');
            $table->timestamps();

            $table->unique('booking_id');
            $table->index(['room_id', 'starts_on', 'ends_on']);
        });

        DB::table('bookings')
            ->whereNotIn('status', ['cancelled', 'checked_out', 'completed'])
            ->whereNotNull('room_id')
            ->whereNotNull('check_in_date')
            ->whereNotNull('check_out_date')
            ->select(['id', 'room_id', 'check_in_date', 'check_out_date', 'created_at', 'updated_at'])
            ->chunkById(100, function ($bookings): void {
                foreach ($bookings as $booking) {
                    DB::table('room_availabilities')->insert([
                        'room_id' => $booking->room_id,
                        'booking_id' => $booking->id,
                        'starts_on' => $booking->check_in_date,
                        'ends_on' => $booking->check_out_date,
                        'status' => 'blocked',
                        'source' => 'booking',
                        'created_at' => $booking->created_at ?? now(),
                        'updated_at' => $booking->updated_at ?? now(),
                    ]);
                }
            });

        DB::table('rooms')
            ->where('status', 'occupied')
            ->update(['status' => 'available']);
    }

    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};
