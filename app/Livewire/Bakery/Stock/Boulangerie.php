<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\Site;
use App\Models\StockBoulangerie;
use App\Models\StockPf;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Boulangerie extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $site_id; // Pour les admins, pour filtrer par site
    public $adjustmentQuantity, $selectedBoulangerieId;

    public function mount()
    {
        $user = Auth::user();
        if ($user->isBakeryUser()) {
            $this->site_id = $user->site_id;
        } else {
            // Pour les admins, on peut mettre le premier site par défaut
            $this->site_id = $this->site_id ?: (Site::first()->id ?? null);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSiteId()
    {
        $this->resetPage();
    }

    public function openAdjustmentModal($id)
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->selectedBoulangerieId = $id;
        $stockBoulangerie = StockBoulangerie::findOrFail($id);
        $this->adjustmentQuantity = $stockBoulangerie->solde;
        $this->dispatch('openModal', ['id' => 'adjustmentModalBoulangerie']);
    }

    public function updateAdjustment()
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->validate([
            'adjustmentQuantity' => 'required|numeric|min:0',
        ]);

        $stockBoulangerie = StockBoulangerie::findOrFail($this->selectedBoulangerieId);
        $stockBoulangerie->update([
            'solde' => $this->adjustmentQuantity,
        ]);

        session()->flash('success', 'Stock point de vente ajusté avec succès.');
        $this->dispatch('closeModal', ['id' => 'adjustmentModalBoulangerie']);
        $this->reset(['adjustmentQuantity', 'selectedBoulangerieId']);
    }

    public function render()
    {
        $user = Auth::user();

        $query = StockBoulangerie::with(['stockProduitFinis', 'site'])
            ->whereHas('stockProduitFinis', function ($q) {
                $q->where('designation', 'like', '%' . $this->search . '%');
            });

        // Si l'utilisateur est restreint à un site (et n'est pas admin)
        if ($user->isBakeryUser() && !$user->hasRoleString('admin')) {
            $this->site_id = $user->site_id;
        }

        if ($this->site_id) {
            $query->where('site_id', $this->site_id);
        }

        $produits = $query->orderBy('site_id')->paginate(20);
        $sites = Site::orderBy('nom', 'ASC')->get();

        // Calcul du total pour les produits filtrés
        $tot = StockBoulangerie::whereHas('stockProduitFinis', function ($q) {
            $q->where('designation', 'like', '%' . $this->search . '%');
        })
            ->when($this->site_id, function ($q) {
                $q->where('site_id', $this->site_id);
            })
            ->join('stock_pfs', 'stock_boulangeries.stock_pf_id', '=', 'stock_pfs.id')
            ->selectRaw('SUM(stock_pfs.prix * stock_boulangeries.solde) as total_valeur')
            ->first()->total_valeur ?? 0;

        return view('livewire.bakery.stock.boulangerie', [
            'produits' => $produits,
            'sites' => $sites,
            'tot' => $tot
        ])->layout('components.layouts.app', ['title' => 'Stock Points de Vente']);
    }
}
