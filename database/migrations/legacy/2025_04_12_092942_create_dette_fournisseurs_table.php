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
        Schema::create('dette_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_fournisseur')->constrained('fournisseurs')->onDelete('cascade');
            $table->foreignId('id_achat')->constrained('achat_stock_maisons')->onDelete('cascade');
            $table->decimal('montant_dette', 30); // montant dû non payé
            $table->decimal('reste_a_payer', 30); // mise à jour après paiements
            $table->boolean('est_soldee')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dette_fournisseurs');
    }
};
