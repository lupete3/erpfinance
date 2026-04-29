<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\StockMaison;
use App\Models\StockUsine;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Maison extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $designation, $unite, $prix, $solde, $configuration, $auto_production = false;
    public $isEditMode = false;
    public $editingMaisonId = null;
    public $adjustmentQuantity;

    // Transfer fields
    public $transferQuantity;
    public $selectedMaisonId;
    public $maisonDetails;

    // Mass selection fields
    public $selectedMaisons = [];
    public $massQtys = []; // [maison_id => quantity]

    protected $rules = [
        'designation' => 'required|string|max:255',
        'unite' => 'required|string|max:50',
        'prix' => 'required|numeric|min:0',
        'solde' => 'nullable|numeric|min:0',
        'configuration' => 'nullable|numeric|min:0',
        'auto_production' => 'boolean',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->designation = '';
        $this->unite = '';
        $this->prix = 0;
        $this->solde = 0;
        $this->configuration = 0;
        $this->auto_production = false;
        $this->editingMaisonId = null;
        $this->isEditMode = false;
        $this->selectedMaisons = [];
        $this->massQtys = [];
    }

    public function create()
    {
        $this->resetFields();
        $this->dispatch('openModal', ['id' => 'maisonModal']);
    }

    public function store()
    {
        if (in_array(auth()->user()->role, ['geran_depot_magasin', 'geran_depot_usine', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $maison = StockMaison::create([
                'designation' => $this->designation,
                'unite' => $this->unite,
                'prix' => $this->prix,
                'solde' => $this->solde ?? 0,
                'configuration' => $this->configuration ?? 0,
                'auto_production' => $this->auto_production ?? false,
            ]);

            // Comme dans le contrôleur de production, on crée automatiquement l'entrée Usine associée
            StockUsine::create([
                'id_stock_maisons' => $maison->id,
                'solde' => 0,
            ]);
        });

        session()->flash('success', 'Matière première ajoutée avec succès.');
        $this->dispatch('closeModal', ['id' => 'maisonModal']);
        $this->resetFields();
    }

    public function edit($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_magasin', 'geran_depot_usine', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->isEditMode = true;
        $this->editingMaisonId = $id;
        $maison = StockMaison::findOrFail($id);

        $this->designation = $maison->designation;
        $this->unite = $maison->unite;
        $this->prix = $maison->prix;
        $this->solde = $maison->solde;
        $this->configuration = $maison->configuration;
        $this->auto_production = (bool) $maison->auto_production;

        $this->dispatch('openModal', ['id' => 'maisonModal']);
    }

    public function update()
    {
        if (in_array(auth()->user()->role, ['geran_depot_magasin', 'geran_depot_usine', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        $maison = StockMaison::findOrFail($this->editingMaisonId);
        $maison->update([
            'designation' => $this->designation,
            'unite' => $this->unite,
            'prix' => $this->prix,
            'solde' => $this->solde ?? 0,
            'configuration' => $this->configuration ?? 0,
            'auto_production' => $this->auto_production ?? false,
        ]);

        session()->flash('success', 'Mise à jour effectuée avec succès.');
        $this->dispatch('closeModal', ['id' => 'maisonModal']);
        $this->resetFields();
    }

    public function openAdjustmentModal($id)
    {
        if (auth()->user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->editingMaisonId = $id;
        $maison = StockMaison::findOrFail($id);
        $this->adjustmentQuantity = $maison->solde;
        $this->dispatch('openModal', ['id' => 'adjustmentModalMaison']);
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

        $maison = StockMaison::findOrFail($this->editingMaisonId);
        $maison->update([
            'solde' => $this->adjustmentQuantity,
        ]);

        session()->flash('success', 'Stock dépôt ajusté avec succès.');
        $this->dispatch('closeModal', ['id' => 'adjustmentModalMaison']);
        $this->reset(['adjustmentQuantity', 'editingMaisonId']);
    }

    public function delete($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_magasin', 'geran_depot_usine'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $maison = StockMaison::findOrFail($id);
        $maison->delete();
        session()->flash('success', 'Suppression effectuée avec succès !');
    }

    public function openTransferModal($id)
    {
        $this->selectedMaisonId = $id;
        $this->maisonDetails = StockMaison::findOrFail($id);
        $this->transferQuantity = 0;
        $this->dispatch('openModal', ['id' => 'transferModal']);
    }

    public function storeTransfer()
    {
        $this->validate([
            'transferQuantity' => 'required|numeric|min:0.01',
        ]);

        $maison = StockMaison::findOrFail($this->selectedMaisonId);

        if ($this->transferQuantity > $maison->solde) {
            $this->addError('transferQuantity', 'La quantité dépasse le solde disponible au dépôt.');
            return;
        }

        DB::transaction(function () use ($maison) {
            // 1. Décrémenter le stock dépôt (Maison)
            $maison->decrement('solde', $this->transferQuantity);

            // 2. Incrémenter le stock usine
            $usine = StockUsine::where('id_stock_maisons', $maison->id)->first();
            if (!$usine) {
                $usine = StockUsine::create([
                    'id_stock_maisons' => $maison->id,
                    'solde' => 0
                ]);
            }
            $usine->increment('solde', $this->transferQuantity);

            // 3. Créer le log de mouvement
            \App\Models\MouvementStockMp::create([
                'id_stock_mp' => $maison->id,
                'quantite' => $this->transferQuantity,
                'reste_maison' => $maison->solde,
                'reste_usine' => $usine->solde,
                'statut' => 1
            ]);
        });

        session()->flash('success', 'Transfert vers l\'usine effectué avec succès.');
        $this->dispatch('closeModal', ['id' => 'transferModal']);
        $this->reset(['transferQuantity', 'selectedMaisonId', 'maisonDetails']);
    }

    public function openMassTransferModal()
    {
        if (empty($this->selectedMaisons)) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Veuillez sélectionner au moins une matière première.']);
            return;
        }

        $this->massQtys = [];
        foreach ($this->selectedMaisons as $id) {
            $this->massQtys[$id] = '';
        }

        $this->dispatch('openModal', ['id' => 'massTransferModal']);
    }

    public function storeMassTransfer()
    {
        $this->validate([
            'massQtys.*' => 'required|numeric|min:0.01',
        ], [
            'massQtys.*.required' => 'La quantité est obligatoire.',
            'massQtys.*.min' => 'La quantité doit être positive.',
        ]);

        DB::transaction(function () {
            foreach ($this->selectedMaisons as $maisonId) {
                $maison = StockMaison::findOrFail($maisonId);
                $qty = $this->massQtys[$maisonId];

                if ($qty > $maison->solde) {
                    throw new \Exception("Stock insuffisant pour {$maison->designation}.");
                }

                // 2. Incrémenter le stock usine
                $usine = StockUsine::firstOrCreate(
                    ['id_stock_maisons' => $maison->id],
                    ['solde' => 0]
                );

                // 1. Décrémenter le stock dépôt (Maison)
                $maison->decrement('solde', $qty);
                $usine->increment('solde', $qty);

                // 3. Créer le log de mouvement
                \App\Models\MouvementStockMp::create([
                    'id_stock_mp' => $maison->id,
                    'quantite' => $qty,
                    'reste_maison' => $maison->solde,
                    'reste_usine' => $usine->solde,
                    'statut' => 1
                ]);
            }
        });

        session()->flash('success', 'Transfert en masse effectué avec succès.');
        $this->dispatch('closeModal', ['id' => 'massTransferModal']);
        $this->reset(['selectedMaisons', 'massQtys']);
    }

    #[Layout('components.layouts.app')]
    #[Title('Stock MP Dépôt')]
    public function render()
    {
        $matiresPremieres = StockMaison::where('designation', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $tot = StockMaison::selectRaw('SUM(prix * solde) as total_valeur')->first()->total_valeur ?? 0;

        return view('livewire.bakery.stock.maison', [
            'matiresPremieres' => $matiresPremieres,
            'tot' => $tot
        ]);
    }
}
