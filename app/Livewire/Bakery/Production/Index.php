<?php

namespace App\Livewire\Bakery\Production;

use App\Models\Composition;
use App\Models\Production;
use App\Models\StockPf;
use App\Models\StockUsine;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchPf = '';         // Search inside modal – finished products
    public $searchIngredient = ''; // Search inside modal – raw materials
    public $checkedPfs = []; // Array of stock_pf_id => boolean
    public $pfQuantities = []; // Array of stock_pf_id => quantity
    public $charge_personnel = 0;
    public $autres_charges = 0;

    // Ingredient selection
    public $selectedIngredients = []; // Array of ['stock_usine_id' => x, 'designation' => y, 'quantite' => z, 'unite' => u, 'prix' => p]
    public $checkedIngredients = []; // Array of stock_usine_id => boolean
    public $ingredient_id;
    public $ingredient_quantite;

    public $isEditMode = false;
    public $viewingProductionId;

    protected $rules = [
        'charge_personnel' => 'required|numeric|min:0',
        'autres_charges' => 'required|numeric|min:0',
        'selectedIngredients.*.quantite' => 'required|numeric|min:0.01',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createProduction()
    {
        $this->resetFields();
        $this->isEditMode = false;

        // Auto-check raw materials marked as auto_production
        $autoIngredients = StockUsine::with('stockMaison')
            ->whereHas('stockMaison', fn($q) => $q->where('auto_production', true))
            ->get();

        foreach ($autoIngredients as $usineItem) {
            $this->checkedIngredients[$usineItem->id] = true;
            $this->selectedIngredients[] = [
                'stock_usine_id' => $usineItem->id,
                'designation' => $usineItem->stockMaison->designation,
                'quantite' => (float) ($usineItem->stockMaison->configuration ?? 0),
                'unite' => $usineItem->stockMaison->unite,
                'prix' => $usineItem->stockMaison->prix,
            ];
        }

        $this->dispatch('openModal', ['id' => 'productionModal']);
    }

    public function updatedCheckedPfs($value, $id)
    {
        if ($value) {
            if (!isset($this->pfQuantities[$id])) {
                $this->pfQuantities[$id] = 0;
            }
        }
    }

    public function updatedCheckedIngredients($value, $id)
    {
        $id = (int) $id;
        $usineItem = StockUsine::with('stockMaison')->find($id);

        if (!$usineItem)
            return;

        if ($value) {
            // Check stock before adding
            $qtyRequired = (float) ($usineItem->stockMaison->configuration ?? 0);
            if ($usineItem->solde < $qtyRequired) {
                $this->checkedIngredients[$id] = false;
                $this->addError('checkedIngredients', "Stock insuffisant pour {$usineItem->stockMaison->designation}. Disponible: {$usineItem->solde} {$usineItem->stockMaison->unite}.");
                return;
            }

            // Add to selectedIngredients if not already there
            $exists = collect($this->selectedIngredients)->contains('stock_usine_id', $id);
            if (!$exists) {
                $this->selectedIngredients[] = [
                    'stock_usine_id' => $usineItem->id,
                    'designation' => $usineItem->stockMaison->designation,
                    'quantite' => $qtyRequired,
                    'unite' => $usineItem->stockMaison->unite,
                    'prix' => $usineItem->stockMaison->prix,
                ];
            }
        } else {
            // Remove from selectedIngredients
            $this->selectedIngredients = collect($this->selectedIngredients)
                ->reject(fn($item) => $item['stock_usine_id'] == $id)
                ->values()
                ->toArray();
        }
    }

    public function updatedSelectedIngredients($value, $key)
    {
        // $key is format "index.property" e.g. "0.quantite"
        if (str_contains($key, '.quantite')) {
            $parts = explode('.', $key);
            $index = $parts[0];
            $item = $this->selectedIngredients[$index];
            $usineItem = StockUsine::find($item['stock_usine_id']);

            $qty = is_numeric($value) ? (float) $value : 0;

            if ($usineItem && $usineItem->solde < $qty) {
                $this->addError("selectedIngredients.$index.quantite", "Stock insuffisant. Disponible: {$usineItem->solde} {$item['unite']}.");
            } else {
                $this->resetErrorBag("selectedIngredients.$index.quantite");
            }
        }
    }

    public function addIngredient()
    {
        $this->validate([
            'ingredient_id' => 'required|exists:stock_usines,id',
            'ingredient_quantite' => 'required|numeric|min:0.01',
        ]);

        $usineItem = StockUsine::with('stockMaison')->find($this->ingredient_id);

        // Check if already in list
        if (collect($this->selectedIngredients)->contains('stock_usine_id', $this->ingredient_id)) {
            $this->addError('ingredient_id', 'Cette matière première est déjà dans la liste.');
            return;
        }

        // Check stock
        if ($usineItem->solde < $this->ingredient_quantite) {
            $this->addError('ingredient_quantite', 'Stock insuffisant en Usine.');
            return;
        }

        $this->selectedIngredients[] = [
            'stock_usine_id' => $usineItem->id,
            'designation' => $usineItem->stockMaison->designation,
            'quantite' => $this->ingredient_quantite,
            'unite' => $usineItem->stockMaison->unite,
            'prix' => $usineItem->stockMaison->prix,
        ];

        // Also check the checkbox
        $this->checkedIngredients[$this->ingredient_id] = true;

        $this->ingredient_id = null;
        $this->ingredient_quantite = null;
    }

    public function removeIngredient($index)
    {
        $id = $this->selectedIngredients[$index]['stock_usine_id'] ?? null;
        if ($id) {
            $this->checkedIngredients[$id] = false;
        }
        unset($this->selectedIngredients[$index]);
        $this->selectedIngredients = array_values($this->selectedIngredients);
    }

    public function deselectAllIngredients()
    {
        $this->selectedIngredients = [];
        $this->checkedIngredients = [];
    }

    public function store()
    {
        $this->validate();

        $selectedPfIds = array_keys(array_filter($this->checkedPfs));

        if (empty($selectedPfIds)) {
            $this->addError('checkedPfs', 'Vous devez choisir au moins un produit fini.');
            return;
        }

        foreach ($selectedPfIds as $id) {
            if (!isset($this->pfQuantities[$id]) || $this->pfQuantities[$id] <= 0) {
                $this->addError("pfQuantities.$id", 'La quantité doit être supérieure à 0.');
                return;
            }
        }

        /*
        if (empty($this->selectedIngredients)) {
            $this->addError('checkedIngredients', 'Vous devez ajouter au moins un ingrédient.');
            return;
        }
        */

        // Final stock check for all ingredients
        foreach ($this->selectedIngredients as $item) {
            $usine = StockUsine::find($item['stock_usine_id']);
            if ($usine->solde < $item['quantite']) {
                $this->addError('checkedIngredients', "Stock insuffisant pour {$item['designation']}. Requis: {$item['quantite']}, Disponible: {$usine->solde} {$item['unite']}.");
                return;
            }
        }

        $user = auth()->user();
        $isFactoryManager = $user->role === 'geran_depot_usine';

        DB::transaction(function () use ($selectedPfIds, $isFactoryManager) {
            $isFirst = true;

            foreach ($selectedPfIds as $pfId) {
                // 1. Update PF Stock
                $pf = StockPf::find($pfId);
                $qty = $this->pfQuantities[$pfId];
                $pf->increment('solde', $qty);

                // 2. Create Production record
                // Only the first one gets the charges and ingredients
                $production = Production::create([
                    'designation' => $pf->designation . ' (Batch)',
                    'quantite' => $qty,
                    'charge_personnel' => ($isFirst && !$isFactoryManager) ? $this->charge_personnel : 0,
                    'autres_charges' => ($isFirst && !$isFactoryManager) ? $this->autres_charges : 0,
                    'stock_pf_id' => $pfId,
                ]);

                if ($isFirst) {
                    // Update designations with ingredients info
                    if (!empty($this->selectedIngredients)) {
                        $ingredientNames = implode(', ', array_column($this->selectedIngredients, 'designation'));
                        $production->update(['designation' => $pf->designation . ' - Ingrédients: ' . $ingredientNames]);
                    } else {
                        $production->update(['designation' => $pf->designation]);
                    }

                    // 3. Update Ingredients Stock & Create Compositions ONLY for the first product
                    foreach ($this->selectedIngredients as $item) {
                        $usine = StockUsine::find($item['stock_usine_id']);
                        $usine->decrement('solde', $item['quantite']);

                        Composition::create([
                            'stock_usine_id' => $item['stock_usine_id'],
                            'designation' => $item['designation'],
                            'unite' => $item['unite'],
                            'quantite' => $item['quantite'],
                            'prix' => $item['prix'],
                            'production_id' => $production->id,
                        ]);
                    }
                    $isFirst = false;
                }
            }
        });

        session()->flash('success', 'Production enregistrée avec succès.');
        $this->dispatch('closeModal', ['id' => 'productionModal']);
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->checkedPfs = [];
        $this->pfQuantities = [];
        $this->charge_personnel = 0;
        $this->autres_charges = 0;
        $this->selectedIngredients = [];
        $this->checkedIngredients = [];
        $this->ingredient_id = null;
        $this->ingredient_quantite = null;
        $this->searchPf = '';
        $this->searchIngredient = '';
    }

    public function deleteProduction($id)
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        DB::transaction(function () use ($id) {
            $production = Production::with('compositions')->find($id);
            if (!$production)
                return;

            // 1. Restore Raw Materials (StockUsine)
            foreach ($production->compositions as $composition) {
                $usine = StockUsine::find($composition->stock_usine_id);
                if ($usine) {
                    $usine->increment('solde', $composition->quantite);
                }
                $composition->delete();
            }

            // 2. Withdraw Finished Products (StockPf)
            $pf = StockPf::find($production->stock_pf_id);
            if ($pf) {
                $pf->decrement('solde', $production->quantite);
            }

            // 3. Delete Production
            $production->delete();
        });

        session()->flash('success', 'Production supprimée et stocks ajustés.');
    }

    public function editProduction($id)
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->isEditMode = true;
        $this->viewingProductionId = $id;
        $production = Production::find($id);

        $this->charge_personnel = $production->charge_personnel;
        $this->autres_charges = $production->autres_charges;
        // We temporarily store the single PF quantity in a property for the modal
        $this->pfQuantities[$production->stock_pf_id] = $production->quantite;
        $this->checkedPfs[$production->stock_pf_id] = true;

        $this->dispatch('openModal', ['id' => 'productionModal']);
    }

    public function update()
    {
        if (auth()->user()->role === 'geran_depot_usine') {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate([
            'charge_personnel' => 'required|numeric|min:0',
            'autres_charges' => 'required|numeric|min:0',
        ]);

        $production = Production::find($this->viewingProductionId);
        $pfId = $production->stock_pf_id;
        $newQty = $this->pfQuantities[$pfId] ?? 0;

        if ($newQty <= 0) {
            $this->addError("pfQuantities.$pfId", 'La quantité doit être supérieure à 0.');
            return;
        }

        DB::transaction(function () use ($production, $pfId, $newQty) {
            // 1. Adjust PF Stock
            $delta = $newQty - $production->quantite;
            $pf = StockPf::find($pfId);
            $pf->increment('solde', $delta);

            // 2. Update Production
            $production->update([
                'quantite' => $newQty,
                'charge_personnel' => $this->charge_personnel,
                'autres_charges' => $this->autres_charges,
            ]);
        });

        session()->flash('success', 'Production mise à jour avec ajustement des stocks.');
        $this->dispatch('closeModal', ['id' => 'productionModal']);
        $this->resetFields();
    }

    public function viewDetails($id)
    {
        $this->viewingProductionId = $id;
        $this->dispatch('openModal', ['id' => 'detailsModal']);
    }

    public function render()
    {
        $productions = Production::with(['produitFinis', 'compositions'])
            ->where('designation', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        $produitsPfs = StockPf::when($this->searchPf, fn($q) => $q->where('designation', 'like', '%' . $this->searchPf . '%'))
            ->orderBy('designation', 'ASC')->get();

        $matieresPremieres = StockUsine::with('stockMaison')
            ->when($this->searchIngredient, fn($q) => $q->whereHas('stockMaison', fn($sq) => $sq->where('designation', 'like', '%' . $this->searchIngredient . '%')))
            ->get();

        return view('livewire.bakery.production.index', [
            'productions' => $productions,
            'produitsPfs' => $produitsPfs,
            'matieresPremieres' => $matieresPremieres,
            'details' => $this->viewingProductionId ? Production::with('compositions')->find($this->viewingProductionId) : null,
        ])->layout('components.layouts.app', ['title' => 'Journal de Production']);
    }
}
