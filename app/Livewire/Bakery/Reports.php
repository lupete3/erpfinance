<?php

namespace App\Livewire\Bakery;

use App\Models\AchatStockMaison;
use App\Models\Caisse;
use App\Models\Cloture;
use App\Models\CommandeClient;
use App\Models\Production;
use App\Models\StockBoulangerie;
use App\Models\StockMaison;
use App\Models\StockPf;
use App\Models\StockUsine;
use App\Models\Synthese;
use App\Models\MouvementStockMp;
use App\Models\MouvementStockPf;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Reports extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'sales';
    public $dateFilter = 'today';
    public $startDate;
    public $endDate;
    public $selectedStockSiteId;

    public function mount()
    {
        $this->startDate = Carbon::today()->format('Y-m-d');
        $this->endDate = Carbon::today()->format('Y-m-d');
        $this->selectedStockSiteId = Auth::user()->site_id ?? 1;
    }

    public function updatedDateFilter()
    {
        if ($this->dateFilter == 'today') {
            $this->startDate = Carbon::today()->format('Y-m-d');
            $this->endDate = Carbon::today()->format('Y-m-d');
        } elseif ($this->dateFilter == 'week') {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        } elseif ($this->dateFilter == 'month') {
            $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }
        $this->resetPage();
    }

    #[Computed]
    public function sales()
    {
        $site_id = Auth::user()->site_id ?? 1;
        return CommandeClient::with('client')
            ->where('site_id', $site_id)
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function productions()
    {
        return Production::with(['produitFinis', 'compositions'])
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function stocks()
    {
        $site_id = $this->selectedStockSiteId ?: (Auth::user()->site_id ?? 1);
        return [
            'maison' => StockMaison::all(),
            'usine' => StockUsine::with('stockMaison')->get(),
            'pf' => StockPf::all(),
            'boulangerie' => StockBoulangerie::with('stockProduitFinis')->where('site_id', $site_id)->get(),
        ];
    }

    #[Computed]
    public function financials()
    {
        $site_id = Auth::user()->site_id ?? 1;
        return Synthese::with('user')
            ->where('site_id', $site_id)
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function cashOperations()
    {
        return Caisse::with('user')
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('created_at', 'ASC')
            ->paginate(15);
    }

    #[Computed]
    public function situation()
    {
        $dateDebut = Carbon::parse($this->startDate)->startOfDay();
        $dateFin = Carbon::parse($this->endDate)->endOfDay();

        $valStockMaison = StockMaison::all()->sum(fn($item) => $item->prix * $item->solde);
        $valStockUsine = StockUsine::with('stockMaison')->get()->sum(fn($s) => ($s->stockMaison->prix ?? 0) * $s->solde);
        $valStockPf = StockPf::all()->sum(fn($item) => $item->prix * $item->solde);
        $valStockBoulangerie = StockBoulangerie::with('stockProduitFinis')->get()->sum(fn($s) => ($s->stockProduitFinis->prix ?? 0) * $s->solde);

        $achats = AchatStockMaison::whereBetween('created_at', [$dateDebut, $dateFin])->get();
        $totalAchats = $achats->sum(fn($a) => $a->prix_achat * $a->quantite);

        $productions = Production::with(['produitFinis', 'compositions'])
            ->whereBetween('created_at', [$dateDebut, $dateFin])
            ->get();

        $totalValProd = 0;
        $totalCoutProd = 0;

        foreach ($productions as $p) {
            $totalValProd += $p->quantite * ($p->produitFinis->prix ?? 0);
            $coutMp = $p->compositions->sum(fn($c) => $c->quantite * ($c->prix ?? 0));
            $totalCoutProd += $coutMp + $p->charge_personnel + $p->autres_charges;
        }

        return [
            'valeur_stocks' => [
                'maison' => $valStockMaison,
                'usine' => $valStockUsine,
                'pf' => $valStockPf,
                'boulangerie' => $valStockBoulangerie,
                'total' => $valStockMaison + $valStockUsine + $valStockPf + $valStockBoulangerie
            ],
            'flux' => [
                'achats' => $totalAchats,
                'production_valeur' => $totalValProd,
                'production_cout' => $totalCoutProd,
                'benefice_theorique' => $totalValProd - $totalCoutProd
            ]
        ];
    }

    #[Computed]
    public function debts()
    {
        $site_id = Auth::user()->site_id ?? 1;
        return CommandeClient::with('client')
            ->where('site_id', $site_id)
            ->where('reste', '>', 0)
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('reste', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function payments()
    {
        $site_id = Auth::user()->site_id ?? 1;
        return \App\Models\PaiementClient::with(['client', 'commandeClient'])
            ->whereHas('commandeClient', function ($query) use ($site_id) {
                $query->where('site_id', $site_id);
            })
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function purchases()
    {
        return AchatStockMaison::with(['fournisseur', 'stockMaison'])
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function expenses()
    {
        return \App\Models\Depense::whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function transferMps()
    {
        return MouvementStockMp::with(['stockMaison', 'stockUsine'])
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    #[Computed]
    public function transferPfs()
    {
        return MouvementStockPf::with(['stockPf', 'site'])
            ->whereBetween('created_at', [Carbon::parse($this->startDate)->startOfDay(), Carbon::parse($this->endDate)->endOfDay()])
            ->orderBy('id', 'DESC')
            ->paginate(15);
    }

    public function exportPdf($type)
    {
        $data = [];
        $view = '';
        $title = '';

        $startDate = Carbon::parse($this->startDate)->startOfDay();
        $endDate = Carbon::parse($this->endDate)->endOfDay();
        $site_id = Auth::user()->site_id ?? 1;

        switch ($type) {
            case 'sales':
                $data['sales'] = CommandeClient::with('client')
                    ->where('site_id', $site_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.sales';
                $title = 'Rapport des Ventes';
                break;
            case 'production':
                $data['productions'] = Production::with(['produitFinis', 'compositions'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.production';
                $title = 'Rapport de Production';
                break;
            case 'stocks_maison':
                $data['stocks'] = ['maison' => StockMaison::all()];
                $view = 'exports.bakery.stocks';
                $title = 'État du Stock MP (Dépôt)';
                break;
            case 'stocks_usine':
                $data['stocks'] = ['usine' => StockUsine::with('stockMaison')->get()];
                $view = 'exports.bakery.stocks';
                $title = 'État du Stock MP (Usine)';
                break;
            case 'stocks_pf':
                $data['stocks'] = ['pf' => StockPf::all()];
                $view = 'exports.bakery.stocks';
                $title = 'État du Stock Produits Finis';
                break;
            case 'stocks_boulangerie':
                $stock_site_id = $this->selectedStockSiteId ?: (Auth::user()->site_id ?? 1);
                $data['stocks'] = [
                    'boulangerie' => StockBoulangerie::with('stockProduitFinis')->where('site_id', $stock_site_id)->get(),
                    'selected_site' => \App\Models\Site::find($stock_site_id)?->nom ?? 'Tous les sites'
                ];
                $view = 'exports.bakery.stocks';
                $title = 'État du Stock Point de Vente';
                break;
            case 'stocks':
                $stock_site_id = $this->selectedStockSiteId ?: $site_id;
                $data['stocks'] = [
                    'maison' => StockMaison::all(),
                    'usine' => StockUsine::with('stockMaison')->get(),
                    'pf' => StockPf::all(),
                    'boulangerie' => StockBoulangerie::with('stockProduitFinis')->where('site_id', $stock_site_id)->get(),
                    'selected_site' => \App\Models\Site::find($stock_site_id)?->nom ?? 'Tous les sites'
                ];
                $view = 'exports.bakery.stocks';
                $title = 'État Global des Stocks';
                break;
            case 'debts':
                $data['debts'] = CommandeClient::with('client')
                    ->where('site_id', $site_id)
                    ->where('reste', '>', 0)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('reste', 'DESC')
                    ->get();
                $data['payments'] = \App\Models\PaiementClient::with(['client', 'commandeClient'])
                    ->whereHas('commandeClient', function ($query) use ($site_id) {
                        $query->where('site_id', $site_id);
                    })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.debts_payments';
                $title = 'Rapport Dettes & Paiements';
                break;
            case 'purchases':
                $data['purchases'] = AchatStockMaison::with(['fournisseur', 'stockMaison'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $data['expenses'] = \App\Models\Depense::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.purchases_expenses';
                $title = 'Rapport Achats & Dépenses';
                break;
            case 'cash':
                $data['cashOperations'] = Caisse::with('user')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'ASC')
                    ->get();
                $view = 'exports.bakery.cash';
                $title = 'Livre de Caisse';
                break;
            case 'financial':
                $data['financials'] = Synthese::with('user')
                    ->where('site_id', $site_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.financial';
                $title = 'Rapport Financier';
                break;
            case 'situation':
                $data['situation'] = $this->situation;
                $view = 'exports.bakery.situation';
                $title = 'Situation Globale';
                break;
            case 'transfers':
                $data['transferMps'] = MouvementStockMp::with(['stockMaison', 'stockUsine'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $data['transferPfs'] = MouvementStockPf::with(['stockPf', 'site'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('id', 'DESC')
                    ->get();
                $view = 'exports.bakery.transfers';
                $title = 'Rapport des Transferts de Stocks';
                break;
        }

        $data['title'] = $title;
        $data['startDate'] = $this->startDate;
        $data['endDate'] = $this->endDate;

        $pdf = Pdf::loadView($view, $data);
        if ($type == 'production') {
            $pdf->setPaper('a4', 'landscape');
        }
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'rapport_' . $type . '_' . now()->format('YmdHis') . '.pdf');
    }

    public function render()
    {
        $sites = \App\Models\Site::all();
        return view('livewire.bakery.reports', [
            'sites' => $sites
        ])->layout('components.layouts.app', ['title' => 'Rapports d\'activité']);
    }
}
