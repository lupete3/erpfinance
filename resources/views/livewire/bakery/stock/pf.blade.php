<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Produits Finis (Fournil)') }}
    </h4>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div
            class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="card-title mb-0">{{ __('Produits en fin de production') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>

                <a href="{{ route('bakery.stock.mouvements-pf') }}" class="btn btn-outline-info text-nowrap">
                    <i class="bx bx-history me-1"></i> {{ __('Historique PF') }}
                </a>

                @if(count($selectedPfs) > 0)
                    <button class="btn btn-warning text-nowrap" wire:click="openMassShipModal">
                        <i class="bx bx-send me-1"></i> {{ __('Expédier') }} ({{ count($selectedPfs) }})
                    </button>
                @endif

                @if(Auth::user()->role !== 'geran_depot_usine')
                    <button class="btn btn-primary text-nowrap" wire:click="create">
                        <i class="bx bx-plus me-1"></i> {{ __('Produit') }}
                    </button>
                @endif
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>#</th>
                        <th>{{ __('Désignation') }}</th>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <th>{{ __('Prix de Vente') }}</th>
                        @endif
                        <th>{{ __('Solde Fournil') }}</th>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <th>{{ __('Valeur') }}</th>
                        @endif
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($produits->currentPage() - 1) * $produits->perPage() + 1; @endphp
                    @forelse($produits as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" value="{{ $item->id }}"
                                    wire:model.live="selectedPfs">
                            </td>
                            <td>{{ $i++ }}</td>
                            <td><strong>{{ $item->designation }}</strong></td>
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <td>{{ number_format($item->prix, 0, ',', ' ') }} FC</td>
                            @endif
                            <td>
                                <span class="badge bg-label-{{ $item->solde <= 10 ? 'danger' : 'success' }}">
                                    {{ $item->solde }} {{ __('pcs') }}
                                </span>
                            </td>
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <td>{{ number_format($item->prix * $item->solde, 0, ',', ' ') }} FC</td>
                            @endif
                            <td class="text-center text-nowrap">
                                <button class="btn btn-sm btn-outline-warning me-1"
                                    wire:click="openShipModal({{ $item->id }})">
                                    <i class="bx bx-send me-1"></i> {{ __('Expédier') }}
                                </button>
                                @if(Auth::user()->role !== 'geran_depot_usine')
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <button class="dropdown-item" wire:click="edit({{ $item->id }})">
                                                <i class="bx bx-edit-alt me-1"></i> {{ __('Modifier') }}
                                            </button>
                                            @if(Auth::user()->role === 'admin')
                                                <button class="dropdown-item" wire:click="openAdjustmentModal({{ $item->id }})">
                                                    <i class="bx bx-cog me-1"></i> {{ __('Ajuster le Stock') }}
                                                </button>
                                            @endif
                                            <button class="dropdown-item text-danger" wire:click="delete({{ $item->id }})"
                                                onclick="confirm('Confirmer la suppression ?') || event.stopImmediatePropagation()">
                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                            </button>
                                        </div>
...
    <!-- Modal for Adjustment -->
    <div wire:ignore.self class="modal fade" id="adjustmentModalPf" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header shadow-sm mt-0">
                    <h5 class="modal-title">{{ __('Ajuster le Stock Fournil') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateAdjustment">
                    <div class="modal-body">
                        <div class="row g-3 text-start">
                            <div class="col-12">
                                <label class="form-label">{{ __('Nouveau Solde en Stock') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01"
                                        class="form-control @error('adjustmentQuantity') is-invalid @enderror"
                                        wire:model="adjustmentQuantity" placeholder="0.00">
                                    <span class="input-group-text">{{ __('pcs') }}</span>
                                </div>
                                @error('adjustmentQuantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary shadow">
                            <i class="bx bx-check me-1"></i> {{ __('Confirmer l\'Ajustement') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ __('Aucun produit fini trouvé.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($produits->total() > 0 && Auth::user()->role !== 'geran_depot_usine')
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="5" class="fw-bold">{{ __('Valeur Totale') }}</td>
                            <td colspan="2" class="fw-bold text-success">{{ number_format($tot, 0, ',', ' ') }} FC</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $produits->links() }}
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div wire:ignore.self class="modal fade" id="pfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? __('Modifier le produit') : __('Nouveau produit fini') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Désignation') }}</label>
                                <input type="text" class="form-control @error('designation') is-invalid @enderror"
                                    wire:model="designation">
                                @error('designation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Prix de Vente') }}</label>
                                <div class="input-group">
                                    <input type="number" step="any"
                                        class="form-control @error('prix') is-invalid @enderror" wire:model="prix">
                                    <span class="input-group-text">FC</span>
                                </div>
                                @error('prix') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Solde initial') }}</label>
                                <input type="number" step="any"
                                    class="form-control @error('solde') is-invalid @enderror" wire:model="solde">
                                @error('solde') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit"
                            class="btn btn-primary">{{ $isEditMode ? __('Mettre à jour') : __('Ajouter') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Shipping -->
    <div wire:ignore.self class="modal fade" id="shipModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom shadow-sm">
                    <h5 class="modal-title">{{ __('Expédier vers un Point de Vente') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="shipToSite">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <p class="mb-2 text-muted">{{ __('Produit') }}:
                                    <strong>{{ $editingPfId ? \App\Models\StockPf::find($editingPfId)->designation : '' }}</strong>
                                </p>
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Point de Vente (Site)') }}</label>
                                <select class="form-select @error('site_id') is-invalid @enderror" wire:model="site_id">
                                    <option value="">-- {{ __('Choisir') }} --</option>
                                    @foreach($sites as $s)
                                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                                    @endforeach
                                </select>
                                @error('site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Quantité à expédier') }}</label>
                                <input type="number" step="any"
                                    class="form-control @error('quantite_exp') is-invalid @enderror"
                                    wire:model="quantite_exp" placeholder="0">
                                @error('quantite_exp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-warning shadow">
                            <i class="bx bx-send me-1"></i> {{ __('Confirmer l\'expédition') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Mass Shipping -->
    <div wire:ignore.self class="modal fade" id="massShipModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Expédition en Masse vers Point de Vente') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeMassShip">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label
                                    class="form-label fw-bold">{{ __('1. Choisir le Point de Vente (Site)') }}</label>
                                <select class="form-select @error('massSiteId') is-invalid @enderror"
                                    wire:model="massSiteId">
                                    <option value="">-- {{ __('Choisir') }} --</option>
                                    @foreach($sites as $s)
                                        <option value="{{ $s->id }}">{{ $s->nom }}</option>
                                    @endforeach
                                </select>
                                @error('massSiteId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-3">

                            <div class="col-12">
                                <label
                                    class="form-label fw-bold mb-2">{{ __('2. Saisir les quantités à expédier') }}</label>
                                <div class="table-responsive border border-secondary rounded p-2">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>{{ __('Produit') }}</th>
                                                <th>{{ __('Stock Actuel') }}</th>
                                                <th style="width: 150px;">{{ __('Qté à envoyer') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($selectedPfs as $pfId)
                                                @php $p = \App\Models\StockPf::find($pfId); @endphp
                                                @if($p)
                                                    <tr>
                                                        <td>{{ $p->designation }}</td>
                                                        <td>{{ $p->solde }} pcs</td>
                                                        <td>
                                                            <input type="number" step="any"
                                                                class="form-control form-control-sm @error('massQtys.' . $pfId) is-invalid @enderror"
                                                                wire:model="massQtys.{{ $pfId }}">
                                                            @error('massQtys.' . $pfId) <div class="invalid-feedback small">
                                                                {{ $message }}
                                                            </div> @enderror
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-warning shadow">
                            <i class="bx bx-send me-1"></i> {{ __('Confirmer l\'expédition groupée') }}
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