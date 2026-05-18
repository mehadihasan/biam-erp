<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_pending_approvals', function (Blueprint $table): void {
            $table->id();
            $table->string('ref', 50)->nullable()->index();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
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
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->string('approval_level', 30)->default('admin');
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'check_in_date', 'check_out_date'], 'gpa_room_check_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_pending_approvals');
    }
};
