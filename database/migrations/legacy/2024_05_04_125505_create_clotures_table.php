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
        Schema::create('clotures', function (Blueprint $table) {
            $table->id();
            $table->decimal('qnte_entree', 30);
            $table->decimal('qnte_sortie', 30);
            $table->decimal('avarie', 30);
            $table->decimal('consommation', 30);
            $table->decimal('prix', 30);
            $table->decimal('solde', 30);
            $table->foreignId('stock_pf_id');
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
        Schema::dropIfExists('clotures');
    }
};
