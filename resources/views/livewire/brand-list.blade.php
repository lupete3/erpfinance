<div>
    {{-- Recherche et bouton Ajouter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text"
                   class="form-control"
                   {{-- Utilisation de la clé 'brand.rechercher_marques' --}}
                   placeholder="{{ __('brand.rechercher_marques') }}"
                   wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary"
                wire:click="create"
                data-bs-toggle="modal"
                data-bs-target="#brandModal">
            <i class="bx bx-plus me-1"></i> {{-- Utilisation de la clé 'brand.ajouter' --}}
            {{ __('brand.ajouter') }}
        </button>
    </div>

    {{-- Tableau des marques --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Utilisation des clés pour l'en-tête du tableau --}}
                    <th>{{ __('brand.id') }}</th>
                    <th>{{ __('brand.nom') }}</th>
                    <th>{{ __('brand.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($brands as $index => $brand)
                    <tr wire:key="{{ $brand->id }}">
                        <td>{{ $index+1 }}</td>
                        <td><strong>{{ $brand->name }}</strong></td>
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
                                       wire:click="edit({{ $brand->id }})"
                                       data-bs-toggle="modal"
                                       data-bs-target="#brandModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{-- Utilisation de la clé 'brand.modifier' --}}
                                        {{ __('brand.modifier') }}
                                    </a>
                                    <a class="dropdown-item"
                                       href="#"
                                       wire:click="confirmDelete({{ $brand->id }})">
                                        <i class="bx bx-trash me-1"></i> {{-- Utilisation de la clé 'brand.supprimer' --}}
                                        {{ __('brand.supprimer') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            {{-- Utilisation de la clé 'brand.aucune_marque' --}}
                            {{ __('brand.aucune_marque') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $brands->links() }}
    </div>

    {{-- Modal de marque --}}
    <div class="modal fade" id="brandModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{-- Utilisation des clés conditionnelles pour le titre du modal --}}
                        {{ $isEditMode ? __('brand.modifier_marque') : __('brand.creer_marque') }}
                    </h5>
                    {{-- Utilisation de la clé 'brand.fermer' pour l'attribut aria-label --}}
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="{{ __('brand.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- Utilisation de la clé 'brand.nom' --}}
                            <label for="name" class="form-label">{{ __('brand.nom') }}</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   wire:model="name"
                                   {{-- Utilisation de la clé 'brand.nom_marque' comme placeholder --}}
                                   placeholder="{{ __('brand.nom_marque') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Utilisation de la clé 'brand.fermer' --}}
                        <button type="button"
                                class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">{{ __('brand.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{-- Utilisation des clés conditionnelles pour le bouton d'action --}}
                            {{ $isEditMode ? __('brand.enregistrer_modifications') : __('brand.creer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
