<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\MouvementStockMp;
use App\Models\StockMaison;
use App\Models\StockUsine;
use Livewire\Component;
use Livewire\WithPagination;

class Mouvements extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $dateDebut;
    public $dateFin;
    public $selectedMatiereId;

    // Edit properties
    public $editingMvtId;
    public $newQuantite;
    public $mvtDetails;

    public function mount()
    {
        $this->dateDebut = now()->startOfMonth()->format('Y-m-d');
        $this->dateFin = now()->format('Y-m-d');
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['dateDebut', 'dateFin', 'selectedMatiereId'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = MouvementStockMp::with(['stockMaison', 'stockUsine'])
            ->orderBy('created_at', 'DESC');

        if ($this->dateDebut) {
            $query->whereDate('created_at', '>=', $this->dateDebut);
        }

        if ($this->dateFin) {
            $query->whereDate('created_at', '<=', $this->dateFin);
        }

        if ($this->selectedMatiereId) {
            $query->where('id_stock_mp', $this->selectedMatiereId);
        }

        return view('livewire.bakery.stock.mouvements', [
            'mouvements' => $query->paginate(15),
            'matieres' => StockMaison::orderBy('designation')->get()
        ])->layout('components.layouts.app', ['title' => 'Historique des Mouvements MP']);
    }

    public function edit($id)
    {
        $this->editingMvtId = $id;
        $this->mvtDetails = MouvementStockMp::with('stockMaison')->findOrFail($id);
        $this->newQuantite = $this->mvtDetails->quantite;
        $this->dispatch('openModal', ['id' => 'editMvtModal']);
    }

    public function update()
    {
        $this->validate([
            'newQuantite' => 'required|numeric|min:0.01',
        ]);

        $mvt = MouvementStockMp::findOrFail($this->editingMvtId);
        $maison = StockMaison::findOrFail($mvt->id_stock_mp);
        $usine = StockUsine::where('id_stock_maisons', $maison->id)->first();

        $diff = $this->newQuantite - $mvt->quantite;

        // Si on augmente le transfert, on vérifie si le stock dépôt est suffisant
        if ($diff > 0 && ($maison->solde < $diff)) {
            $this->addError('newQuantite', 'Le stock au dépôt est insuffisant pour augmenter ce transfert.');
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($mvt, $maison, $usine, $diff) {
            // Ajuster les stocks
            $maison->decrement('solde', $diff);
            $usine->increment('solde', $diff);

            // Mettre à jour le mouvement
            $mvt->update([
                'quantite' => $this->newQuantite,
                'reste_maison' => $maison->solde,
                'reste_usine' => $usine->solde,
            ]);
        });

        session()->flash('success', 'Mouvement mis à jour avec succès.');
        $this->dispatch('closeModal', ['id' => 'editMvtModal']);
        $this->reset(['editingMvtId', 'newQuantite', 'mvtDetails']);
    }

    public function delete($id)
    {
        $mvt = MouvementStockMp::findOrFail($id);
        $maison = StockMaison::find($mvt->id_stock_mp);
        $usine = StockUsine::where('id_stock_maisons', $mvt->id_stock_mp)->first();

        \Illuminate\Support\Facades\DB::transaction(function () use ($mvt, $maison, $usine) {
            if ($maison) {
                $maison->increment('solde', $mvt->quantite);
            }
            if ($usine) {
                $usine->decrement('solde', $mvt->quantite);
            }
            $mvt->delete();
        });

        session()->flash('success', 'Mouvement annulé et supprimé. Les stocks ont été restaurés.');
    }
}
