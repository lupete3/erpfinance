<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\StockMaison;
use App\Models\StockUsine;
use App\Models\MouvementStockMp;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Usine extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $matiere_premiere_id, $quantite;
    public $isTransferMode = false;
    public $adjustmentQuantity, $selectedUsineId;

    protected $rules = [
        'matiere_premiere_id' => 'required|exists:stock_maisons,id',
        'quantite' => 'required|numeric|min:0.01',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->matiere_premiere_id = null;
        $this->quantite = '';
        $this->isTransferMode = false;
    }

    public function openTransferModal()
    {
        $this->resetFields();
        $this->isTransferMode = true;
        $this->dispatch('openModal', ['id' => 'usineModal']);
    }

    public function storeTransfer()
    {
        $this->validate();

        $stockMaison = StockMaison::findOrFail($this->matiere_premiere_id);

        if ($stockMaison->solde < $this->quantite) {
            $this->addError('quantite', 'La quantité demandée est supérieure au solde disponible au Dépôt (' . $stockMaison->solde . ' ' . $stockMaison->unite . ').');
            return;
        }

        DB::transaction(function () use ($stockMaison) {
            $stockUsine = StockUsine::where('id_stock_maisons', $this->matiere_premiere_id)->first();

            // Si l'entrée Usine n'existe pas (par sécurité, car elle devrait être créée avec StockMaison)
            if (!$stockUsine) {
                $stockUsine = StockUsine::create([
                    'id_stock_maisons' => $this->matiere_premiere_id,
                    'solde' => 0
                ]);
            }

            $soldeMaisonAvant = $stockMaison->solde;
            $soldeUsineAvant = $stockUsine->solde;

            // Mettre à jour les stocks
            $stockMaison->decrement('solde', $this->quantite);
            $stockUsine->increment('solde', $this->quantite);

            // Créer le mouvement
            MouvementStockMp::create([
                'id_stock_mp' => $this->matiere_premiere_id,
                'quantite' => $this->quantite,
                'reste_maison' => $soldeMaisonAvant - $this->quantite,
                'reste_usine' => $soldeUsineAvant + $this->quantite
            ]);
        });

        session()->flash('success', 'Transfert du dépôt vers l\'usine effectué avec succès.');
        $this->dispatch('closeModal', ['id' => 'usineModal']);
        $this->resetFields();
    }

    public function openAdjustmentModal($id)
    {
        if (auth()->user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->selectedUsineId = $id;
        $stockUsine = StockUsine::findOrFail($id);
        $this->adjustmentQuantity = $stockUsine->solde;
        $this->dispatch('openModal', ['id' => 'adjustmentModal']);
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

        $stockUsine = StockUsine::findOrFail($this->selectedUsineId);
        $stockUsine->update([
            'solde' => $this->adjustmentQuantity,
        ]);

        session()->flash('success', 'Stock usine ajusté avec succès.');
        $this->dispatch('closeModal', ['id' => 'adjustmentModal']);
        $this->reset(['adjustmentQuantity', 'selectedUsineId']);
    }

    public function render()
    {
        $matiresPremieres = StockUsine::with('stockMaison')
            ->whereHas('stockMaison', function ($query) {
                $query->where('designation', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $allMatires = StockMaison::orderBy('designation', 'ASC')->get();

        $tot = StockUsine::join('stock_maisons', 'stock_usines.id_stock_maisons', '=', 'stock_maisons.id')
            ->selectRaw('SUM(stock_maisons.prix * stock_usines.solde) as total_valeur')
            ->first()->total_valeur ?? 0;

        return view('livewire.bakery.stock.usine', [
            'matiresPremieres' => $matiresPremieres,
            'allMatires' => $allMatires,
            'tot' => $tot
        ])->layout('components.layouts.app', ['title' => 'Stock MP Usine']);
    }
}
