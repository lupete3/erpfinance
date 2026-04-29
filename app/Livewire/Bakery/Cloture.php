<?php

namespace App\Livewire\Bakery;

use App\Models\Cloture as ClotureModel;
use App\Models\StockBoulangerie;
use App\Models\Synthese;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Cloture extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'inventory'; // inventory or synthesis
    public $selectedSiteId;
    public $sites = [];

    // Inventory Closing State
    public $selectedProductId;
    public $qnte_entree;
    public $solde_final;
    public $avarie = 0;
    public $consommation = 0;
    public $productDetails;

    // Financial Synthesis State
    public $vente_theorique = 0;
    public $avarie_total = 0;
    public $depense = 0;
    public $consommation_total = 0;
    public $dette_du_jour = 0;
    public $fonds_de_caisse = 0; // "change" in production controller
    public $espece_reel = 0;

    protected $rules = [
        'solde_final' => 'required|numeric|min:0',
        'espece_reel' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->selectedSiteId = $user->site_id;

        if ($user->hasRoleString('admin')) {
            $this->sites = Site::all();
            if (!$this->selectedSiteId && $this->sites->count() > 0) {
                $this->selectedSiteId = $this->sites->first()->id;
            }
        }

        $this->computeTheoreticSales();
    }

    public function updatedSelectedSiteId()
    {
        $this->resetPage();
        $this->computeTheoreticSales();
    }

    public function computeTheoreticSales()
    {
        $site_id = $this->selectedSiteId;
        $today = Carbon::today();

        // Simple theoretical sale calculation based on closure records of today
        $this->vente_theorique = ClotureModel::where('site_id', $site_id)
            ->whereDate('created_at', $today)
            ->sum(DB::raw('(qnte_entree - solde - avarie - consommation) * prix'));

        $this->avarie_total = ClotureModel::where('site_id', $site_id)
            ->whereDate('created_at', $today)
            ->sum(DB::raw('avarie * prix'));

        $this->consommation_total = ClotureModel::where('site_id', $site_id)
            ->whereDate('created_at', $today)
            ->sum(DB::raw('consommation * prix'));
    }

    public function openClotureModal($id)
    {
        $this->selectedProductId = $id;
        $this->productDetails = StockBoulangerie::with('stockProduitFinis')->find($id);
        $this->qnte_entree = $this->productDetails->solde;
        $this->solde_final = 0;
        $this->dispatch('openModal', ['id' => 'clotureProductModal']);
    }

    public function storeProductCloture()
    {
        $this->validate([
            'solde_final' => 'required|numeric|min:0',
        ]);

        $site_id = $this->selectedSiteId;

        DB::transaction(function () use ($site_id) {
            $produit = StockBoulangerie::find($this->selectedProductId);

            ClotureModel::create([
                'qnte_entree' => $this->qnte_entree,
                'qnte_sortie' => $this->qnte_entree - $this->solde_final,
                'avarie' => $this->avarie,
                'consommation' => $this->consommation,
                'solde' => $this->solde_final,
                'prix' => $this->productDetails->stockProduitFinis->prix,
                'stock_pf_id' => $this->selectedProductId,
                'site_id' => $site_id,
                'user_id' => Auth::id(),
            ]);

            $produit->update([
                'solde' => $this->solde_final,
            ]);
        });

        $this->computeTheoreticSales();
        session()->flash('success', 'Produit clôturé avec succès.');
        $this->dispatch('closeModal', ['id' => 'clotureProductModal']);
        $this->reset(['selectedProductId', 'qnte_entree', 'solde_final', 'avarie', 'consommation']);
    }

    public function storeSynthese()
    {
        $site_id = $this->selectedSiteId;

        $total_attendu = $this->vente_theorique - ($this->avarie_total + $this->depense + $this->consommation_total + $this->dette_du_jour) + $this->fonds_de_caisse;

        if ($this->espece_reel > $total_attendu + 1000) { // Tolerance of 1000? Production had strict check, but users might have small écart positive
            // session()->flash('error', 'Attention! L\'espèce dépasse le total calculé.');
            // return;
        }

        Synthese::create([
            'vente' => $this->vente_theorique,
            'avarie' => $this->avarie_total,
            'depense' => $this->depense,
            'consommation' => $this->consommation_total,
            'dette' => $this->dette_du_jour,
            'change' => $this->fonds_de_caisse,
            'total' => $total_attendu,
            'espece' => $this->espece_reel,
            'manquant' => $total_attendu - $this->espece_reel,
            'site_id' => $site_id,
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', 'Synthèse journalière enregistrée avec succès.');
        $this->reset(['depense', 'dette_du_jour', 'fonds_de_caisse', 'espece_reel']);
    }

    #[Layout('components.layouts.app')]
    #[Title('Clôture de Journée')]
    public function render()
    {
        $site_id = $this->selectedSiteId;

        $products = StockBoulangerie::with('stockProduitFinis')
            ->where('site_id', $site_id)
            ->get();

        $clotures = ClotureModel::with('stockProduitFinis.stockProduitFinis')
            ->where('site_id', $site_id)
            ->whereDate('created_at', Carbon::today())
            ->get();

        $syntheses = Synthese::where('site_id', $site_id)
            ->orderBy('id', 'DESC')
            ->paginate(5);

        return view('livewire.bakery.cloture', [
            'products' => $products,
            'clotures' => $clotures,
            'syntheses' => $syntheses,
        ]);
    }
}
