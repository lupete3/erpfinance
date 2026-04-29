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
        Schema::create('paiement_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dette_fournisseur_id')->constrained()->onDelete('cascade');
            $table->date('date_paiement')->default(now());
            $table->decimal('montant', 10, 2);
            $table->string('mode_paiement')->nullable(); // Ex: Espèces, Virement, Chèque
            $table->text('observation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiement_fournisseurs');
    }
};
