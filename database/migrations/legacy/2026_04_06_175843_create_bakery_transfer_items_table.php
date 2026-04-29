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
        Schema::create('bakery_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bakery_transfer_id')->constrained('bakery_transfers')->onDelete('cascade');
            $table->foreignId('stock_pf_id')->constrained('stock_pfs')->onDelete('cascade');
            $table->decimal('quantity', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bakery_transfer_items');
    }
};
