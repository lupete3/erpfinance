<div>
    {{-- Recherche + bouton --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control"
                   placeholder="Rechercher par tenant..."
                   wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary"
                wire:click="create"
                data-bs-toggle="modal"
                data-bs-target="#subscriptionModal">
            <i class="bx bx-plus me-1"></i> Nouvelle souscription
        </button>
    </div>

    {{-- Tableau --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Tenant</th>
                <th>Plan</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($subscriptions as $index => $sub)
                <tr>
                    <td>{{ $index+1 }}</td>
                    <td>{{ $sub->tenant->name }}</td>
                    <td>{{ $sub->plan->name }}</td>
                    <td>{{ $sub->start_date }}</td>
                    <td>{{ $sub->end_date }}</td>
                    <td>
                        <span class="badge bg-{{ $sub->is_active && $sub->end_date >= now() ? 'success' : 'danger' }}">
                            {{ $sub->is_active && $sub->end_date >= now() ? 'Active' : 'Expirée' }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="edit({{ $sub->id }})"
                                   data-bs-toggle="modal"
                                   data-bs-target="#subscriptionModal">
                                    <i class="bx bx-edit-alt me-1"></i> Modifier
                                </a>
                                <a class="dropdown-item"
                                   href="#"
                                   wire:click="confirmDelete({{ $sub->id }})">
                                    <i class="bx bx-trash me-1"></i> Supprimer
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Aucune souscription trouvée.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $subscriptions->links() }}
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? 'Modifier la souscription' : 'Nouvelle souscription' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tenant</label>
                            <select wire:model="tenant_id" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                                @endforeach
                            </select>
                            @error('tenant_id') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Plan</label>
                            <select wire:model="plan_id" class="form-control">
                                <option value="">-- Sélectionner --</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            @error('plan_id') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Montant</label>
                            <input type="number" wire:model="amount" class="form-control">
                            @error('amount') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" wire:model="start_date" class="form-control">
                            @error('start_date') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" wire:model="end_date" class="form-control">
                            @error('end_date') <span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" wire:model="is_active" class="form-check-input" id="is_active">
                            <label class="form-check-label" for="is_active">Active</label>
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