<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Matières Premières (Dépôt)') }}
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
            <h5 class="card-title mb-0">{{ __('Liste des matières premières') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <a href="{{ route('bakery.stock.mouvements') }}" class="btn btn-outline-info text-nowrap">
                    <i class="bx bx-history me-1"></i> {{ __('Historique') }}
                </a>

                @if(count($selectedMaisons) > 0)
                    <button class="btn btn-warning text-nowrap" wire:click="openMassTransferModal">
                        <i class="bx bx-send me-1"></i> {{ __('Transférer') }} ({{ count($selectedMaisons) }})
                    </button>
                @endif

                <button class="btn btn-primary text-nowrap" wire:click="create">
                    <i class="bx bx-plus me-1"></i> {{ __('Ajouter') }}
                </button>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th style="width: 40px;"></th>
                        <th>#</th>
                        <th>{{ __('Désignation') }}</th>
                        <th>{{ __('Unité') }}</th>
                        @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                            <th>{{ __('Prix') }}</th>
                        @endif
                        <th>{{ __('Solde Dépôt') }}</th>
                        @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                            <th>{{ __('Valeur') }}</th>
                        @endif
                        <th class="text-center">{{ __('Auto Production') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($matiresPremieres->currentPage() - 1) * $matiresPremieres->perPage() + 1; @endphp
                    @forelse($matiresPremieres as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" value="{{ $item->id }}"
                                    wire:model.live="selectedMaisons">
                            </td>
                            <td>{{ $i++ }}</td>
                            <td><strong>{{ $item->designation }}</strong></td>
                            <td>{{ $item->unite }}</td>
                            @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                                <td>{{ number_format($item->prix, 0, ',', ' ') }} FC</td>
                            @endif
                            <td>
                                <span class="badge bg-label-{{ $item->solde <= 5 ? 'danger' : 'success' }}">
                                    {{ $item->solde }} {{ $item->unite }}
                                </span>
                            </td>
                            @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                                <td>{{ number_format($item->prix * $item->solde, 0, ',', ' ') }} FC</td>
                            @endif
                            <td class="text-center">
                                @if($item->auto_production)
                                    <span class="badge bg-label-success"><i
                                            class="bx bx-check-circle me-1"></i>{{ __('Oui') }}</span>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('Non') }}</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-sm btn-outline-warning me-1"
                                    wire:click="openTransferModal({{ $item->id }})">
                                    <i class="bx bx-send me-1"></i> {{ __('Transférer Production') }}
                                </button>
                                <div class="btn-group">
                                    @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
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
    <div wire:ignore.self class="modal fade" id="adjustmentModalMaison" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header shadow-sm mt-0">
                    <h5 class="modal-title">{{ __('Ajuster le Stock Dépôt') }}</h5>
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
                                    <span class="input-group-text">
                                        {{ $editingMaisonId ? \App\Models\StockMaison::find($editingMaisonId)->unite : '...' }}
                                    </span>
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
                                    @else
                                        <button type="button" class="btn btn-sm btn-icon disabled text-muted">
                                            <i class="bx bx-lock-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ __('Aucune matière première trouvée.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($matiresPremieres->total() > 0 && !in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin']))
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="6" class="fw-bold">{{ __('Valeur Totale') }}</td>
                            <td colspan="2" class="fw-bold text-success">{{ number_format($tot, 0, ',', ' ') }} FC</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $matiresPremieres->links() }}
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div wire:ignore.self class="modal fade" id="maisonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? __('Modifier la matière première') : __('Nouvelle matière première') }}
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
                                <label class="form-label">{{ __('Unité de mesure') }}</label>
                                <input type="text" class="form-control @error('unite') is-invalid @enderror"
                                    wire:model="unite" placeholder="ex: Kg, Sac, Litre">
                                @error('unite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Prix d\'achat') }}</label>
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
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Configuration (Conditionnement)') }}</label>
                                <input type="number" step="any"
                                    class="form-control @error('configuration') is-invalid @enderror"
                                    wire:model="configuration" placeholder="ex: 50 pour sac de 50kg">
                                @error('configuration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <div class="d-flex align-items-center gap-2 border rounded p-3 bg-light">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            id="autoProductionSwitch" wire:model="auto_production">
                                        <label class="form-check-label fw-semibold" for="autoProductionSwitch">
                                            {{ __('Cocher automatiquement en Production') }}
                                        </label>
                                    </div>
                                </div>
                                <small
                                    class="text-muted ms-1">{{ __('Si activé, cette matière sera pré-sélectionnée lors de chaque nouvelle production.') }}</small>
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

    <!-- Modal for One Transfer -->
    <div wire:ignore.self class="modal fade" id="transferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Transférer vers l\'Usine (Production)') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeTransfer">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="alert alert-info py-2 small mb-0">
                                    <strong>{{ __('Matière') }} :</strong> {{ $maisonDetails?->designation }}<br>
                                    <strong>{{ __('Stock Dépôt') }} :</strong> {{ $maisonDetails?->solde }}
                                    {{ $maisonDetails?->unite }}
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Quantité à transférer') }}</label>
                                <input type="number" step="0.01"
                                    class="form-control form-control-lg @error('transferQuantity') is-invalid @enderror"
                                    wire:model="transferQuantity">
                                @error('transferQuantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-warning">
                            {{ __('Confirmer le transfert') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Mass Transfer -->
    <div wire:ignore.self class="modal fade" id="massTransferModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Transfert en Masse vers l\'Usine (Production)') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeMassTransfer">
                    <div class="modal-body">
                        <p class="text-muted small mb-3">
                            {{ __('Saisissez les quantités à transférer du dépôt vers la production pour chaque matière sélectionnée.') }}
                        </p>
                        <div class="table-responsive border rounded p-2">
                            <table class="table table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Matière Première') }}</th>
                                        <th>{{ __('Disponible (Dépôt)') }}</th>
                                        <th style="width: 180px;">{{ __('Qté à transférer') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedMaisons as $id)
                                        @php $m = \App\Models\StockMaison::find($id); @endphp
                                        @if($m)
                                            <tr>
                                                <td class="align-middle fw-bold">{{ $m->designation }}</td>
                                                <td class="align-middle text-muted">{{ $m->solde }} {{ $m->unite }}</td>
                                                <td>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" step="0.01"
                                                            class="form-control @error('massQtys.' . $id) is-invalid @enderror"
                                                            wire:model="massQtys.{{ $id }}">
                                                        <span class="input-group-text">{{ $m->unite }}</span>
                                                    </div>
                                                    @error('massQtys.' . $id) <div class="invalid-feedback small d-block">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-warning shadow">
                            <i class="bx bx-send me-1"></i> {{ __('Confirmer le transfert groupé') }}
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