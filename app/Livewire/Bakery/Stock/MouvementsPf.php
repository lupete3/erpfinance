<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\MouvementStockPf;
use App\Models\StockPf;
use App\Models\StockBoulangerie;
use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class MouvementsPf extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $dateDebut;
    public $dateFin;
    public $selectedPfId;
    public $selectedSiteId;

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
        if (in_array($propertyName, ['dateDebut', 'dateFin', 'selectedPfId', 'selectedSiteId'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = MouvementStockPf::with(['stockPf', 'site'])
            ->orderBy('created_at', 'DESC');

        if ($this->dateDebut) {
            $query->whereDate('created_at', '>=', $this->dateDebut);
        }

        if ($this->dateFin) {
            $query->whereDate('created_at', '<=', $this->dateFin);
        }

        if ($this->selectedPfId) {
            $query->where('stock_pf_id', $this->selectedPfId);
        }

        if ($this->selectedSiteId) {
            $query->where('site_id', $this->selectedSiteId);
        }

        return view('livewire.bakery.stock.mouvements-pf', [
            'mouvements' => $query->paginate(15),
            'produits' => StockPf::orderBy('designation')->get(),
            'sites' => Site::orderBy('nom')->get()
        ])->layout('components.layouts.app', ['title' => 'Historique des Mouvements PF']);
    }

    public function edit($id)
    {
        $this->editingMvtId = $id;
        $this->mvtDetails = MouvementStockPf::with(['stockPf', 'site'])->findOrFail($id);
        $this->newQuantite = $this->mvtDetails->quantite;
        $this->dispatch('openModal', ['id' => 'editMvtPfModal']);
    }

    public function update()
    {
        $this->validate([
            'newQuantite' => 'required|numeric|min:0.01',
        ]);

        $mvt = MouvementStockPf::findOrFail($this->editingMvtId);
        $pf = StockPf::findOrFail($mvt->stock_pf_id);
        $boulangerie = StockBoulangerie::where('stock_pf_id', $pf->id)
            ->where('site_id', $mvt->site_id)
            ->first();

        $diff = $this->newQuantite - $mvt->quantite;

        // Si on augmente le transfert, on vérifie si le stock Fournil est suffisant
        if ($diff > 0 && ($pf->solde < $diff)) {
            $this->addError('newQuantite', 'Le stock au fournil est insuffisant pour augmenter ce transfert.');
            return;
        }

        DB::transaction(function () use ($mvt, $pf, $boulangerie, $diff) {
            // Ajuster les stocks
            $pf->decrement('solde', $diff);
            if ($boulangerie) {
                $boulangerie->increment('solde', $diff);
            }

            // Mettre à jour le mouvement
            $mvt->update([
                'quantite' => $this->newQuantite,
                'reste_stock_pf' => $pf->solde,
                'reste_boulangerie' => $boulangerie ? $boulangerie->solde : 0,
            ]);
        });

        session()->flash('success', 'Mouvement PF mis à jour avec succès.');
        $this->dispatch('closeModal', ['id' => 'editMvtPfModal']);
        $this->reset(['editingMvtId', 'newQuantite', 'mvtDetails']);
    }

    public function delete($id)
    {
        $mvt = MouvementStockPf::findOrFail($id);
        $pf = StockPf::find($mvt->stock_pf_id);
        $boulangerie = StockBoulangerie::where('stock_pf_id', $mvt->stock_pf_id)
            ->where('site_id', $mvt->site_id)
            ->first();

        DB::transaction(function () use ($mvt, $pf, $boulangerie) {
            if ($pf) {
                $pf->increment('solde', $mvt->quantite);
            }
            if ($boulangerie) {
                $boulangerie->decrement('solde', $mvt->quantite);
            }
            $mvt->delete();
        });

        session()->flash('success', 'Mouvement PF annulé et supprimé. Les stocks ont été restaurés.');
    }
}
