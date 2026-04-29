<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Achats Matières Premières') }}
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
            <h5 class="card-title mb-0">{{ __('Historique des Achats') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <button class="btn btn-primary text-nowrap" wire:click="create">
                    <i class="bx bx-plus me-1"></i> {{ __('Nouvel Achat') }}
                </button>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Fournisseur') }}</th>
                        <th>{{ __('Matière Première') }}</th>
                        <th>{{ __('Quantité') }}</th>
                        @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                            <th>{{ __('P.U') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Payé') }}</th>
                            <th>{{ __('Dette') }}</th>
                        @endif
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($achats as $item)
                        @php $total = $item->prix_achat * $item->quantite; @endphp
                        <tr>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                            <td><strong>{{ $item->fournisseur->nom ?? '-' }}</strong></td>
                            <td>{{ $item->stockMaison->designation ?? '-' }}</td>
                            <td>{{ $item->quantite }} {{ $item->stockMaison->unite ?? '' }}</td>
                            @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                                <td>{{ number_format($item->prix_achat, 0, ',', ' ') }}</td>
                                <td>{{ number_format($total, 0, ',', ' ') }}</td>
                                <td class="text-success">{{ number_format($item->montant_paye, 0, ',', ' ') }}</td>
                                <td class="text-danger">
                                    @if($total > $item->montant_paye)
                                        {{ number_format($total - $item->montant_paye, 0, ',', ' ') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td class="text-center">
                                @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" wire:click="edit({{ $item->id }})">
                                                <i class="bx bx-edit-alt me-1"></i> {{ __('Modifier') }}
                                            </button>
                                            <button class="dropdown-item text-danger" wire:click="delete({{ $item->id }})"
                                                onclick="confirm('Confirmer la suppression ? Cela ajustera également le stock.') || event.stopImmediatePropagation()">
                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('Consultation seule') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                {{ __('Aucun achat trouvé.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $achats->links() }}
        </div>
    </div>

    <style>
        @media (max-width: 767.98px) {
            .responsive-table thead { display: none; }
            .responsive-table tr { 
                display: block; 
                margin-bottom: 1.5rem; 
                border: 1px solid #e1e4e8; 
                border-radius: 0.75rem; 
                padding: 1rem; 
                background-color: #f8f9fa;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }
            .responsive-table td { 
                display: block; 
                text-align: right; 
                padding: 0.5rem 0; 
                border: none;
            }
            .responsive-table td:before { 
                content: attr(data-label); 
                float: left; 
                font-weight: 700; 
                color: #495057;
                margin-top: 0.4rem;
            }
            .responsive-table td:first-child { 
                text-align: center; 
                background: #fff; 
                margin: -1rem -1rem 1rem -1rem; 
                padding: 1rem; 
                border-bottom: 1px solid #e1e4e8 !important;
                border-radius: 0.75rem 0.75rem 0 0;
            }
            .responsive-table td:first-child:before { content: none; }
            .responsive-table .form-control { width: 100% !important; text-align: left; }
        }
    </style>

    <!-- Modal for Create/Edit -->
    <div wire:ignore.self class="modal fade" id="achatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">
                        <i class="bx {{ $isEditMode ? 'bx-edit' : 'bx-plus-circle' }} me-2 text-primary fs-4"></i>
                        {{ $isEditMode ? __('Modifier l\'achat') : __('Nouvel Achat Groupé de Matières Premières') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('1. Choisir le Fournisseur') }}</label>
                                <select class="form-select @error('fournisseur_id') is-invalid @enderror"
                                    wire:model="fournisseur_id">
                                    <option value="">-- {{ __('Sélectionner Fournisseur') }} --</option>
                                    @foreach($fournisseurs as $f)
                                        <option value="{{ $f->id }}">{{ $f->nom }}</option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-3">

                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('2. Sélectionner les Matières Premières & Saisir les Détails') }}</label>
                                <div class="row g-3">
                                    {{-- Left: List of MPs to check --}}
                                    <div class="col-md-4 border-end">
                                        <div class="p-2 bg-light rounded mb-2 small fw-bold text-center">{{ __('Boutique / Dépôt') }}</div>
                                        <div style="max-height: 400px; overflow-y: auto;" class="pe-2">
                                            @foreach($stockMaisons as $mp)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" value="{{ $mp->id }}"
                                                        id="mp-{{ $mp->id }}" wire:model.live="checkedMps"
                                                        @if($isEditMode) disabled @endif>
                                                    <label class="form-check-label small" for="mp-{{ $mp->id }}">
                                                        {{ $mp->designation }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Right: Entry Table --}}
                                    <div class="col-md-8">
                                        @if(empty($checkedMps))
                                            <div class="text-center py-5 text-muted">
                                                <i class="bx bx-check-square fs-1 d-block mb-2"></i>
                                                {{ __('Cochez des articles à gauche pour commencer') }}
                                            </div>
                                        @else
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle no-footer shadow-none responsive-table">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>{{ __('Article') }}</th>
                                                            <th style="width: 150px;">{{ __('Qté') }}</th>
                                                            <th style="width: 200px;">{{ __('P.U (FC)') }}</th>
                                                            <th style="width: 200px;">{{ __('Payé (FC)') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($mpData as $id => $data)
                                                            <tr wire:key="entry-{{ $id }}">
                                                                <td>
                                                                    <div class="fw-bold small">{{ $data['designation'] }}</div>
                                                                    <div class="text-muted extra-small">{{ $data['unite'] }}</div>
                                                                </td>
                                                                <td data-label="{{ __('Qté') }}">
                                                                    <input type="number" step="any" class="form-control @error('mpData.'.$id.'.quantite') is-invalid @enderror"
                                                                        wire:model.live="mpData.{{ $id }}.quantite">
                                                                    @error('mpData.'.$id.'.quantite') <div class="invalid-feedback extra-small text-start">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td data-label="{{ __('P.U (FC)') }}">
                                                                    <input type="number" step="any" class="form-control @error('mpData.'.$id.'.prix') is-invalid @enderror"
                                                                        wire:model.live="mpData.{{ $id }}.prix">
                                                                    @error('mpData.'.$id.'.prix') <div class="invalid-feedback extra-small text-start">{{ $message }}</div> @enderror
                                                                </td>
                                                                <td data-label="{{ __('Payé (FC)') }}">
                                                                    <input type="number" step="any" class="form-control border-primary @error('mpData.'.$id.'.montant_paye') is-invalid @enderror"
                                                                        wire:model="mpData.{{ $id }}.montant_paye">
                                                                    @error('mpData.'.$id.'.montant_paye') <div class="invalid-feedback extra-small text-start">{{ $message }}</div> @enderror
                                                                </td>
                                                            </tr>
                                                            <tr wire:key="sub-{{ $id }}" class="border-bottom">
                                                                <td colspan="4" class="text-end py-1">
                                                                    @php
                                                                        $total = (float)($mpData[$id]['quantite'] ?? 0) * (float)($mpData[$id]['prix'] ?? 0);
                                                                        $paye = (float)($mpData[$id]['montant_paye'] ?? 0);
                                                                        $dette = $total - $paye;
                                                                    @endphp
                                                                    <div class="small">
                                                                        <span class="text-muted">{{ __('Total') }}:</span> <span class="fw-bold">{{ number_format($total, 0, ',', ' ') }} FC</span>
                                                                        @if($dette > 0)
                                                                            | <span class="text-danger small">{{ __('Dette') }}: {{ number_format($dette, 0, ',', ' ') }} FC</span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top bg-light pt-2">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary btn-lg shadow">
                            <i class="bx bx-save me-1"></i>
                            {{ $isEditMode ? __('Mettre à jour l\'achat') : __('Enregistrer les achats') }}
                        </button>
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