<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\StockMaison;
use App\Models\StockUsine;
use App\Models\StockPf;
use App\Models\StockBoulangerie;
use App\Models\AchatStockMaison;
use App\Models\Vente;
use App\Models\Depense;
use App\Models\Fournisseur;
use App\Models\CommandeClient;

class Index extends Component
{
    public $title = 'Tableau de bord - Gestion Boulangerie';

    // Stats
    public $valeurStockMaison = 0;
    public $valeurStockUsine = 0;
    public $valeurStockPf = 0;
    public $valeurStockBoulangerie = 0;

    public $achatsJour = 0;
    public $montantAchatsJour = 0;

    public $ventesJour = 0;
    public $montantVentesJour = 0;

    public $depensesJour = 0;
    public $montantDepensesJour = 0;

    // Recent transactions
    public $recentAchats;
    public $recentVentes;
    public $recentDepenses;

    public function mount()
    {
        $this->loadStatistics();
        $this->loadRecentTransactions();
    }

    public function loadStatistics()
    {
        $user = auth()->user();
        $isFactoryManager = $user->role === 'geran_depot_usine';
        $isStoreManager = $user->role === 'geran_depot_magasin';
        $isRestricted = $isFactoryManager || $isStoreManager;

        // Valeurs des stocks
        if (!$isRestricted) {
            $this->valeurStockMaison = StockMaison::all()->sum(function ($item) {
                return $item->prix * $item->solde;
            });

            $this->valeurStockUsine = StockUsine::with('stockMaison')->get()->sum(function ($item) {
                return $item->stockMaison->prix * $item->solde;
            });

            $this->valeurStockPf = StockPf::all()->sum(function ($item) {
                return $item->prix * $item->solde;
            });

            $this->valeurStockBoulangerie = StockBoulangerie::with('stockProduitFinis')->get()->sum(function ($item) {
                return $item->stockProduitFinis->prix * $item->solde;
            });
        }

        if (!$isRestricted) {
            // Achats du jour
            $achatsToday = AchatStockMaison::whereDate('created_at', Carbon::today())->get();
            $this->achatsJour = $achatsToday->count();
            $this->montantAchatsJour = $achatsToday->sum(function ($achat) {
                return $achat->prix_achat * $achat->quantite;
            });

            // Ventes du jour
            $commandesToday = CommandeClient::whereDate('created_at', Carbon::today())->get();
            $this->ventesJour = $commandesToday->count();
            $this->montantVentesJour = $commandesToday->sum('montant');

            // Dépenses du jour
            $depensesToday = Depense::whereDate('created_at', Carbon::today())->get();
            $this->depensesJour = $depensesToday->count();
            $this->montantDepensesJour = $depensesToday->sum('montant');
        }
    }

    public function loadRecentTransactions()
    {
        $user = auth()->user();
        $isFactoryManager = $user->role === 'geran_depot_usine';
        $isStoreManager = $user->role === 'geran_depot_magasin';
        $isRestricted = $isFactoryManager || $isStoreManager;

        if (!$isRestricted) {
            $this->recentAchats = AchatStockMaison::with('stockMaison', 'fournisseur')
                ->latest()
                ->take(5)
                ->get();

            $this->recentVentes = CommandeClient::with('ventes', 'site')
                ->latest()
                ->take(5)
                ->get();

            $this->recentDepenses = Depense::latest()
                ->take(5)
                ->get();
        } else {
            $this->recentAchats = collect();
            $this->recentVentes = collect();
            $this->recentDepenses = collect();
        }
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
