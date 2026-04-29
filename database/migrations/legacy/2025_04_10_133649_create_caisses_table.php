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
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->enum('type_operation', ['entree', 'sortie']);
            $table->decimal('montant', 15, 2)->default(0);
            $table->string('motif')->nullable();
            $table->decimal('solde_apres_operation', 15, 2)->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // L'utilisateur qui a effectué l'opération
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caisses');
    }
};
