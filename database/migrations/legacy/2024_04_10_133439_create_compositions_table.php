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
        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->text('designation');
            $table->text('unite');
            $table->decimal('quantite', 30);
            $table->decimal('prix', 30);
            $table->foreignId('stock_usine_id')->references('id')->on('stock_usines')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('production_id')->references('id')->on('productions')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compositions');
    }
};
