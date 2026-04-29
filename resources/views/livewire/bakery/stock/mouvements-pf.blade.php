<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Historique des Mouvements PF (Fournil -> Points de Vente)') }}
    </h4>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">{{ __('Date début') }}</label>
                    <input type="date" class="form-control" wire:model.live="dateDebut">
                </div>
                <div class="col-md-2">
                    <label class="form-label">{{ __('Date fin') }}</label>
                    <input type="date" class="form-control" wire:model.live="dateFin">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Produit Fini') }}</label>
                    <select class="form-select" wire:model.live="selectedPfId">
                        <option value="">{{ __('Tous les produits...') }}</option>
                        @foreach($produits as $p)
                            <option value="{{ $p->id }}">{{ $p->designation }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Destination (Site)') }}</label>
                    <select class="form-select" wire:model.live="selectedSiteId">
                        <option value="">{{ __('Tous les sites...') }}</option>
                        @foreach($sites as $s)
                            <option value="{{ $s->id }}">{{ $s->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" wire:click="$refresh">
                        <i class="bx bx-refresh me-1"></i> {{ __('Actualiser') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">{{ __('Journal des expéditions PF') }}</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-striped border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Date & Heure') }}</th>
                        <th>{{ __('Produit') }}</th>
                        <th>{{ __('Destination') }}</th>
                        <th class="text-center">{{ __('Quantité') }}</th>
                        <th class="text-end">{{ __('Fournil (Après)') }}</th>
                        <th class="text-end">{{ __('Site (Après)') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($mouvements->currentPage() - 1) * $mouvements->perPage() + 1; @endphp
                    @forelse($mouvements as $mvt)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $mvt->stockPf?->designation ?? 'N/A' }}</strong></td>
                            <td><span class="text-primary fw-bold">{{ $mvt->site?->nom ?? 'N/A' }}</span></td>
                            <td class="text-center">
                                <span class="badge bg-label-warning px-3">
                                    {{ $mvt->quantite }} pcs
                                </span>
                            </td>
                            <td class="text-end fw-bold">{{ $mvt->reste_stock_pf }} pcs</td>
                            <td class="text-end text-success fw-bold">{{ $mvt->reste_boulangerie }} pcs</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-sm btn-outline-primary me-1" wire:click="edit({{ $mvt->id }})">
                                    <i class="bx bx-edit-alt"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="delete({{ $mvt->id }})"
                                    onclick="confirm('Annuler ce transfert ? Les stocks seront restaurés.') || event.stopImmediatePropagation()">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                {{ __('Aucun mouvement PF trouvé.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $mouvements->links() }}
        </div>
    </div>

    <!-- Modal for Edit PF Movement -->
    <div wire:ignore.self class="modal fade" id="editMvtPfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Modifier le Transfert PF') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        @if($mvtDetails)
                            <div class="alert alert-info py-2 small mb-4 text-start">
                                <strong>{{ __('Produit') }} :</strong> {{ $mvtDetails->produit?->designation }}<br>
                                <strong>{{ __('Destination') }} :</strong> {{ $mvtDetails->site?->nom }}<br>
                                <strong>{{ __('Quantité actuelle') }} :</strong> {{ $mvtDetails->quantite }} pcs
                            </div>
                        @endif
                        <div class="col-12 text-start">
                            <label class="form-label fw-bold">{{ __('Nouvelle quantité expédiée') }}</label>
                            <input type="number" step="1"
                                class="form-control form-control-lg fw-bold @error('newQuantite') is-invalid @enderror"
                                wire:model="newQuantite">
                            @error('newQuantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text text-warning small mt-2">
                                <i class="bx bx-info-circle me-1"></i>
                                {{ __('Les stocks Fournil et Site seront automatiquement ajustés.') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> {{ __('Mettre à jour') }}
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