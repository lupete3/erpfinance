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
        Schema::table('stock_maisons', function (Blueprint $table) {
            $table->decimal('configuration')->default(0)->after('solde');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_maisons', function (Blueprint $table) {
            $table->dropColumn('configuration');
        });
    }
};
