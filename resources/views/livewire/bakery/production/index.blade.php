<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Journal de Production') }}
    </h4>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="card-title mb-0">{{ __('Productions Journalières') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}" wire:model.live.debounce.300ms="search">
                </div>
                <button class="btn btn-primary text-nowrap" wire:click="createProduction">
                    <i class="bx bx-plus me-1"></i> {{ __('Enregistrer une Production') }}
                </button>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Produit Fini') }}</th>
                        <th>{{ __('Quantité') }}</th>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <th>{{ __('Valeur') }}</th>
                            <th>{{ __('Coût Total') }}</th>
                            <th>{{ __('Bénéfice') }}</th>
                        @endif
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totValeur = 0;
                        $totCout = 0;
                    @endphp
                    @forelse($productions as $prod)
                        @php 
                            $valeur = $prod->quantite * ($prod->produitFinis->prix ?? 0);
                            $coutMP = $prod->compositions->sum(function($c) { return $c->prix * $c->quantite; });
                            $autresFrais = $prod->charge_personnel + $prod->autres_charges;
                            $coutTotal = $coutMP + $autresFrais;
                            $benefice = $valeur - $coutTotal;

                            $totValeur += $valeur;
                            $totCout += $coutTotal;
                        @endphp
                        <tr>
                            <td>{{ $prod->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $prod->produitFinis->designation ?? '-' }}</strong></td>
                            <td>{{ $prod->quantite }}</td>
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <td>{{ number_format($valeur, 0, ',', ' ') }} FC</td>
                                <td>{{ number_format($coutTotal, 0, ',', ' ') }} FC</td>
                                <td class="fw-bold {{ $benefice >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($benefice, 0, ',', ' ') }} FC
                                </td>
                            @endif
                            <td class="text-center">
                                <button class="btn btn-sm btn-icon btn-label-info" title="{{ __('Détails') }}" wire:click="viewDetails({{ $prod->id }})">
                                    <i class="bx bx-show"></i>
                                </button>
                                @if(Auth::user()->role !== 'geran_depot_usine')
                                    <button class="btn btn-sm btn-icon btn-label-warning" title="{{ __('Modifier') }}" wire:click="editProduction({{ $prod->id }})">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-label-danger" title="{{ __('Supprimer') }}" 
                                        wire:click="deleteProduction({{ $prod->id }})" 
                                        wire:confirm="{{ __('Êtes-vous sûr de vouloir supprimer cette production ? Les stocks seront ajustés automatiquement.') }}">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ __('Aucune production enregistrée.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($productions->count() > 0 && Auth::user()->role !== 'geran_depot_usine')
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3">{{ __('TOTAUX') }}</th>
                        <th>{{ number_format($totValeur, 0, ',', ' ') }} FC</th>
                        <th>{{ number_format($totCout, 0, ',', ' ') }} FC</th>
                        <th class="{{ ($totValeur - $totCout) >= 0 ? 'text-primary' : 'text-danger' }}">
                            {{ number_format($totValeur - $totCout, 0, ',', ' ') }} FC
                        </th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $productions->links() }}
        </div>
    </div>

    <!-- Modal for New Production -->
    <div wire:ignore.self class="modal fade" id="productionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ $isEditMode ? __('Modifier Production') : __('Nouvelle Production') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            {{-- Section Produits Finis (Checkbox Style) --}}
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted small fw-bold mb-2">{{ __('1. Produits Finis Obtenus') }}</h6>
                            </div>
                            <div class="col-12">
                                <div class="input-group input-group-sm mb-2" style="max-width:300px;">
                                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                                    <input type="text" class="form-control" placeholder="{{ __('Rechercher un produit fini...') }}"
                                        wire:model.live.debounce.300ms="searchPf">
                                </div>
                                <div class="card border shadow-none mb-3">
                                    <div class="card-body p-0">
                                        <div class="table-responsive" style="max-height: 250px;">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th class="ps-3" style="width: 40px;">#</th>
                                                        <th>{{ __('Produit Fini') }}</th>
                                                        <th>{{ __('Prix unitaire') }}</th>
                                                        <th class="text-center" style="width: 150px;">{{ __('Quantité Produite') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($produitsPfs as $pf)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <input type="checkbox" class="form-check-input" 
                                                                    wire:model.live="checkedPfs.{{ $pf->id }}">
                                                            </td>
                                                            <td>
                                                                <span class="small fw-bold">{{ $pf->designation }}</span>
                                                            </td>
                                                            <td>
                                                                <span class="small text-primary fw-semibold">{{ number_format($pf->prix, 0, ',', ' ') }} FC</span>
                                                            </td>
                                                            <td class="text-center">
                                                                @if(isset($checkedPfs[$pf->id]) && $checkedPfs[$pf->id])
                                                                    <input type="number" step="0.01" 
                                                                        class="form-control form-control-sm @error('pfQuantities.'.$pf->id) is-invalid @enderror" 
                                                                        wire:model.live="pfQuantities.{{ $pf->id }}">
                                                                @else
                                                                    <span class="text-muted small">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @error('checkedPfs') <div class="text-danger small ms-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Section Ingredients (Checkbox Style) --}}
                            <div class="col-12 mt-4">
                                <h6 class="text-uppercase text-muted small fw-bold mb-2">{{ __('2. Ingrédients / Matières Premières utilisées') }}</h6>
                            </div>
                            
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="input-group input-group-sm" style="max-width:300px;">
                                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                                        <input type="text" class="form-control" placeholder="{{ __('Rechercher une matière première...') }}"
                                            wire:model.live.debounce.300ms="searchIngredient">
                                    </div>
                                    <button type="button" class="btn btn-sm btn-label-danger" wire:click="deselectAllIngredients">
                                        <i class="bx bx-trash-alt me-1"></i> {{ __('Désélectionner tout') }}
                                    </button>
                                </div>
                                <div class="card border shadow-none">
                                    <div class="card-body p-0">
                                        <div class="table-responsive" style="max-height: 300px;">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light sticky-top">
                                                    <tr>
                                                        <th class="ps-3" style="width: 40px;">#</th>
                                                        <th>{{ __('Ingredient') }}</th>
                                                        <th class="text-center">{{ __('Stock Disp.') }}</th>
                                                        <th class="text-center">{{ __('Prix Unit.') }}</th>
                                                        <th class="text-center">{{ __('Qte par Défaut') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($matieresPremieres as $mp)
                                                        <tr>
                                                            <td class="ps-3">
                                                                <input type="checkbox" class="form-check-input" 
                                                                    wire:model.live="checkedIngredients.{{ $mp->id }}">
                                                            </td>
                                                            <td>
                                                                <span class="small fw-bold">{{ $mp->stockMaison->designation }}</span>
                                                                @if($mp->stockMaison->auto_production)
                                                                    <span class="badge bg-success ms-1" style="font-size:0.65rem;" title="{{ __('Sélectionnée automatiquement') }}">Auto</span>
                                                                @endif
                                                                <br>
                                                                <span class="text-muted extra-small">{{ $mp->stockMaison->unite }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge @if($mp->solde <= 0) bg-label-danger @else bg-label-secondary @endif">
                                                                    {{ $mp->solde }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="text-success fw-semibold small">{{ number_format($mp->stockMaison->prix, 0, ',', ' ') }} FC</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="text-primary fw-bold small">{{ $mp->stockMaison->configuration ?? 0 }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .extra-small { font-size: 0.75rem; }
                            </style>

                            <div class="col-12 mt-2 table-responsive">
                                <table class="table table-sm border table-responsive">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Ingrédient') }}</th>
                                            <th>{{ __('Quantité') }}</th>
                                            <th>{{ __('Coût Est.') }}</th>
                                            <th class="text-center">#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($selectedIngredients as $index => $item)
                                            <tr>
                                                <td>{{ $item['designation'] }}</td>
                                                <td style="width: 220px;">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01" class="form-control" 
                                                            style="min-width: 80px;"
                                                            wire:model.live="selectedIngredients.{{ $index }}.quantite">
                                                        <span class="input-group-text">{{ $item['unite'] }}</span>
                                                    </div>
                                                    @error("selectedIngredients.$index.quantite") <div class="text-danger extra-small">{{ $message }}</div> @enderror
                                                </td>
                                                <td>{{ number_format(($item['prix'] ?? 0) * (is_numeric($item['quantite'] ?? 0) ? $item['quantite'] : 0), 0, ',', ' ') }} FC</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon text-danger" wire:click="removeIngredient({{ $index }})">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted small py-3">{{ __('Aucun ingrédient ajouté.') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(!empty($selectedIngredients) && Auth::user()->role !== 'geran_depot_usine')
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2">{{ __('TOTAL MATIÈRES PREMIÈRES') }}</th>
                                                <th colspan="2">{{ number_format(collect($selectedIngredients)->sum(function($i){ return ($i['prix'] ?? 0) * (is_numeric($i['quantite'] ?? 0) ? $i['quantite'] : 0); }), 0, ',', ' ') }} FC</th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                                @error('checkedIngredients') <div class="text-danger small ms-1 mb-2">{{ $message }}</div> @enderror
                            </div>

                            {{-- Section Frais Annexes --}}
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <div class="col-12 mt-4">
                                    <h6 class="text-uppercase text-muted small fw-bold mb-2">{{ __('3. Autres Frais de Production') }}</h6>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Main d\'œuvre / Personnel (FC)') }}</label>
                                    <input type="number" step="any" class="form-control" wire:model.live="charge_personnel">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Autres charges (Bois, Eau, Elec...) (FC)') }}</label>
                                    <input type="number" step="any" class="form-control" wire:model.live="autres_charges">
                                </div>
                            @endif

                            {{-- Section Récapitulatif --}}
                            <div class="col-12 mt-4">
                                <div class="card bg-label-secondary border-0 shadow-none">
                                    <div class="card-body">
                                        <h6 class="text-uppercase fw-bold mb-3"><i class="bx bx-list-check me-2"></i>{{ __('4. Récapitulatif de Production') }}</h6>
                                        <div class="row g-3">
                                            @php
                                                $totalPF = 0;
                                                foreach($checkedPfs as $pfId => $checked) {
                                                    if($checked) {
                                                        $pf = $produitsPfs->find($pfId);
                                                        if($pf) {
                                                            $totalPF += ($pf->prix ?? 0) * (is_numeric($pfQuantities[$pfId] ?? 0) ? $pfQuantities[$pfId] : 0);
                                                        }
                                                    }
                                                }
                                                $totalMP = collect($selectedIngredients)->sum(function($i){ 
                                                    return ($i['prix'] ?? 0) * (is_numeric($i['quantite'] ?? 0) ? $i['quantite'] : 0); 
                                                });
                                                $totalCharges = (float)(is_numeric($charge_personnel) ? $charge_personnel : 0) + (float)(is_numeric($autres_charges) ? $autres_charges : 0);
                                                $coutTotal = $totalMP + $totalCharges;
                                                $profit = $totalPF - $coutTotal;
                                            @endphp
                                            
                                            <div class="col-sm-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted small">{{ __('Valeur Produits') }}</span>
                                                    <span class="fw-bold text-primary">{{ number_format($totalPF, 0, ',', ' ') }} FC</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted small">{{ __('Coût Matières') }}</span>
                                                    <span class="fw-bold text-danger">{{ number_format($totalMP, 0, ',', ' ') }} FC</span>
                                                </div>
                                            </div>
                                            @if(Auth::user()->role !== 'geran_depot_usine')
                                            <div class="col-sm-6 col-md-3">
                                                <div class="d-flex flex-column">
                                                    <span class="text-muted small">{{ __('Charges Annexes') }}</span>
                                                    <span class="fw-bold text-warning">{{ number_format($totalCharges, 0, ',', ' ') }} FC</span>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-md-3">
                                                <div class="d-flex flex-column px-2 py-1 bg-white rounded border border-light">
                                                    <span class="text-muted small">{{ __('Profit Estimé') }}</span>
                                                    <span class="fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                        {{ number_format($profit, 0, ',', ' ') }} FC
                                                    </span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-column flex-sm-row gap-2">
                        <button type="button" class="btn btn-label-secondary w-100 w-sm-auto" data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary w-100 w-sm-auto" wire:loading.attr="disabled">
                            <i class="bx bx-save me-1"></i>
                            {{ $isEditMode ? __('Mettre à jour') : __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Details --}}
    <div wire:ignore.self class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Détails de la Production') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($details)
                        <div class="mb-4">
                            <h6 class="mb-1 text-primary">{{ $details->produitFinis->designation ?? '-' }}</h6>
                            <p class="mb-0 small text-muted">{{ __('Quantité produite :') }} {{ $details->quantite }}</p>
                            <p class="mb-0 small text-muted">{{ __('Date :') }} {{ $details->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <h6>{{ __('Composition (MP)') }}</h6>
                        <ul class="list-group list-group-flush mb-4">
                            @foreach($details->compositions as $comp)
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <span>{{ $comp->designation }}</span>
                                    <span class="badge bg-label-secondary">{{ $comp->quantite }} {{ $comp->unite }}</span>
                                </li>
                            @endforeach
                        </ul>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <div class="bg-light p-3 rounded">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small text-muted">{{ __('Main d\'œuvre :') }}</span>
                                    <span class="small fw-bold">{{ number_format($details->charge_personnel, 0, ',', ' ') }} FC</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="small text-muted">{{ __('Autres charges :') }}</span>
                                    <span class="small fw-bold">{{ number_format($details->autres_charges, 0, ',', ' ') }} FC</span>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer px-0 justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                </div>
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
