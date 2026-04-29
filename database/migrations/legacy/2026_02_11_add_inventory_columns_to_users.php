<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Colonnes pour l'app inventaire (nullable pour compatibilité avec utilisateurs boulangerie)
            $table->unsignedBigInteger('tenant_id')->nullable()->after('id');
            $table->unsignedBigInteger('role_id')->nullable()->after('email_verified_at');
            $table->boolean('is_active')->default(1)->after('password');

            // Foreign keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');

            // Index pour performance
            $table->index('tenant_id');
            $table->index('role_id');
            $table->index('site_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['role_id']);
            $table->dropIndex(['tenant_id']);
            $table->dropIndex(['role_id']);
            $table->dropIndex(['site_id']);
            $table->dropColumn(['tenant_id', 'role_id', 'is_active']);
        });
    }
};
