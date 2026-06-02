<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_stock_ins', function (Blueprint $table) {
            $table->id();
            $table->date('stock_in_date');
            $table->date('expiry_warranty_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('inventory_supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->text('purpose_notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_stock_ins');
    }
};
