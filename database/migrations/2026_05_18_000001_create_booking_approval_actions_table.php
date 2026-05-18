<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_approval_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 30)->index();
            $table->string('level', 30)->default('admin')->index();
            $table->text('notes')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_approval_actions');
    }
};
