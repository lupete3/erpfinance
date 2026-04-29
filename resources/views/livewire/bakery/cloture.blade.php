<div>
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Clôture de Caisse & Stock') }}
        </h4>

        @if(Auth::user()->hasRoleString('admin'))
            <div class="w-100 w-md-auto" style="min-width: 250px;">
                <label class="form-label small mb-1">{{ __('Filtrer par Site') }}</label>
                <select class="form-select border-primary" wire:model.live="selectedSiteId">
                    @foreach($sites as $site)
                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="nav-align-top mb-4">
        <div class="d-flex overflow-x-auto pb-2 scrollbar-hidden">
            <ul class="nav nav-pills flex-nowrap" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link @if($activeTab == 'inventory') active @endif" role="tab"
                        wire:click="$set('activeTab', 'inventory')">
                        <i class="bx bx-package me-1"></i> {{ __('1. Clôture Stock') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link @if($activeTab == 'synthesis') active @endif" role="tab"
                        wire:click="$set('activeTab', 'synthesis')">
                        <i class="bx bx-calculator me-1"></i> {{ __('2. Synthèse Financière') }}
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link @if($activeTab == 'history') active @endif" role="tab"
                        wire:click="$set('activeTab', 'history')">
                        <i class="bx bx-history me-1"></i> {{ __('3. Historique') }}
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content border shadow-none p-0 bg-transparent">
            {{-- Tab 1: Inventory Reconciliation --}}
            <div class="tab-pane fade @if($activeTab == 'inventory') show active @endif">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header border-bottom bg-label-primary py-3">
                                <h5 class="card-title mb-0">{{ __('Produits en Rayon') }}</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Produit') }}</th>
                                                <th>{{ __('Stock Actuel') }}</th>
                                                <th class="text-end">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $p)
                                                <tr>
                                                    <td>{{ $p->stockProduitFinis->designation }}</td>
                                                    <td><span class="badge bg-label-info">{{ $p->solde }}</span></td>
                                                    <td class="text-end">
                                                        <button class="btn btn-xs btn-primary"
                                                            wire:click="openClotureModal({{ $p->id }})">
                                                            {{ __('Clôturer') }}
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header border-bottom bg-label-success py-3">
                                <h5 class="card-title mb-0">{{ __('Clôtures du Jour') }}</h5>
                            </div>
                            <div class="card-body pt-3">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Produit') }}</th>
                                                <th>{{ __('Vendu') }}</th>
                                                <th>{{ __('Reste Relevé') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($clotures as $c)
                                                <tr>
                                                    <td>{{ $c->stockProduitFinis->stockProduitFinis->designation }}</td>
                                                    <td><span class="text-success fw-bold">{{ $c->qnte_sortie }}</span></td>
                                                    <td>{{ $c->solde }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center py-4 text-muted small">
                                                        {{ __('Aucune clôture effectuée aujourd\'hui.') }}
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
            </div>

            {{-- Tab 2: Financial Synthesis --}}
            <div class="tab-pane fade @if($activeTab == 'synthesis') show active @endif">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h5 class="mb-4 text-primary"><i
                                        class="bx bx-list-check me-2"></i>{{ __('Calcul des Ventes Théoriques') }}</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Ventes Brutes (calculées du stock)') }} :</span>
                                    <span class="fw-bold">{{ number_format($vente_theorique, 0, ',', ' ') }} FC</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Avaries') }} :</span>
                                    <span class="text-danger">- {{ number_format($avarie_total, 0, ',', ' ') }}
                                        FC</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Consommations') }} :</span>
                                    <span class="text-warning">- {{ number_format($consommation_total, 0, ',', ' ') }}
                                        FC</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="h6">{{ __('TOTAL NET ATTENDU') }} :</span>
                                    <span
                                        class="h5 mb-0 text-success fw-bold">{{ number_format($vente_theorique - $avarie_total - $consommation_total, 0, ',', ' ') }}
                                        FC</span>
                                </div>
                            </div>
                            <div class="col-md-6 ps-md-4">
                                <h5 class="mb-4 text-primary"><i
                                        class="bx bx-wallet me-2"></i>{{ __('Réconciliation de Caisse') }}</h5>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="form-label small">{{ __('Dépenses Journée') }}</label>
                                        <input type="number" step="any" class="form-control" wire:model.live="depense">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">{{ __('Dettes Client (Crédit)') }}</label>
                                        <input type="number" step="any" class="form-control"
                                            wire:model.live="dette_du_jour">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">{{ __('Fonds de Caisse / Change') }}</label>
                                        <input type="number" step="any" class="form-control"
                                            wire:model.live="fonds_de_caisse">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small">{{ __('Espèce Réel en Main') }}</label>
                                        <input type="number" step="any"
                                            class="form-control border-primary fw-bold text-primary"
                                            wire:model.live="espece_reel">
                                    </div>
                                </div>

                                <div class="bg-light p-3 rounded mt-4">
                                    @php
                                        $attendu = $vente_theorique - ($avarie_total + $depense + $consommation_total + $dette_du_jour) + $fonds_de_caisse;
                                        $diff = $espece_reel - $attendu;
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{ __('Solde de Caisse Théorique') }} :</span>
                                        <span class="fw-bold">{{ number_format($attendu, 0, ',', ' ') }} FC</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>{{ __('Écart / Manquant') }} :</span>
                                        <span class="fw-bold @if($diff < 0) text-danger @else text-success @endif">
                                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', ' ') }} FC
                                        </span>
                                    </div>
                                </div>

                                <button class="btn btn-primary w-100 mt-4" wire:click="storeSynthese">
                                    {{ __('Enregistrer la Synthèse') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 3: History --}}
            <div class="tab-pane fade @if($activeTab == 'history') show active @endif">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Ventes') }}</th>
                                    <th>{{ __('Dépenses') }}</th>
                                    <th>{{ __('Espèce') }}</th>
                                    <th>{{ __('Écart') }}</th>
                                    <th>{{ __('Utilisateur') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($syntheses as $s)
                                    <tr>
                                        <td>{{ $s->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($s->vente, 0, ',', ' ') }} FC</td>
                                        <td>{{ number_format($s->depense, 0, ',', ' ') }} FC</td>
                                        <td><span class="fw-bold">{{ number_format($s->espece, 0, ',', ' ') }} FC</span>
                                        </td>
                                        <td class="@if($s->manquant > 0) text-danger @else text-success @endif">
                                            {{ number_format(-$s->manquant, 0, ',', ' ') }} FC
                                        </td>
                                        <td>{{ $s->user->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $syntheses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Product Cloture Modal --}}
    <div wire:ignore.self class="modal fade" id="clotureProductModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Clôturer un Produit') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeProductCloture">
                    <div class="modal-body">
                        @if($productDetails)
                            <div class="alert alert-primary py-2 mb-4">
                                <h6 class="mb-0 text-white">{{ $productDetails->stockProduitFinis->designation }}</h6>
                                <p class="mb-0 small text-white">{{ __('Total Entrée (Matin + Réappros)') }} :
                                    <strong>{{ $qnte_entree }}</strong>
                                </p>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Reste en Stock (Relevé physique)') }}</label>
                                <input type="number" step="any" class="form-control form-control-lg fw-bold"
                                    wire:model="solde_final">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">{{ __('Avaries (Pertes)') }}</label>
                                <input type="number" step="any" class="form-control" wire:model="avarie">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">{{ __('Consommation Interne') }}</label>
                                <input type="number" step="any" class="form-control" wire:model="consommation">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Valider') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                window.addEventListener('openModal', (event) => {
                    const modalElement = document.getElementById(event.detail[0].id);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modal.show();
                    }
                });

                window.addEventListener('closeModal', (event) => {
                    const modalElement = document.getElementById(event.detail[0].id);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }
                });
            });
        </script>
    @endpush
</div>