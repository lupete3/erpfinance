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
        Schema::table('mouvement_stock_pfs', function (Blueprint $table) {
            $table->index('stock_pf_id');
            $table->index('created_at');
        });

        Schema::table('mouvement_stock_mps', function (Blueprint $table) {
            $table->index('id_stock_mp');
            $table->index('created_at');
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mouvement_stock_pfs', function (Blueprint $table) {
            $table->dropIndex(['stock_pf_id']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('mouvement_stock_mps', function (Blueprint $table) {
            $table->dropIndex(['id_stock_mp']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('productions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });
    }
};
