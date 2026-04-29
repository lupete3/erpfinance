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
        Schema::create('achat_stock_maisons', function (Blueprint $table) {
            $table->id();
            $table->decimal('prix_achat', 30);
            $table->decimal('quantite', 30);
            $table->foreignId('id_fournisseur')->references('id')->on('fournisseurs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('id_stock_maisons')->references('id')->on('stock_maisons')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achat_stock_maisons');
    }
};
