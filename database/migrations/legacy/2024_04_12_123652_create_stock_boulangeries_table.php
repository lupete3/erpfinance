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
        Schema::create('stock_boulangeries', function (Blueprint $table) {
            $table->id();
            $table->decimal('solde', 30)->default(0);
            $table->foreignId('stock_pf_id')->references('id')->on('stock_pfs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('site_id')->references('id')->on('sites')->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('inventaire')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_boulangeries');
    }
};
