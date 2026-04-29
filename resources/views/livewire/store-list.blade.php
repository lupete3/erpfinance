<div>
    {{-- Recherche et bouton Ajouter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text"
                   class="form-control"
                   placeholder="{{ __('store.rechercher_point_vente') }}"
                   wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary"
                wire:click="create"
                data-bs-toggle="modal"
                data-bs-target="#storeModal">
            <i class="bx bx-plus me-1"></i> {{ __('store.ajouter') }}
        </button>
    </div>

    {{-- Tableau des points de vente --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('store.id') }}</th>
                    <th>{{ __('store.nom') }}</th>
                    <th>{{ __('store.localisation') }}</th>
                    <th>{{ __('store.telephone') }}</th>
                    <th>{{ __('store.utilisateurs_affectes') }}</th>
                    <th>{{ __('store.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($stores as $index => $store)
                    <tr wire:key="store-{{ $store->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $store->name }}</strong></td>
                        <td>{{ $store->location }}</td>
                        <td>{{ $store->phone }}</td>
                        <td>
                            @foreach($store->users as $user)
                                <span class="badge bg-label-primary me-1">
                                     {{ $user->name }} - {{ $user->role->name }}
                                </span>
                            @endforeach
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
                                       href="{{ route('stores.listproducts', $store) }}">
                                        <i class="bx bx-show me-1"></i> {{ __('store.afficher_articles') }}
                                    </a>
                                    <a class="dropdown-item"
                                       href="#"
                                       wire:click="edit({{ $store->id }})"
                                       data-bs-toggle="modal"
                                       data-bs-target="#storeModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{ __('store.modifier') }}
                                    </a>
                                    <a class="dropdown-item"
                                       href="#"
                                       wire:click="confirmDelete({{ $store->id }})">
                                        <i class="bx bx-trash me-1"></i> {{ __('store.supprimer') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('store.aucun_point_vente') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $stores->links() }}
    </div>

    {{-- Modal de création / édition --}}
    <div class="modal fade" id="storeModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $isEditMode ? __('store.modifier_point_vente') : __('store.creer_point_vente') }}
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('store.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">{{ __('store.nom') }}</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       wire:model="name"
                                       placeholder="{{ __('store.entrer_nom_point_vente') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">{{ __('store.localisation') }}</label>
                                <input type="text" class="form-control" id="location" wire:model="location">
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">{{ __('store.telephone') }}</label>
                                <input type="text" class="form-control" id="phone" wire:model="phone">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">{{ __('store.email') }}</label>
                                <input type="email" class="form-control" id="email" wire:model="email">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">{{ __('store.affecter_utilisateurs_roles') }}</label>
                                <div class="border p-2 rounded">
                                    @foreach($allUsers as $user)
                                        <div class="d-flex align-items-center mb-2">
                                            <input type="checkbox" value="{{ $user->id }}" wire:model="selectedUsers" class="form-check-input me-2">
                                            <span class="me-2">{{ $user->name }} ({{ $user->email }})</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('store.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{ $isEditMode ? __('store.enregistrer_modifications') : __('store.creer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
