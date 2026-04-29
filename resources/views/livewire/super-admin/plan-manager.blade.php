<div>
    {{-- Recherche + bouton --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control"
                   placeholder="Rechercher un plan..."
                   wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary"
                wire:click="create"
                data-bs-toggle="modal"
                data-bs-target="#planModal">
            <i class="bx bx-plus me-1"></i> Nouveau plan
        </button>
    </div>

    {{-- Tableau --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prix ($)</th>
                <th>Durée (jours)</th>
                <th>Max. Utilisateurs</th>
                <th>Max. Magasins</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($plans as $plan)
                <tr>
                    <td><strong>{{ $plan->name }}</strong></td>
                    <td>{{ number_format($plan->price, 2) }}</td>
                    <td>{{ $plan->duration_days }}</td>
                    <td>{{ $plan->max_users ?? 'Illimité' }}</td>
                    <td>{{ $plan->max_stores ?? 'Illimité' }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="edit({{ $plan->id }})"
                                   data-bs-toggle="modal"
                                   data-bs-target="#planModal">
                                    <i class="bx bx-edit-alt me-1"></i> Modifier
                                </a>
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="confirmDelete({{ $plan->id }})">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucun plan trouvé</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $plans->links() }}
    </div>

    {{-- Modal Plan --}}
    <div class="modal fade" id="planModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? 'Modifier le plan' : 'Créer un plan' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" wire:model="name"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Prix ($)</label>
                            <input type="number" wire:model="price"
                                   class="form-control @error('price') is-invalid @enderror">
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Durée (jours)</label>
                            <input type="number" wire:model="duration_days"
                                   class="form-control @error('duration_days') is-invalid @enderror">
                            @error('duration_days') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max. Utilisateurs</label>
                            <input type="number" wire:model="max_users"
                                   class="form-control @error('max_users') is-invalid @enderror"
                                   placeholder="laisser vide = illimité">
                            @error('max_users') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Max. Magasins</label>
                            <input type="number" wire:model="max_stores"
                                   class="form-control @error('max_stores') is-invalid @enderror"
                                   placeholder="laisser vide = illimité">
                            @error('max_stores') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                          <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                          {{ $isEditMode ? 'Enregistrer' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
