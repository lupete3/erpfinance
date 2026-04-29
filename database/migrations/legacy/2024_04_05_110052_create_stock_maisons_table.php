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
        Schema::create('stock_maisons', function (Blueprint $table) {
            $table->id();
            $table->text('designation');
            $table->text('unite');
            $table->decimal('prix', 30);
            $table->decimal('solde', 30)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_maisons');
    }
};
