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
    Schema::create('inventory_items', function (Blueprint $table) {
      $table->id();
      $table->foreignId('inventory_id')->constrained()->onDelete('cascade');
      $table->foreignId('product_id')->constrained();
      $table->integer('physical_quantity');
      $table->integer('theoretical_quantity');
      $table->integer('difference');
      $table->text('comment')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventory_items');
  }
};
