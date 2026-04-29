<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->index('expense_date');
            $table->index(['tenant_id', 'store_id']);
            $table->index('currency');
        });

        Schema::table('dotations', function (Blueprint $table) {
            $table->index('date_dotation');
            $table->index(['tenant_id', 'store_id']);
            $table->index('currency');
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date']);
            $table->dropIndex(['tenant_id', 'store_id']);
            $table->dropIndex(['currency']);
        });

        Schema::table('dotations', function (Blueprint $table) {
            $table->dropIndex(['date_dotation']);
            $table->dropIndex(['tenant_id', 'store_id']);
            $table->dropIndex(['currency']);
        });
    }
};
