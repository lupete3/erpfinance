<?php

namespace App\Livewire\Bakery;

use App\Models\Caisse;
use App\Models\CommandeClient;
use App\Models\PaiementClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ClientDebt extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    // Payment fields
    public $selectedCommandeId;
    public $montantPaye;
    public $commandeDetails;
    public $selectedSiteId;

    // Details fields
    public $selectedDetailsCommande;

    public $filterSiteId = '';

    protected $rules = [
        'montantPaye' => 'required|numeric|min:1',
        'selectedSiteId' => 'required|exists:sites,id',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterSiteId()
    {
        $this->resetPage();
    }

    public function openPaymentModal($id)
    {
        $this->selectedCommandeId = $id;
        $this->commandeDetails = CommandeClient::with('client')->find($id);
        $this->montantPaye = $this->commandeDetails->reste;
        $this->selectedSiteId = $this->commandeDetails->site_id;
        $this->dispatch('openModal', ['id' => 'paymentModal']);
    }

    public function openDetailsModal($id)
    {
        $this->selectedDetailsCommande = CommandeClient::with(['client', 'ventes'])->find($id);
        $this->dispatch('openModal', ['id' => 'detailsModal']);
    }

    public function storePayment()
    {
        $this->validate();

        $commande = CommandeClient::find($this->selectedCommandeId);

        if ($this->montantPaye > $commande->reste) {
            $this->addError('montantPaye', 'Le montant dépasse le reste à payer.');
            return;
        }

        DB::transaction(function () use ($commande) {
            // 1. Update Commande
            $commande->update([
                'reste' => $commande->reste - $this->montantPaye,
                'paye' => $commande->paye + $this->montantPaye,
            ]);

            // 2. Create Payment Record
            PaiementClient::create([
                'montant' => $this->montantPaye,
                'reste' => $commande->reste,
                'commande_client_id' => $commande->id,
                'client_id' => $commande->client_id,
            ]);

            // 3. Update Cash Register
            $dernierSolde = Caisse::latest()->value('solde_apres_operation') ?? 0;
            $description = "Paiement Dette Commande #{$commande->id} - Client: {$commande->client->nom}";

            Caisse::create([
                'type_operation' => 'entree',
                'montant' => $this->montantPaye,
                'motif' => $description,
                'solde_apres_operation' => $dernierSolde + $this->montantPaye,
                'user_id' => Auth::id(),
            ]);
        });

        session()->flash('success', 'Paiement enregistré avec succès.');
        $this->dispatch('closeModal', ['id' => 'paymentModal']);
        $this->reset(['selectedCommandeId', 'montantPaye', 'commandeDetails', 'selectedSiteId']);
    }

    public function render()
    {
        $user = Auth::user();

        $query = CommandeClient::with(['client', 'ventes'])
            ->where('reste', '>', 0);

        // Apply site filter
        if ($this->filterSiteId !== '') {
            $query->where('site_id', $this->filterSiteId);
        } elseif (!$user->hasRoleString('admin')) {
            // Default filter for non-admins
            $site_id = $user->site_id ?? 1;
            $query->where('site_id', $site_id);
        }

        $unpaidCommandes = $query->whereHas('client', function ($q) {
                $q->where('nom', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $sites = \App\Models\Site::select('id', 'nom')->get();

        return view('livewire.bakery.client-debt', [
            'commandes' => $unpaidCommandes,
            'sites' => $sites,
        ])->layout('components.layouts.app', ['title' => 'Gestion des Dettes Clients']);
    }
}
