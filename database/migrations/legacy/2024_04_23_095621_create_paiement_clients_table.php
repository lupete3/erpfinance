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
        Schema::create('paiement_clients', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 30);
            $table->decimal('reste', 30);
            $table->foreignId('commande_client_id')->references('id')->on('commande_clients')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('client_id')->references('id')->on('clients')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_clients');
    }
};
