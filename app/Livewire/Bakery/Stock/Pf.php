<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\StockPf;
use Livewire\Component;
use Livewire\WithPagination;

class Pf extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $designation, $prix, $solde;
    public $editingPfId, $site_id, $quantite_exp;
    public $isEditMode = false;
    public $isShippingMode = false;
    public $adjustmentQuantity;

    // Mass selection properties
    public $selectedPfs = [];
    public $massQtys = []; // [pf_id => quantity]
    public $massSiteId;

    protected $rules = [
        'designation' => 'required|string|max:255',
        'prix' => 'required|numeric|min:0',
        'solde' => 'required|numeric|min:0',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->designation = '';
        $this->prix = 0;
        $this->solde = 0;
        $this->editingPfId = null;
        $this->site_id = null;
        $this->quantite_exp = '';
        $this->selectedPfs = [];
        $this->massQtys = [];
        $this->massSiteId = null;
        $this->isEditMode = false;
        $this->isShippingMode = false;
    }

    public function create()
    {
        $this->resetFields();
        $this->dispatch('openModal', ['id' => 'pfModal']);
    }

    public function store()
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        StockPf::create([
            'designation' => $this->designation,
            'prix' => $this->prix,
            'solde' => $this->solde,
        ]);

        session()->flash('success', 'Produit fini ajouté avec succès.');
        $this->dispatch('closeModal', ['id' => 'pfModal']);
        $this->resetFields();
    }

    public function edit($id)
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->resetFields();
        $this->isEditMode = true;
        $this->editingPfId = $id;
        $pf = StockPf::findOrFail($id);

        $this->designation = $pf->designation;
        $this->prix = $pf->prix;
        $this->solde = $pf->solde;

        $this->dispatch('openModal', ['id' => 'pfModal']);
    }

    public function update()
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        $pf = StockPf::findOrFail($this->editingPfId);
        $pf->update([
            'designation' => $this->designation,
            'prix' => $this->prix,
            'solde' => $this->solde,
        ]);

        session()->flash('success', 'Mise à jour effectuée avec succès.');
        $this->dispatch('closeModal', ['id' => 'pfModal']);
        $this->resetFields();
    }

    public function openAdjustmentModal($id)
    {
        if (auth()->user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->editingPfId = $id;
        $pf = StockPf::findOrFail($id);
        $this->adjustmentQuantity = $pf->solde;
        $this->dispatch('openModal', ['id' => 'adjustmentModalPf']);
    }

    public function updateAdjustment()
    {
        if (auth()->user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->validate([
            'adjustmentQuantity' => 'required|numeric|min:0',
        ]);

        $pf = StockPf::findOrFail($this->editingPfId);
        $pf->update([
            'solde' => $this->adjustmentQuantity,
        ]);

        session()->flash('success', 'Stock produits finis ajusté avec succès.');
        $this->dispatch('closeModal', ['id' => 'adjustmentModalPf']);
        $this->reset(['adjustmentQuantity', 'editingPfId']);
    }

    public function openShipModal($id)
    {
        $this->resetFields();
        $this->isShippingMode = true;
        $this->editingPfId = $id;
        $this->dispatch('openModal', ['id' => 'shipModal']);
    }

    public function shipToSite()
    {
        $this->validate([
            'site_id' => 'required|exists:sites,id',
            'quantite_exp' => 'required|numeric|min:0.01',
        ]);

        $pf = StockPf::findOrFail($this->editingPfId);

        if ($pf->solde < $this->quantite_exp) {
            $this->addError('quantite_exp', 'Stock insuffisant au fournil.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($pf) {
            $stockBoulangerie = \App\Models\StockBoulangerie::where('stock_pf_id', $pf->id)
                ->where('site_id', $this->site_id)
                ->first();

            if (!$stockBoulangerie) {
                $stockBoulangerie = \App\Models\StockBoulangerie::create([
                    'solde' => 0,
                    'stock_pf_id' => $pf->id,
                    'site_id' => $this->site_id
                ]);
            }

            $soldeBoulangerieAvant = $stockBoulangerie->solde;

            // Mouvement
            \App\Models\MouvementStockPf::create([
                'stock_pf_id' => $pf->id,
                'quantite' => $this->quantite_exp,
                'reste_stock_pf' => $pf->solde - $this->quantite_exp,
                'reste_boulangerie' => $soldeBoulangerieAvant + $this->quantite_exp,
                'site_id' => $this->site_id
            ]);

            // Maj stocks
            $pf->decrement('solde', $this->quantite_exp);
            $stockBoulangerie->increment('solde', $this->quantite_exp);
        });

        session()->flash('success', 'Produits expédiés vers le point de vente avec succès.');
        $this->dispatch('closeModal', ['id' => 'shipModal']);
        $this->resetFields();
    }

    public function openMassShipModal()
    {
        if (empty($this->selectedPfs)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Veuillez sélectionner au moins un produit.']);
            return;
        }

        $this->massQtys = [];
        foreach ($this->selectedPfs as $id) {
            $this->massQtys[$id] = '';
        }

        $this->massSiteId = null;
        $this->dispatch('openModal', ['id' => 'massShipModal']);
    }

    public function storeMassShip()
    {
        $this->validate([
            'massSiteId' => 'required|exists:sites,id',
            'massQtys.*' => 'required|numeric|min:0.01',
        ], [
            'massQtys.*.required' => 'La quantité est obligatoire.',
            'massQtys.*.min' => 'La quantité doit être positive.',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () {
            foreach ($this->selectedPfs as $pfId) {
                $pf = StockPf::findOrFail($pfId);
                $qty = $this->massQtys[$pfId];

                if ($pf->solde < $qty) {
                    throw new \Exception("Stock insuffisant pour {$pf->designation}.");
                }

                $stockBoulangerie = \App\Models\StockBoulangerie::firstOrCreate(
                    ['stock_pf_id' => $pf->id, 'site_id' => $this->massSiteId],
                    ['solde' => 0]
                );

                $soldeBoulangerieAvant = $stockBoulangerie->solde;

                // Créer le mouvement
                \App\Models\MouvementStockPf::create([
                    'stock_pf_id' => $pf->id,
                    'quantite' => $qty,
                    'reste_stock_pf' => $pf->solde - $qty,
                    'reste_boulangerie' => $soldeBoulangerieAvant + $qty,
                    'site_id' => $this->massSiteId
                ]);

                // Mettre à jour les stocks
                $pf->decrement('solde', $qty);
                $stockBoulangerie->increment('solde', $qty);
            }
        });

        session()->flash('success', 'Expédition en masse réussie !');
        $this->dispatch('closeModal', ['id' => 'massShipModal']);
        $this->resetFields();
    }

    public function delete($id)
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $pf = StockPf::findOrFail($id);
        $pf->delete();
        session()->flash('success', 'Suppression effectuée avec succès !');
    }

    public function render()
    {
        $produits = StockPf::where('designation', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $sites = \App\Models\Site::orderBy('nom', 'ASC')->get();

        $tot = StockPf::selectRaw('SUM(prix * solde) as total_valeur')->first()->total_valeur ?? 0;

        return view('livewire.bakery.stock.pf', [
            'produits' => $produits,
            'sites' => $sites,
            'tot' => $tot
        ])->layout('components.layouts.app', ['title' => 'Stock Produits Finis']);
    }
}
