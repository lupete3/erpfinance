<?php

namespace App\Livewire\Bakery;

use App\Models\AchatStockMaison;
use App\Models\DetteFournisseur;
use App\Models\Fournisseur;
use App\Models\StockMaison;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class AchatStock extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $fournisseur_id;
    public $checkedMps = []; // Array of selected MP IDs
    public $mpData = [];    // Array keyed by MP ID: [quantite, prix, montant_paye]
    public $editingAchatId;
    public $isEditMode = false;

    // Validation rules will be dynamic in store() for arrays
    protected $rules = [
        'fournisseur_id' => 'required|exists:fournisseurs,id',
        'mpData.*.quantite' => 'required|numeric|min:0.01',
        'mpData.*.prix' => 'required|numeric|min:0',
        'mpData.*.montant_paye' => 'required|numeric|min:0',
    ];

    public function updatedCheckedMps($value)
    {
        // When checking/unchecking, ensure mpData is synchronized
        foreach ($this->checkedMps as $mpId) {
            if (!isset($this->mpData[$mpId])) {
                $mp = StockMaison::find($mpId);
                $this->mpData[$mpId] = [
                    'designation' => $mp->designation,
                    'unite' => $mp->unite,
                    'quantite' => 1,
                    'prix' => $mp->prix ?? 0,
                    'montant_paye' => $mp->prix ?? 0,
                ];
            }
        }

        // Clean up mpData for unchecked items
        foreach ($this->mpData as $mpId => $data) {
            if (!in_array($mpId, $this->checkedMps)) {
                unset($this->mpData[$mpId]);
            }
        }
    }

    public function updatedMpData($value, $key)
    {
        // Handle sub-key updates if needed, like recalcs
        // Example key: 5.quantite
        if (str_contains($key, '.quantite') || str_contains($key, '.prix')) {
            $parts = explode('.', $key);
            $mpId = $parts[0];
            $data = $this->mpData[$mpId];
            $total = (float) $data['quantite'] * (float) $data['prix'];
            // Auto-set montant_paye to total by default if it was equal before
            $this->mpData[$mpId]['montant_paye'] = $total;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->fournisseur_id = null;
        $this->checkedMps = [];
        $this->mpData = [];
        $this->editingAchatId = null;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetFields();
        $this->dispatch('openModal', ['id' => 'achatModal']);
    }

    public function store()
    {
        $this->validate();

        if (empty($this->checkedMps)) {
            $this->addError('checkedMps', 'Veuillez sélectionner au moins une matière première.');
            return;
        }

        DB::transaction(function () {
            foreach ($this->mpData as $mpId => $data) {
                // Mise à jour du stock
                $matierePremiere = StockMaison::find($mpId);
                $matierePremiere->prix = $data['prix'];
                $matierePremiere->solde += $data['quantite'];
                $matierePremiere->save();

                // Création de l'achat
                $achatMP = AchatStockMaison::create([
                    'prix_achat' => $data['prix'],
                    'quantite' => $data['quantite'],
                    'montant_paye' => $data['montant_paye'],
                    'id_fournisseur' => $this->fournisseur_id,
                    'id_stock_maisons' => $mpId,
                ]);

                // Gestion de la dette
                $totalAchat = (float) $data['prix'] * (float) $data['quantite'];
                if ($data['montant_paye'] < $totalAchat) {
                    $montantDette = $totalAchat - $data['montant_paye'];

                    DetteFournisseur::create([
                        'id_fournisseur' => $this->fournisseur_id,
                        'id_achat' => $achatMP->id,
                        'montant_dette' => $montantDette,
                        'reste_a_payer' => $montantDette,
                        'est_soldee' => false,
                    ]);
                }
            }
        });

        session()->flash('success', 'Achats enregistrés avec succès.');
        $this->dispatch('closeModal', ['id' => 'achatModal']);
        $this->resetFields();
    }

    public function edit($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        // For simplicity, we keep edit for single records as it was before
        $this->isEditMode = true;
        $this->editingAchatId = $id;
        $achat = AchatStockMaison::findOrFail($id);

        $this->fournisseur_id = $achat->id_fournisseur;
        $this->checkedMps = [$achat->id_stock_maisons];
        $this->mpData[$achat->id_stock_maisons] = [
            'designation' => $achat->stockMaison->designation ?? '-',
            'unite' => $achat->stockMaison->unite ?? '',
            'quantite' => $achat->quantite,
            'prix' => $achat->prix_achat,
            'montant_paye' => $achat->montant_paye,
        ];

        $this->dispatch('openModal', ['id' => 'achatModal']);
    }

    public function update()
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $achat = AchatStockMaison::findOrFail($this->editingAchatId);
            $ancienneQuantite = $achat->quantite;
            $ancienneMPId = $achat->id_stock_maisons;

            // Get the data from mpData (there should be only one since it's an edit of a single record)
            $mpId = array_key_first($this->mpData);
            $data = $this->mpData[$mpId];

            // Ajustement du stock
            if ($ancienneMPId == $mpId) {
                $matierePremiere = StockMaison::find($mpId);
                $matierePremiere->solde = $matierePremiere->solde - $ancienneQuantite + $data['quantite'];
                $matierePremiere->prix = $data['prix'];
                $matierePremiere->save();
            } else {
                $ancienneMP = StockMaison::find($ancienneMPId);
                $ancienneMP->decrement('solde', $ancienneQuantite);

                $nouvelleMP = StockMaison::find($mpId);
                $nouvelleMP->increment('solde', $data['quantite']);
                $nouvelleMP->prix = $data['prix'];
                $nouvelleMP->save();
            }

            // Mise à jour de l'achat
            $achat->update([
                'prix_achat' => $data['prix'],
                'quantite' => $data['quantite'],
                'montant_paye' => $data['montant_paye'],
                'id_fournisseur' => $this->fournisseur_id,
                'id_stock_maisons' => $mpId,
            ]);

            // Mise à jour de la dette
            $totalAchat = (float) $data['prix'] * (float) $data['quantite'];
            $dette = DetteFournisseur::where('id_achat', $achat->id)->first();

            if ($data['montant_paye'] < $totalAchat) {
                $montantDette = $totalAchat - $data['montant_paye'];

                if ($dette) {
                    $dette->update([
                        'id_fournisseur' => $this->fournisseur_id,
                        'montant_dette' => $montantDette,
                        'reste_a_payer' => $montantDette,
                        'est_soldee' => false,
                    ]);
                } else {
                    DetteFournisseur::create([
                        'id_fournisseur' => $this->fournisseur_id,
                        'id_achat' => $achat->id,
                        'montant_dette' => $montantDette,
                        'reste_a_payer' => $montantDette,
                        'est_soldee' => false,
                    ]);
                }
            } elseif ($dette) {
                $dette->delete();
            }
        });

        session()->flash('success', 'Achat mis à jour avec succès.');
        $this->dispatch('closeModal', ['id' => 'achatModal']);
        $this->resetFields();
    }

    public function delete($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        DB::transaction(function () use ($id) {
            $achat = AchatStockMaison::findOrFail($id);

            // Retirer du stock
            $matierePremiere = StockMaison::find($achat->id_stock_maisons);
            if ($matierePremiere) {
                $matierePremiere->decrement('solde', $achat->quantite);
            }

            // Supprimer dette associée
            DetteFournisseur::where('id_achat', $id)->delete();

            $achat->delete();
        });

        session()->flash('success', 'Achat supprimé et stock ajusté.');
    }

    #[Layout('components.layouts.app')]
    #[Title('Achats Matières Premières')]
    public function render()
    {
        $achats = AchatStockMaison::with(['fournisseur', 'stockMaison'])
            ->whereHas('stockMaison', function ($query) {
                $query->where('designation', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('fournisseur', function ($query) {
                $query->where('nom', 'like', '%' . $this->search . '%');
            })
            ->orderBy('id', 'DESC')
            ->paginate(15);

        $fournisseurs = Fournisseur::orderBy('nom', 'ASC')->get();
        $stockMaisons = StockMaison::orderBy('designation', 'ASC')->get();

        return view('livewire.bakery.achat-stock', [
            'achats' => $achats,
            'fournisseurs' => $fournisseurs,
            'stockMaisons' => $stockMaisons,
        ]);
    }
}
