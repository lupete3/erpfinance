<div>
    {{-- Recherche et bouton Ajouter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control"
                   placeholder="Rechercher un tenant..."
                   wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary"
                wire:click="create"
                data-bs-toggle="modal"
                data-bs-target="#tenantModal">
            <i class="bx bx-plus me-1"></i> Ajouter
        </button>
    </div>

    {{-- Tableau --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Téléphone</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($tenants as $index => $tenant)
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td><strong>{{ $tenant->name }}</strong></td>
                    <td>{{ $tenant->email }}</td>
                    <td>{{ $tenant->contact_name }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>
                        <span class="badge bg-{{ $tenant->is_active ? 'success' : 'danger' }}">
                            {{ $tenant->is_active ? 'Actif' : 'Suspendu' }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button"
                                    class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="edit({{ $tenant->id }})"
                                   data-bs-toggle="modal"
                                   data-bs-target="#tenantModal">
                                    <i class="bx bx-edit-alt me-1"></i> Modifier
                                </a>
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="confirmDelete({{ $tenant->id }})">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Aucun tenant trouvé.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $tenants->links() }}
    </div>

    {{-- Modal Tenant --}}
    <div class="modal fade" id="tenantModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? 'Modifier le Tenant' : 'Créer un Tenant' }}
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
                            <label class="form-label">Email</label>
                            <input type="email" wire:model="email"
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <input type="text" wire:model="contact_name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="text" wire:model="phone" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea wire:model="address" class="form-control"></textarea>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" wire:model.defer="is_active" class="form-check-input" id="is_active">
                            <label class="form-check-label" for="is_active">Actif</label>
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

