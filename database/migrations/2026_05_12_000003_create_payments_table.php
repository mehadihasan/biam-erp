<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('ref', 20)->unique();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['booking_money', 'rent', 'meal'])->index();
            $table->string('gateway')->nullable()->default('SSLCOMMERZ');
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'cancelled'])->default('success')->index();
            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
