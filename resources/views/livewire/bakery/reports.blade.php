<div>
    <style>
        .nav-scrollable {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE, Edge */
        }

        .nav-scrollable::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .nav-scrollable .nav-item {
            flex: 0 0 auto;
        }
    </style>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-4 gap-3">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Rapports & Statistiques') }}
        </h4>
        <div class="d-flex flex-column flex-sm-row align-items-center gap-2 gap-sm-3 bg-white p-2 rounded shadow-sm border w-100 w-md-auto">
            <select class="form-select form-select-sm border-0 w-100 w-sm-auto" wire:model.live="dateFilter" style="min-width: 150px;">
                <option value="today">{{ __('Aujourd\'hui') }}</option>
                <option value="week">{{ __('Cette Semaine') }}</option>
                <option value="month">{{ __('Ce Mois') }}</option>
                <option value="range">{{ __('Période Personnalisée') }}</option>
            </select>
            @if($dateFilter == 'range')
                <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                    <input type="date" class="form-control form-control-sm border-0" wire:model.live="startDate">
                    <span class="small text-muted">{{ __('au') }}</span>
                    <input type="date" class="form-control form-control-sm border-0" wire:model.live="endDate">
                </div>
            @endif
        </div>
    </div>

    <div class="nav-align-top mb-4">
        <ul class="nav nav-pills nav-scrollable mb-3" role="tablist">
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'sales') active @endif" role="tab"
                    wire:click="$set('activeTab', 'sales')">
                    <i class="bx bx-cart me-1"></i> {{ __('Ventes') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'production') active @endif" role="tab"
                    wire:click="$set('activeTab', 'production')">
                    <i class="bx bx-repost me-1"></i> {{ __('Production') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'stocks') active @endif" role="tab"
                    wire:click="$set('activeTab', 'stocks')">
                    <i class="bx bx-box me-1"></i> {{ __('États des Stocks') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'debts') active @endif" role="tab"
                    wire:click="$set('activeTab', 'debts')">
                    <i class="bx bx-credit-card me-1"></i> {{ __('Dettes & Paiements') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'purchases') active @endif" role="tab"
                    wire:click="$set('activeTab', 'purchases')">
                    <i class="bx bx-cart-add me-1"></i> {{ __('Achats & Dépenses') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'cash') active @endif" role="tab"
                    wire:click="$set('activeTab', 'cash')">
                    <i class="bx bx-book me-1"></i> {{ __('Livre de Caisse') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'financial') active @endif" role="tab"
                    wire:click="$set('activeTab', 'financial')">
                    <i class="bx bx-money-withdraw me-1"></i> {{ __('Rapport Financier') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'situation') active @endif" role="tab"
                    wire:click="$set('activeTab', 'situation')">
                    <i class="bx bx-chart me-1"></i> {{ __('Situation Globale') }}
                </button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link @if($activeTab == 'transfers') active @endif" role="tab"
                    wire:click="$set('activeTab', 'transfers')">
                    <i class="bx bx-transfer me-1"></i> {{ __('Transferts') }}
                </button>
            </li>
        </ul>
        <div class="tab-content border-top-0 shadow-none p-0 bg-transparent pt-3">
            {{-- Sales Tab --}}
            <div class="tab-pane @if($activeTab == 'sales') show active @endif" wire:key="tab-sales">
                <div class="card">
                    <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">{{ __('Rapport des Ventes') }}</h5>
                        <button wire:click="exportPdf('sales')" class="btn btn-sm btn-danger text-nowrap">
                            <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter en PDF') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('N°') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('sale.client') }}</th>
                                    <th>{{ __('Montant Total') }}</th>
                                    <th>{{ __('Payé') }}</th>
                                    <th>{{ __('Reste') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->sales as $sale)
                                    <tr>
                                        <td>#{{ $sale->id }}</td>
                                        <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $sale->client->nom ?? 'N/A' }}</td>
                                        <td class="fw-bold">{{ number_format($sale->montant, 0, ',', ' ') }} FC</td>
                                        <td class="text-success">{{ number_format($sale->paye, 0, ',', ' ') }} FC</td>
                                        <td class="text-danger">{{ number_format($sale->reste, 0, ',', ' ') }} FC</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $this->sales->links() }}
                    </div>
                </div>
            </div>

            {{-- Production Tab --}}
            <div class="tab-pane @if($activeTab == 'production') show active @endif" wire:key="tab-production">
                <div class="card">
                    <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">{{ __('Rapport de Production') }}</h5>
                        <button wire:click="exportPdf('production')" class="btn btn-sm btn-danger text-nowrap">
                            <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter en PDF') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Produit Fini') }}</th>
                                    <th class="text-center">{{ __('Qté') }}</th>
                                    <th class="text-end">{{ __('PU') }}</th>
                                    <th class="text-end">{{ __('Valeur') }}</th>
                                    <th>{{ __('Ingrédients (Compo.)') }}</th>
                                    <th class="text-end">{{ __('Coût Prod.') }}</th>
                                    <th class="text-end">{{ __('Bénéfice') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $grandTotalValeur = 0;
                                    $grandTotalCout = 0;
                                    $grandTotalBenefice = 0;
                                @endphp
                                @foreach($this->productions as $p)
                                    @php
                                        $valeur = $p->quantite * ($p->produitFinis->prix ?? 0);
                                        $coutMp = $p->compositions->sum(fn($c) => $c->quantite * ($c->prix ?? 0));
                                        $coutTotal = $coutMp + $p->charge_personnel + $p->autres_charges;
                                        $benefice = $valeur - $coutTotal;
                                        
                                        $grandTotalValeur += $valeur;
                                        $grandTotalCout += $coutTotal;
                                        $grandTotalBenefice += $benefice;
                                    @endphp
                                    <tr>
                                        <td>{{ $p->created_at->format('d/m/Y') }}</td>
                                        <td><span class="fw-bold">{{ $p->produitFinis->designation ?? 'N/A' }}</span></td>
                                        <td class="text-center"><span class="badge bg-label-success">{{ $p->quantite }}</span></td>
                                        <td class="text-end">{{ number_format($p->produitFinis->prix ?? 0, 0, ',', ' ') }}</td>
                                        <td class="text-end fw-bold">{{ number_format($valeur, 0, ',', ' ') }}</td>
                                        <td class="small">
                                            <ul class="list-unstyled mb-0" style="font-size: 0.75rem;">
                                                @foreach($p->compositions as $comp)
                                                    <li>• {{ $comp->designation }}: {{ $comp->quantite }} {{ $comp->unite }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td class="text-end text-danger">{{ number_format($coutTotal, 0, ',', ' ') }}</td>
                                        <td class="text-end fw-bold @if($benefice >= 0) text-success @else text-danger @endif">
                                            {{ number_format($benefice, 0, ',', ' ') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4">{{ __('TOTAUX') }}</th>
                                    <th class="text-end fw-bold text-primary">{{ number_format($grandTotalValeur, 0, ',', ' ') }} FC</th>
                                    <th></th>
                                    <th class="text-end fw-bold text-danger">{{ number_format($grandTotalCout, 0, ',', ' ') }} FC</th>
                                    <th class="text-end fw-bold @if($grandTotalBenefice >= 0) text-success @else text-danger @endif">
                                        {{ number_format($grandTotalBenefice, 0, ',', ' ') }} FC
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $this->productions->links() }}
                    </div>
                </div>
            </div>

            <div class="tab-pane @if($activeTab == 'stocks') show active @endif" wire:key="tab-stocks">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 w-100 w-md-auto">
                        <label class="form-label mb-0 fw-bold text-nowrap">{{ __('Filtrer par Site :') }}</label>
                        <select class="form-select form-select-sm w-100 w-sm-auto" wire:model.live="selectedStockSiteId">
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button wire:click="exportPdf('stocks')" class="btn btn-sm btn-outline-danger text-nowrap">
                        <i class="bx bxs-file-pdf me-1"></i> {{ __('Export Global') }}
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div
                                class="card-header bg-label-primary py-2 d-flex justify-content-between align-items-center">
                                <strong>{{ __('Stock MP (Dépôt)') }}</strong>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-primary">{{ count($this->stocks['maison']) }} {{ __('articles') }}</span>
                                    <button wire:click="exportPdf('stocks_maison')" class="btn btn-xs btn-icon btn-outline-danger" title="{{ __('Exporter PDF') }}">
                                        <i class="bx bxs-file-pdf"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Article') }}</th>
                                            <th class="text-end">{{ __('Solde') }}</th>
                                            <th class="text-end">{{ __('PU') }}</th>
                                            <th class="text-end">{{ __('Valeur') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalMaison = 0; @endphp
                                        @foreach($this->stocks['maison'] as $s)
                                            @php 
                                                $valeur = $s->solde * $s->prix;
                                                $totalMaison += $valeur;
                                            @endphp
                                            <tr>
                                                <td>{{ $s->designation }}</td>
                                                <td class="text-end fw-bold text-nowrap">
                                                    {{ number_format($s->solde, 1, ',', ' ') }} {{ $s->unite }}
                                                </td>
                                                <td class="text-end small">{{ number_format($s->prix, 0, ',', ' ') }}</td>
                                                <td class="text-end fw-bold">{{ number_format($valeur, 0, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3">{{ __('TOTAL') }}</th>
                                            <th class="text-end text-primary">{{ number_format($totalMaison, 0, ',', ' ') }} FC</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div
                                class="card-header bg-label-info py-2 d-flex justify-content-between align-items-center">
                                <strong>{{ __('Stock MP (Usine)') }}</strong>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-info">{{ count($this->stocks['usine']) }} {{ __('articles') }}</span>
                                    <button wire:click="exportPdf('stocks_usine')" class="btn btn-xs btn-icon btn-outline-danger" title="{{ __('Exporter PDF') }}">
                                        <i class="bx bxs-file-pdf"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Article') }}</th>
                                            <th class="text-end">{{ __('Solde') }}</th>
                                            <th class="text-end">{{ __('PU') }}</th>
                                            <th class="text-end">{{ __('Valeur') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalUsine = 0; @endphp
                                        @foreach($this->stocks['usine'] as $s)
                                            @php 
                                                $prix = $s->stockMaison->prix ?? 0;
                                                $valeur = $s->solde * $prix;
                                                $totalUsine += $valeur;
                                            @endphp
                                            <tr>
                                                <td>{{ $s->stockMaison->designation ?? 'N/A' }}</td>
                                                <td class="text-end fw-bold text-nowrap">
                                                    {{ number_format($s->solde, 1, ',', ' ') }}
                                                    {{ $s->stockMaison->unite ?? '' }}
                                                </td>
                                                <td class="text-end small">{{ number_format($prix, 0, ',', ' ') }}</td>
                                                <td class="text-end fw-bold">{{ number_format($valeur, 0, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3">{{ __('TOTAL') }}</th>
                                            <th class="text-end text-primary">{{ number_format($totalUsine, 0, ',', ' ') }} FC</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div
                                class="card-header bg-label-success py-2 d-flex justify-content-between align-items-center">
                                <strong>{{ __('Stock Produits Finis') }}</strong>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success">{{ count($this->stocks['pf']) }} {{ __('articles') }}</span>
                                    <button wire:click="exportPdf('stocks_pf')" class="btn btn-xs btn-icon btn-outline-danger" title="{{ __('Exporter PDF') }}">
                                        <i class="bx bxs-file-pdf"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Produit') }}</th>
                                            <th class="text-end">{{ __('Solde') }}</th>
                                            <th class="text-end">{{ __('PU') }}</th>
                                            <th class="text-end">{{ __('Valeur') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalPf = 0; @endphp
                                        @foreach($this->stocks['pf'] as $s)
                                            @php 
                                                $valeur = $s->solde * $s->prix;
                                                $totalPf += $valeur;
                                            @endphp
                                            <tr>
                                                <td>{{ $s->designation }}</td>
                                                <td class="text-end fw-bold text-nowrap">
                                                    {{ number_format($s->solde, 0, ',', ' ') }}
                                                </td>
                                                <td class="text-end small">{{ number_format($s->prix, 0, ',', ' ') }}</td>
                                                <td class="text-end fw-bold">{{ number_format($valeur, 0, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3">{{ __('TOTAL') }}</th>
                                            <th class="text-end text-primary">{{ number_format($totalPf, 0, ',', ' ') }} FC</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div
                                class="card-header bg-label-warning py-2 d-flex justify-content-between align-items-center">
                                <strong>{{ __('Stock Points de Vente') }}</strong>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning text-dark">{{ count($this->stocks['boulangerie']) }} {{ __('articles') }}</span>
                                    <button wire:click="exportPdf('stocks_boulangerie')" class="btn btn-xs btn-icon btn-outline-danger" title="{{ __('Exporter PDF') }}">
                                        <i class="bx bxs-file-pdf"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Produit') }}</th>
                                            <th class="text-end">{{ __('Solde') }}</th>
                                            <th class="text-end">{{ __('PU') }}</th>
                                            <th class="text-end">{{ __('Valeur') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalBoul = 0; @endphp
                                        @foreach($this->stocks['boulangerie'] as $s)
                                            @php 
                                                $prix = $s->stockProduitFinis->prix ?? 0;
                                                $valeur = $s->solde * $prix;
                                                $totalBoul += $valeur;
                                            @endphp
                                            <tr>
                                                <td>{{ $s->stockProduitFinis->designation ?? 'N/A' }}</td>
                                                <td class="text-end fw-bold text-nowrap">
                                                    {{ number_format($s->solde, 0, ',', ' ') }}
                                                </td>
                                                <td class="text-end small">{{ number_format($prix, 0, ',', ' ') }}</td>
                                                <td class="text-end fw-bold">{{ number_format($valeur, 0, ',', ' ') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3">{{ __('TOTAL') }}</th>
                                            <th class="text-end text-primary">{{ number_format($totalBoul, 0, ',', ' ') }} FC</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Financial Tab --}}
            <div class="tab-pane @if($activeTab == 'financial') show active @endif" wire:key="tab-financial">
                <div class="card">
                    <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">{{ __('Rapport Financier') }}</h5>
                        <button wire:click="exportPdf('financial')" class="btn btn-sm btn-danger text-nowrap">
                            <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter en PDF') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Ventes') }}</th>
                                    <th>{{ __('Dépenses') }}</th>
                                    <th>{{ __('Consom') }}</th>
                                    <th>{{ __('Reste Attendu') }}</th>
                                    <th>{{ __('Espèce') }}</th>
                                    <th>{{ __('Écart') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->financials as $f)
                                    <tr>
                                        <td>{{ $f->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($f->vente, 0, ',', ' ') }} FC</td>
                                        <td>{{ number_format($f->depense, 0, ',', ' ') }} FC</td>
                                        <td>{{ number_format($f->consommation, 0, ',', ' ') }} FC</td>
                                        <td class="fw-bold">{{ number_format($f->total, 0, ',', ' ') }} FC</td>
                                        <td class="text-primary fw-bold">{{ number_format($f->espece, 0, ',', ' ') }} FC
                                        </td>
                                        <td>
                                            @php $diff = $f->espece - $f->total; @endphp
                                            <span
                                                class="badge @if($diff < 0) bg-label-danger @else bg-label-success @endif">
                                                {{ number_format($diff, 0, ',', ' ') }} FC
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $this->financials->links() }}
                    </div>
                </div>
            </div>

            {{-- Debts & Payments Tab --}}
            <div class="tab-pane @if($activeTab == 'debts') show active @endif" wire:key="tab-debts">
                <div class="d-flex justify-content-end mb-3">
                    <button wire:click="exportPdf('debts')" class="btn btn-sm btn-danger">
                        <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter Dettes & Paiements') }}
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Histoiques des Paiements Clients') }}</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('sale.client') }}</th>
                                            <th>{{ __('Commande') }}</th>
                                            <th class="text-end">{{ __('Montant Versé') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->payments as $p)
                                            <tr>
                                                <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                                                <td>{{ $p->client->nom ?? 'N/A' }}</td>
                                                <td>#{{ $p->commande_client_id }}</td>
                                                <td class="text-end fw-bold text-success">
                                                    {{ number_format($p->montant, 0, ',', ' ') }} FC
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">{{ $this->payments->links() }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-label-danger py-2">
                                <strong>{{ __('Dettes Clients en Attente') }}</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('sale.client') }}</th>
                                            <th>{{ __('Commande') }}</th>
                                            <th class="text-end">{{ __('Reste à Payer') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->debts as $d)
                                            <tr>
                                                <td>{{ $d->client->nom ?? 'N/A' }}</td>
                                                <td>#{{ $d->id }} ({{ $d->created_at->format('d/m/Y') }})</td>
                                                <td class="text-end fw-bold text-danger">
                                                    {{ number_format($d->reste, 0, ',', ' ') }} FC
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">{{ $this->debts->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Purchases & Expenses Tab --}}
            <div class="tab-pane @if($activeTab == 'purchases') show active @endif" wire:key="tab-purchases">
                <div class="d-flex justify-content-end mb-3">
                    <button wire:click="exportPdf('purchases')" class="btn btn-sm btn-danger">
                        <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter Achats & Dépenses') }}
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('Historique des Achats MP') }}</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Fournisseur') }}</th>
                                            <th>{{ __('Article') }}</th>
                                            <th>{{ __('Quantité') }}</th>
                                            <th class="text-end">{{ __('Montant') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->purchases as $pur)
                                            <tr>
                                                <td>{{ $pur->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $pur->fournisseur->nom ?? 'N/A' }}</td>
                                                <td>{{ $pur->stockMaison->designation ?? 'N/A' }}</td>
                                                <td>{{ $pur->quantite }} {{ $pur->stockMaison->unite ?? '' }}</td>
                                                <td class="text-end fw-bold">
                                                    {{ number_format($pur->prix_achat * $pur->quantite, 0, ',', ' ') }}
                                                    FC
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">{{ $this->purchases->links() }}</div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-label-warning py-2">
                                <strong>{{ __('Dépenses Diverses') }}</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Motif') }}</th>
                                            <th>{{ __('Bénéficiaire') }}</th>
                                            <th class="text-end">{{ __('Montant') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->expenses as $exp)
                                            <tr>
                                                <td>{{ $exp->created_at->format('d/m/Y') }}</td>
                                                <td>{{ $exp->motif }}</td>
                                                <td>{{ $exp->personne ?? 'N/A' }}</td>
                                                <td class="text-end fw-bold text-danger">
                                                    {{ number_format($exp->montant, 0, ',', ' ') }} FC
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">{{ $this->expenses->links() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cash Book Tab --}}
            <div class="tab-pane @if($activeTab == 'cash') show active @endif" wire:key="tab-cash">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Livre de Caisse') }}</h5>
                        <button wire:click="exportPdf('cash')" class="btn btn-sm btn-danger">
                            <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter en PDF') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Utilisateur') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Motif') }}</th>
                                    <th class="text-end">{{ __('Montant') }}</th>
                                    <th class="text-end">{{ __('Solde après') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->cashOperations as $op)
                                    <tr>
                                        <td>{{ $op->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $op->user->name ?? 'N/A' }}</td>
                                        <td>
                                            @php $isEntree = str_contains(strtolower($op->type_operation), 'entre'); @endphp
                                            <span
                                                class="badge @if($isEntree) bg-label-success @else bg-label-danger @endif">
                                                {{ ucfirst($op->type_operation) }}
                                            </span>
                                        </td>
                                        <td>{{ $op->motif }}</td>
                                        <td class="text-end fw-bold @if($isEntree) text-success @else text-danger @endif">
                                            {{ $isEntree ? '+' : '-' }}
                                            {{ number_format($op->montant, 0, ',', ' ') }} FC
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($op->solde_apres_operation, 0, ',', ' ') }} FC
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $this->cashOperations->links() }}
                    </div>
                </div>
            </div>

            {{-- Situation Tab --}}
            <div class="tab-pane @if($activeTab == 'situation') show active @endif" wire:key="tab-situation">
                <div class="d-flex justify-content-end mb-3">
                    <button wire:click="exportPdf('situation')" class="btn btn-sm btn-danger">
                        <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter la Situation Globale') }}
                    </button>
                </div>
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">{{ __('Valeur Actuelle des Stocks') }} <small
                                        class="text-muted">(Prix de revient/Vente)</small></h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Stock MP (Dépôt)') }}</span>
                                    <span class="fw-bold">
                                        {{ number_format($this->situation['valeur_stocks']['maison'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Stock MP (Usine)') }}</span>
                                    <span class="fw-bold">
                                        {{ number_format($this->situation['valeur_stocks']['usine'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Stock Produits Finis') }}</span>
                                    <span class="fw-bold">
                                        {{ number_format($this->situation['valeur_stocks']['pf'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Stock Points de Vente') }}</span>
                                    <span class="fw-bold">
                                        {{ number_format($this->situation['valeur_stocks']['boulangerie'], 0, ',', ' ') }}
                                        FC
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between text-primary">
                                    <span class="h5 mb-0">{{ __('TOTAL VALEUR STOCK') }}</span>
                                    <span class="h5 mb-0 fw-bold">
                                        {{ number_format($this->situation['valeur_stocks']['total'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card h-100">
                            <div class="card-header border-bottom">
                                <h5 class="card-title mb-0">{{ __('Flux & Performance sur la Période') }}</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Achats MP') }}</span>
                                    <span class="fw-bold text-danger">-
                                        {{ number_format($this->situation['flux']['achats'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Valeur Production') }}</span>
                                    <span class="fw-bold text-success">+
                                        {{ number_format($this->situation['flux']['production_valeur'], 0, ',', ' ') }}
                                        FC
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Coût Production (MP+Charges)') }}</span>
                                    <span class="fw-bold text-warning">-
                                        {{ number_format($this->situation['flux']['production_cout'], 0, ',', ' ') }} FC
                                    </span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h5 mb-0">{{ __('Bénéfice Théorique sur Production') }}</span>
                                    <span class="h5 mb-0 fw-bold text-success">
                                        {{ number_format($this->situation['flux']['benefice_theorique'], 0, ',', ' ') }}
                                        FC
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transfers Tab --}}
            <div class="tab-pane @if($activeTab == 'transfers') show active @endif" wire:key="tab-transfers">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Rapport des Transferts de Stocks') }}</h5>
                        <button wire:click="exportPdf('transfers')" class="btn btn-sm btn-danger">
                            <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter les Transferts en PDF') }}
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    {{-- MP Transfers (Depot -> Usine) --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-label-primary py-2">
                                <strong>{{ __('Transferts Matières Premières (Dépôt -> Usine)') }}</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Matière') }}</th>
                                            <th class="text-center">{{ __('Quantité') }}</th>
                                            <th class="text-end">{{ __('Dépôt (Après)') }}</th>
                                            <th class="text-end">{{ __('Usine (Après)') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->transferMps as $mvt)
                                            <tr>
                                                <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                                                <td><strong>{{ $mvt->stockMaison->designation ?? 'N/A' }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-primary">
                                                        {{ $mvt->quantite }} {{ $mvt->stockMaison->unite ?? '' }}
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold">{{ $mvt->reste_maison }}</td>
                                                <td class="text-end text-success fw-bold">{{ $mvt->reste_usine }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">
                                {{ $this->transferMps->links() }}
                            </div>
                        </div>
                    </div>

                    {{-- PF Transfers (Fournil -> PV) --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-label-warning py-2">
                                <strong>{{ __('Transferts Produits Finis (Fournil -> Points de Vente)') }}</strong>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Date') }}</th>
                                            <th>{{ __('Produit') }}</th>
                                            <th>{{ __('Destination') }}</th>
                                            <th class="text-center">{{ __('Quantité') }}</th>
                                            <th class="text-end">{{ __('Fournil (Après)') }}</th>
                                            <th class="text-end">{{ __('Site (Après)') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($this->transferPfs as $mvt)
                                            <tr>
                                                <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                                                <td><strong>{{ $mvt->stockPf->designation ?? 'N/A' }}</strong></td>
                                                <td><span class="text-primary">{{ $mvt->site->nom ?? 'N/A' }}</span></td>
                                                <td class="text-center">
                                                    <span class="badge bg-label-warning">
                                                        {{ $mvt->quantite }} pcs
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold">{{ $mvt->reste_stock_pf }}</td>
                                                <td class="text-end text-success fw-bold">{{ $mvt->reste_boulangerie }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer py-2">
                                {{ $this->transferPfs->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>