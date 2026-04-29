<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1️⃣ Rôles
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Gestion globale du SaaS'],
            ['name' => 'Boss', 'description' => 'Propriétaire de l\'entreprise / Admin Tenant'],
            ['name' => 'Gérant', 'description' => 'Gestionnaire d\'une succursale'],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['name' => $roleData['name']], $roleData);
        }

        // 2️⃣ Plan par défaut
        $plan = Plan::firstOrCreate(
            ['name' => 'PRO PLAN'],
            [
                'price' => 50, // USD par exemple
                'duration_days' => 30,
                'max_users' => 20,
                'max_stores' => 10,
            ]
        );

        // 3️⃣ Super Admin SaaS
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        User::updateOrCreate(
            ['email' => 'superadmin@erpfinance.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'tenant_id' => null,
                'is_active' => true,
            ]
        );

        // Paramètres système globaux (SaaS)
        \App\Models\CompanySetting::updateOrCreate(
            ['tenant_id' => null],
            [
                'name' => 'ERP Finance SaaS',
                'email' => 'contact@erpfinance.com',
                'devise' => 'USD',
            ]
        );

        // 4️⃣ Tenant de test (Boss)
        $tenant = Tenant::firstOrCreate(
            ['email' => 'boss@test.com'],
            [
                'name' => 'Entreprise RDC Test',
                'contact_name' => 'M. le Boss',
                'phone' => '+243000000000',
                'address' => 'Kinshasa, Gombe',
                'is_active' => true,
            ]
        );

        // 5️⃣ Abonnement pour le tenant
        Subscription::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'is_active' => true,
            ]
        );

        // 6️⃣ Le Boss utilisateur
        $bossRole = Role::where('name', 'Boss')->first();
        $gerantRole = Role::where('name', 'Gérant')->first();
        $storeA = \App\Models\Store::firstOrCreate(['name' => 'Succursale A', 'tenant_id' => $tenant->id]);
        
        $boss = User::updateOrCreate(
            ['email' => 'boss@test.com'],
            [
                'name' => 'Boss User',
                'password' => Hash::make('password'),
                'role_id' => $bossRole->id,
                'tenant_id' => $tenant->id,
                'is_active' => true,
            ]
        );

        // 7️⃣ Un Gérant utilisateur pour la Succursale A
        User::updateOrCreate(
            ['email' => 'gerant@test.com'],
            [
                'name' => 'Gérant Succursale A',
                'password' => Hash::make('password'),
                'role_id' => $gerantRole->id,
                'tenant_id' => $tenant->id,
                'store_id' => $storeA->id,
                'is_active' => true,
            ]
        );

        // 7️⃣ Paramètres entreprise pour le tenant
        \App\Models\CompanySetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'name' => $tenant->name,
                'email' => $tenant->email,
                'phone' => $tenant->phone,
                'address' => $tenant->address,
                'devise' => 'USD',
            ]
        );
    }
}
