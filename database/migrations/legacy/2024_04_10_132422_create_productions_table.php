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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->text('designation');
            $table->decimal('quantite', 30);
            $table->decimal('charge_personnel', 30)->default(0);
            $table->decimal('autres_charges', 30)->default(0);
            $table->foreignId('stock_pf_id')->references('id')->on('stock_pfs')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
