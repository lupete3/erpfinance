<?php

namespace App\Livewire\Bakery\Admin;

use App\Models\Site;
use App\Models\User;
use App\Models\StockPf;
use App\Models\StockBoulangerie;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Settings extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'sites';

    // Site State
    public $site_nom;
    public $editingSiteId;

    // User State
    public $user_name;
    public $user_email;
    public $user_password;
    public $user_role = 'geran_depot_boulangerie';
    public $user_site_id;
    public $editingUserId;

    protected $rules = [
        'site_nom' => 'required_if:activeTab,sites|min:3',
        'user_name' => 'required_if:activeTab,users|min:3',
        'user_email' => 'required_if:activeTab,users|email',
        'user_role' => 'required_if:activeTab,users|in:admin,geran_depot_magasin,geran_depot_usine,geran_depot_boulangerie',
        'user_site_id' => 'required_if:activeTab,users|exists:sites,id',
    ];

    public function resetFields()
    {
        $this->reset(['site_nom', 'editingSiteId', 'user_name', 'user_email', 'user_password', 'user_role', 'user_site_id', 'editingUserId']);
    }

    // --- SITE METHODS ---
    public function openSiteModal($id = null)
    {
        $this->resetFields();
        if ($id) {
            $this->editingSiteId = $id;
            $site = Site::find($id);
            if ($site) {
                $this->site_nom = $site->nom;
            }
        }
        $this->dispatch('openModal', ['id' => 'siteModal']);
    }

    public function storeSite()
    {
        $this->validate(['site_nom' => 'required|min:3']);

        if ($this->editingSiteId) {
            Site::find($this->editingSiteId)->update(['nom' => $this->site_nom]);
            session()->flash('success', 'Point de vente mis à jour.');
        } else {
            $site = Site::create(['nom' => $this->site_nom]);
            $this->syncSiteStock($site->id);
            session()->flash('success', 'Point de vente ajouté et synchronisé.');
        }

        $this->dispatch('closeModal', ['id' => 'siteModal']);
        $this->resetFields();
    }

    public function syncSiteStock($siteId)
    {
        $site = Site::findOrFail($siteId);
        $products = StockPf::all();

        $syncedCount = 0;
        foreach ($products as $pf) {
            $exists = StockBoulangerie::where('site_id', $siteId)
                ->where('stock_pf_id', $pf->id)
                ->exists();

            if (!$exists) {
                StockBoulangerie::create([
                    'site_id' => $siteId,
                    'stock_pf_id' => $pf->id,
                    'solde' => 0,
                    'inventaire' => false,
                ]);
                $syncedCount++;
            }
        }

        if ($this->editingSiteId || $syncedCount > 0) {
            session()->flash('success', "Synchronisation terminée : $syncedCount nouveaux produits ajoutés pour {$site->nom}.");
        }
    }

    // --- USER METHODS ---
    public function openUserModal($id = null)
    {
        $this->resetFields();
        if ($id) {
            $this->editingUserId = $id;
            $user = User::find($id);
            if ($user) {
                $this->user_name = $user->name;
                $this->user_email = $user->email;
                $this->user_role = $user->role;
                $this->user_site_id = $user->site_id;
            }
        }
        $this->dispatch('openModal', ['id' => 'userModal']);
    }

    public function storeUser()
    {
        if ($this->editingUserId) {
            $this->validate([
                'user_name' => 'required|min:3',
                'user_email' => 'required|email|unique:users,email,' . $this->editingUserId,
                'user_role' => 'required|in:admin,geran_depot_magasin,geran_depot_usine,geran_depot_boulangerie',
                'user_site_id' => 'required|exists:sites,id',
            ]);
        } else {
            $this->validate([
                'user_name' => 'required|min:3',
                'user_email' => 'required|email|unique:users,email',
                'user_role' => 'required|in:admin,geran_depot_magasin,geran_depot_usine,geran_depot_boulangerie',
                'user_site_id' => 'required|exists:sites,id',
                'user_password' => 'required|min:6',
            ]);
        }

        $data = [
            'tenant_id' => null, // Explicitly null for Bakery users
            'name' => $this->user_name,
            'email' => $this->user_email,
            'role' => $this->user_role,
            'site_id' => $this->user_site_id,
        ];

        if ($this->user_password) {
            $data['password'] = Hash::make($this->user_password);
        }

        if ($this->editingUserId) {
            User::find($this->editingUserId)->update($data);
            session()->flash('success', 'Utilisateur mis à jour.');
        } else {
            User::create($data);
            session()->flash('success', 'Utilisateur ajouté.');
        }

        $this->dispatch('closeModal', ['id' => 'userModal']);
        $this->resetFields();
    }

    #[Layout('components.layouts.app')]
    #[Title('Administration Boulangerie')]
    public function render()
    {
        $sites = Site::all();
        $bakeryRoles = ['admin', 'geran_depot_magasin', 'geran_depot_usine', 'geran_depot_boulangerie'];

        $users = User::where(function ($q) use ($bakeryRoles) {
            $q->whereIn('role', $bakeryRoles)
                ->orWhere(function ($sq) {
                    $sq->whereNull('tenant_id')
                        ->whereNotNull('site_id');
                });
        })
            ->with('site')
            ->paginate(10);

        return view('livewire.bakery.admin.settings', [
            'sites' => $sites,
            'users' => $users,
        ]);
    }
}
