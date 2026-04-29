<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Matières Premières (Usine)') }}
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
            <h5 class="card-title mb-0">{{ __('Matières premières à l\'Usine (Production)') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <button class="btn btn-warning text-nowrap" wire:click="openTransferModal">
                    <i class="bx bx-transfer me-1"></i> {{ __('Transférer du Dépôt') }}
                </button>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Matière Première') }}</th>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <th>{{ __('Prix Unitaire') }}</th>
                        @endif
                        <th>{{ __('Solde Usine') }}</th>
                        @if(Auth::user()->role !== 'geran_depot_usine')
                            <th>{{ __('Valeur') }}</th>
                        @endif
                        <th>{{ __('Config/Sac 25kg') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($matiresPremieres->currentPage() - 1) * $matiresPremieres->perPage() + 1; @endphp
                    @forelse($matiresPremieres as $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td><strong>{{ $item->stockMaison->designation }}</strong></td>
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <td>{{ number_format($item->stockMaison->prix, 0, ',', ' ') }} FC</td>
                            @endif
                            <td>
                                <span class="badge bg-label-{{ $item->solde <= 5 ? 'warning' : 'primary' }}">
                                    {{ $item->solde }} {{ $item->stockMaison->unite }}
                                </span>
                            </td>
                            @if(Auth::user()->role !== 'geran_depot_usine')
                                <td>{{ number_format($item->stockMaison->prix * $item->solde, 0, ',', ' ') }} FC</td>
                            @endif
                            <td>{{ $item->stockMaison->configuration }}{{ $item->stockMaison->unite }}</td>
                            <td>
                                @if(Auth::user()->role === 'admin')
                                    <button class="btn btn-sm btn-icon btn-label-primary" wire:click="openAdjustmentModal({{ $item->id }})" title="{{ __('Ajuster le Stock') }}">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ __('Aucune matière première trouvée en usine.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($matiresPremieres->total() > 0 && Auth::user()->role !== 'geran_depot_usine')
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="fw-bold">{{ __('Valeur Totale du Stock en Usine') }}</td>
                            <td colspan="3" class="fw-bold text-warning">{{ number_format($tot, 0, ',', ' ') }} FC</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $matiresPremieres->links() }}
        </div>
    </div>

    <!-- Modal for Transfer -->
    <div wire:ignore.self class="modal fade" id="usineModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header shadow-sm mt-0">
                    <h5 class="modal-title">{{ __('Transférer du Dépôt vers l\'Usine') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeTransfer">
                    <div class="modal-body">
                        <div class="row g-3 text-start">
                            <div class="col-12">
                                <label class="form-label">{{ __('Choisir la Matière Première') }}</label>
                                <select class="form-select @error('matiere_premiere_id') is-invalid @enderror"
                                    wire:model="matiere_premiere_id">
                                    <option value="">-- {{ __('Sélectionner') }} --</option>
                                    @foreach($allMatires as $m)
                                        <option value="{{ $m->id }}">{{ $m->designation }} ({{ __('Solde Dépôt') }}:
                                            {{ $m->solde }} {{ $m->unite }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('matiere_premiere_id') <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Quantité à transférer') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01"
                                        class="form-control @error('quantite') is-invalid @enderror"
                                        wire:model="quantite" placeholder="0.00">
                                    <span
                                        class="input-group-text">{{ $matiere_premiere_id ? $allMatires->find($matiere_premiere_id)->unite : '...' }}</span>
                                </div>
                                @error('quantite') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-warning shadow">
                            <i class="bx bx-transfer me-1"></i> {{ __('Confirmer le Transfert') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Adjustment -->
    <div wire:ignore.self class="modal fade" id="adjustmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header shadow-sm mt-0">
                    <h5 class="modal-title">{{ __('Ajuster le Stock Usine') }}</h5>
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
                                        {{ $selectedUsineId ? \App\Models\StockUsine::find($selectedUsineId)->stockMaison->unite : '...' }}
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