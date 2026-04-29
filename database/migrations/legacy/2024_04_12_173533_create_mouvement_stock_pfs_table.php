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
        Schema::create('mouvement_stock_pfs', function (Blueprint $table) {
            $table->id();
            $table->integer('stock_pf_id');
            $table->decimal('quantite', 30);
            $table->decimal('reste_stock_pf', 30);
            $table->decimal('reste_boulangerie', 30);
            $table->foreignId('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_stock_pfs');
    }
};
