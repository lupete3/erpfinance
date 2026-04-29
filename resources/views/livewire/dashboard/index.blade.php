<div>
    {{-- Page Title --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <h4 class="fw-bold">
                <span class="text-muted fw-light">{{ __('menu.tableau_de_bord') }} /</span> {{ __('Boulangerie') }}
            </h4>
        </div>
    </div>

    @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
        {{-- KPI Cards --}}
        <div class="row g-3 mb-4">
            {{-- Stock Matières Premières Dépôt --}}
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-muted d-block mb-1">{{ __('Stock MP Dépôt') }}</span>
                                <div class="d-flex align-items-center">
                                    <h3 class="mb-0 me-2">{{ number_format($valeurStockMaison, 0, ',', ' ') }}</h3>
                                    <p class="text-success mb-0">FC</p>
                                </div>
                                <small class="text-muted">{{ __('Valeur totale') }}</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-package fs-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Matières Premières Usine --}}
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-muted d-block mb-1">{{ __('Stock MP Usine') }}</span>
                                <div class="d-flex align-items-center">
                                    <h3 class="mb-0 me-2">{{ number_format($valeurStockUsine, 0, ',', ' ') }}</h3>
                                    <p class="text-success mb-0">FC</p>
                                </div>
                                <small class="text-muted">{{ __('Valeur totale') }}</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="bx bx-buildings fs-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Produits Finis --}}
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-muted d-block mb-1">{{ __('Stock Produits Finis') }}</span>
                                <div class="d-flex align-items-center">
                                    <h3 class="mb-0 me-2">{{ number_format($valeurStockPf, 0, ',', ' ') }}</h3>
                                    <p class="text-success mb-0">FC</p>
                                </div>
                                <small class="text-muted">{{ __('Valeur totale') }}</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="bx bx-box fs-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stock Points de Vente --}}
            <div class="col-xl-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-muted d-block mb-1">{{ __('Stock Points de Vente') }}</span>
                                <div class="d-flex align-items-center">
                                    <h3 class="mb-0 me-2">{{ number_format($valeurStockBoulangerie, 0, ',', ' ') }}</h3>
                                    <p class="text-success mb-0">FC</p>
                                </div>
                                <small class="text-muted">{{ __('Valeur totale') }}</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="bx bx-cart fs-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
        {{-- Today's Activity --}}
        <div class="row g-3 mb-4">
            {{-- Achats du jour --}}
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">{{ __('Achats du jour') }}</h5>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-shopping-bag fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="mb-0">{{ $achatsJour }}</h4>
                                <small class="text-muted">{{ __('Transactions') }}</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0 text-primary">{{ number_format($montantAchatsJour, 0, ',', ' ') }} FC</h4>
                                <small class="text-muted">{{ __('Montant total') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ventes du jour --}}
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">{{ __('Ventes du jour') }}</h5>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-trending-up fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="mb-0">{{ $ventesJour }}</h4>
                                <small class="text-muted">{{ __('Commandes') }}</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0 text-success">{{ number_format($montantVentesJour, 0, ',', ' ') }} FC</h4>
                                <small class="text-muted">{{ __('Montant total') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dépenses du jour --}}
            <div class="col-xl-4 col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">{{ __('Dépenses du jour') }}</h5>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-wallet fs-3"></i>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h4 class="mb-0">{{ $depensesJour }}</h4>
                                <small class="text-muted">{{ __('Transactions') }}</small>
                            </div>
                            <div class="text-end">
                                <h4 class="mb-0 text-danger">{{ number_format($montantDepensesJour, 0, ',', ' ') }} FC</h4>
                                <small class="text-muted">{{ __('Montant total') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
        {{-- Recent Transactions --}}
        <div class="row g-3">
            {{-- Recent Achats --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Achats récents') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    @forelse($recentAchats as $achat)
                                        <tr>
                                            <td class="text-nowrap">
                                                <h6 class="mb-0">{{ $achat->stockMaison->designation ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $achat->fournisseur->nom ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="mb-0">
                                                    {{ number_format($achat->prix_achat * $achat->quantite, 0, ',', ' ') }}
                                                </h6>
                                                <small class="text-muted">{{ $achat->quantite }}
                                                    {{ $achat->stockMaison->unite ?? '' }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                <small>{{ __('Aucun achat récent') }}</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Ventes --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Ventes récentes') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    @forelse($recentVentes as $vente)
                                        <tr>
                                            <td class="text-nowrap">
                                                <h6 class="mb-0">Commande #{{ $vente->id }}</h6>
                                                <small class="text-muted">{{ $vente->site->nom ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="mb-0">{{ number_format($vente->montant, 0, ',', ' ') }} FC</h6>
                                                @if($vente->reste > 0)
                                                    <small class="text-danger">Dette:
                                                        {{ number_format($vente->reste, 0, ',', ' ') }}</small>
                                                @else
                                                    <small class="text-success">Payé</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                <small>{{ __('Aucune vente récente') }}</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Dépenses --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ __('Dépenses récentes') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-sm">
                                <tbody>
                                    @forelse($recentDepenses as $depense)
                                        <tr>
                                            <td class="text-nowrap">
                                                <h6 class="mb-0">{{ $depense->motif }}</h6>
                                                <small class="text-muted">{{ $depense->personne ?? 'N/A' }}</small>
                                            </td>
                                            <td class="text-end">
                                                <h6 class="mb-0 text-danger">{{ number_format($depense->montant, 0, ',', ' ') }}
                                                    FC</h6>
                                                <small class="text-muted">{{ $depense->created_at->format('d/m H:i') }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">
                                                <small>{{ __('Aucune dépense récente') }}</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- For restricted users, show a welcome message --}}
        <div class="row">
            <div class="col-12">
                <div class="card bg-label-primary border-0 shadow-none">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="text-primary mb-1">{{ __('Bienvenue, ') }} {{ Auth::user()->name }} !</h4>
                                <p class="mb-0">{{ __('Tableau de bord de gestion opérationnelle.') }}</p>
                            </div>
                            <div class="avatar avatar-lg">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="bx bx-user fs-3"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-4 text-center">
                <div class="alert alert-info border-info">
                    <i class="bx bx-info-circle me-1"></i>
                    {{ __('Les indicateurs financiers sont masqués pour votre compte. Veuillez consulter vos modules opérationnels dans le menu.') }}
                </div>
            </div>
        </div>
    @endif
</div>