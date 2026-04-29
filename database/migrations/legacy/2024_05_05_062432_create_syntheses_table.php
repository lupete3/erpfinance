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
        Schema::create('syntheses', function (Blueprint $table) {
            $table->id();
            $table->decimal('vente', 30);
            $table->decimal('avarie', 30);
            $table->decimal('depense', 30);
            $table->decimal('consommation', 30);
            $table->decimal('dette', 30);
            $table->decimal('change', 30);
            $table->decimal('total', 30);
            $table->decimal('espece', 30);
            $table->decimal('manquant', 30);
            $table->foreignId('site_id')->references('id')->on('sites')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('syntheses');
    }
};
