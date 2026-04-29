<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Tenant;
use App\Models\Store;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class TenantManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $tenantId;
    public $name;
    public $contact_name;
    public $email;
    public $phone;
    public $address;
    public $is_active = true;
    public $isEditMode = false;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function render()
    {
        $tenants = Tenant::query()
            ->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.super-admin.tenant-manager', [
            'tenants' => $tenants,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId     = $id;
        $this->name         = $tenant->name;
        $this->contact_name = $tenant->contact_name;
        $this->email        = $tenant->email;
        $this->phone        = $tenant->phone;
        $this->address      = $tenant->address;
        $this->is_active    = (bool) $tenant->is_active;
        $this->isEditMode   = true;
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $this->tenantId,
        ]);

        $tenant = Tenant::updateOrCreate(
            ['id' => $this->tenantId],
            [
                'name'         => $this->name,
                'contact_name' => $this->contact_name,
                'email'        => $this->email,
                'phone'        => $this->phone,
                'address'      => $this->address,
                'is_active'    => $this->is_active,
            ]
        );
        // Si c’est une création (pas une mise à jour)
        if (!$this->isEditMode) {
            // 1. Créer un magasin par défaut
            $store = Store::create([
                'tenant_id' => $tenant->id,
                'name'      => 'Magasin Principal',
                'location'  => $this->address,
                // 'is_active' => true, // La colonne is_active n'existe pas dans la table stores selon la migration
            ]);

            // 2. Récupérer ou créer le rôle Admin
            $adminRole = Role::firstOrCreate(
                ['name' => 'Admin'],
                ['description' => 'Administrateur du tenant']
            );

            // 3. Créer l’utilisateur Admin
            User::create([
                'tenant_id' => $tenant->id,
                // 'store_id'  => $store->id, // La colonne store_id n'existe pas dans la table users
                'role_id'   => $adminRole->id,
                'name'      => $this->contact_name ?: $this->name,
                'email'     => $this->email,
                'password'  => Hash::make('password123'), // ⚠️ À changer ensuite
                'is_active' => true,
            ]);

            // 4. Créer une souscription par défaut
            $plan = Plan::first(); // ⚠️ Ici tu choisis le premier plan dispo
            if ($plan) {
                Subscription::create([
                    'tenant_id'  => $tenant->id,
                    'plan_id'    => $plan->id,
                    'amount'     => $plan->price,
                    'start_date'  => Carbon::now(),
                    'end_date'    => Carbon::now()->addMonth(), // ou selon durée du plan
                    'is_active'  => true,
                ]);
            }
        }

        notyf()->success(__($this->isEditMode ? 'Client mis à jour.' : 'Client, admin et souscription créés.'));

        $this->dispatch('close-modal');
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->tenantId = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Tenant::find($this->tenantId)?->delete();
        notyf()->success(__('Client supprimé.'));
        $this->dispatch('close-delete-confirmation');
    }

    private function resetInputFields()
    {
        $this->tenantId     = null;
        $this->name         = '';
        $this->contact_name = '';
        $this->email        = '';
        $this->phone        = '';
        $this->address      = '';
        $this->is_active    = true;
    }
}
