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
        Schema::create('sales', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
                $table->foreignId('client_id')->nullable()->constrained();
                $table->foreignId('user_id')->constrained();
                $table->decimal('total_amount', 12, 2);
                $table->decimal('total_paid', 12, 2)->default(0);
                $table->dateTime('sale_date')->useCurrent();
                $table->enum('status', ['validated', 'pending', 'returned'])->default('validated');
                $table->foreignId('store_id')->constrained();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
