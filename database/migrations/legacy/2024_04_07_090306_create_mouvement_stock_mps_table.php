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
        Schema::create('mouvement_stock_mps', function (Blueprint $table) {
            $table->id();
            $table->integer('id_stock_mp');
            $table->decimal('quantite', 30);
            $table->decimal('reste_maison', 30);
            $table->decimal('reste_usine', 30);
            $table->integer('statut')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvement_stock_mps');
    }
};
