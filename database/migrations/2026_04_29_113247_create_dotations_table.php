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
        Schema::create('dotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade'); // La succursale
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le Boss qui envoie
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3); // USD ou CDF
            $table->date('date_dotation');
            $table->text('description')->nullable();
            $table->string('reference')->unique()->nullable(); // Numéro de bordereau ou ref transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dotations');
    }
};
