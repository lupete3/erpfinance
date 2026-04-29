<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Historique des Mouvements (Dépôt -> Production)') }}
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
                <div class="col-md-3">
                    <label class="form-label">{{ __('Date début') }}</label>
                    <input type="date" class="form-control" wire:model.live="dateDebut">
                </div>
                <div class="col-md-3">
                    <label class="form-label">{{ __('Date fin') }}</label>
                    <input type="date" class="form-control" wire:model.live="dateFin">
                </div>
                <div class="col-md-4">
                    <label class="form-label">{{ __('Matière Première') }}</label>
                    <select class="form-select" wire:model.live="selectedMatiereId">
                        <option value="">{{ __('Toutes les matières...') }}</option>
                        @foreach($matieres as $matiere)
                            <option value="{{ $matiere->id }}">{{ $matiere->designation }}</option>
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
            <h5 class="card-title mb-0">{{ __('Journal des transferts') }}</h5>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-striped border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Date & Heure') }}</th>
                        <th>{{ __('Produit') }}</th>
                        <th class="text-center">{{ __('Quantité Transférée') }}</th>
                        <th class="text-end">{{ __('Solde Dépôt (Après)') }}</th>
                        <th class="text-end">{{ __('Solde Usine (Après)') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($mouvements->currentPage() - 1) * $mouvements->perPage() + 1; @endphp
                    @forelse($mouvements as $mvt)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $mvt->stockMaison?->designation ?? 'N/A' }}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-label-primary px-3">
                                    {{ $mvt->quantite }} {{ $mvt->stockMaison?->unite ?? '' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">{{ $mvt->reste_maison }} {{ $mvt->stockMaison?->unite ?? '' }}</td>
                            <td class="text-end text-success fw-bold">{{ $mvt->reste_usine }}
                                {{ $mvt->stockMaison?->unite ?? '' }}</td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <button class="dropdown-item" wire:click="edit({{ $mvt->id }})">
                                            <i class="bx bx-edit-alt me-1"></i> {{ __('Modifier') }}
                                        </button>
                                        <button class="dropdown-item text-danger" wire:click="delete({{ $mvt->id }})"
                                            onclick="confirm('Êtes-vous sûr de vouloir annuler ce transfert ? Les stocks seront ajustés.') || event.stopImmediatePropagation()">
                                            <i class="bx bx-trash me-1"></i> {{ __('Annuler / Supprimer') }}
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ __('Aucun mouvement trouvé pour cette sélection.') }}
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

    <!-- Modal for Edit Movement -->
    <div wire:ignore.self class="modal fade" id="editMvtModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Modifier le Transfert') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        @if($mvtDetails)
                            <div class="alert alert-info py-2 small mb-4">
                                <strong>{{ __('Produit') }} :</strong> {{ $mvtDetails->stockMaison?->designation }}<br>
                                <strong>{{ __('Date initial') }} :</strong>
                                {{ $mvtDetails->created_at->format('d/m/Y H:i') }}<br>
                                <strong>{{ __('Quantité actuelle') }} :</strong> {{ $mvtDetails->quantite }}
                                {{ $mvtDetails->stockMaison?->unite }}
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">{{ __('Nouvelle quantité à transférer') }}</label>
                            <input type="number" step="0.01"
                                class="form-control form-control-lg fw-bold @error('newQuantite') is-invalid @enderror"
                                wire:model="newQuantite">
                            @error('newQuantite') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text text-warning small mt-2">
                                <i class="bx bx-info-circle me-1"></i>
                                {{ __('Les stocks (Dépôt et Usine) seront automatiquement ajustés en fonction de la différence.') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-check me-1"></i> {{ __('Enregistrer les modifications') }}
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