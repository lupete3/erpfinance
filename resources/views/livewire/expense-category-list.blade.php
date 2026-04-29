<div>
    {{-- Recherche et bouton Ajouter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" {{-- Clé : rechercher_categories --}}
                placeholder="{{ __('expense_category.rechercher_categories') }}" wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#expenseCategoryModal">
            <i class="bx bx-plus me-1"></i>
            {{-- Clé : ajouter --}}
            {{ __('expense_category.ajouter') }}
        </button>
    </div>

    {{-- Tableau --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('expense_category.id') }}</th>
                    <th>{{ __('expense_category.nom') }}</th>
                    <th>{{ __('expense_category.description') }}</th>
                    <th>{{ __('expense_category.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($categories as $index => $category)
                    <tr wire:key="{{ $category->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $category->name }}</strong></td>
                        <td>{{ Str::limit($category->description, 50) }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $category->id }})"
                                        data-bs-toggle="modal" data-bs-target="#expenseCategoryModal">
                                        <i class="bx bx-edit-alt me-1"></i>
                                        {{-- Clé : modifier --}}
                                        {{ __('expense_category.modifier') }}
                                    </a>
                                    <a class="dropdown-item" href="#"
                                        wire:click="confirmDelete({{ $category->id }})">
                                        <i class="bx bx-trash me-1"></i>
                                        {{-- Clé : supprimer --}}
                                        {{ __('expense_category.supprimer') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {{-- Clé : aucune_categorie --}}
                            {{ __('expense_category.aucune_categorie') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $categories->links() }}
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="expenseCategoryModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{-- Clés : modifier_categorie / creer_categorie --}}
                        {{ $isEditMode ? __('expense_category.modifier_categorie') : __('expense_category.creer_categorie') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('expense_category.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- Clé : nom --}}
                            <label for="name" class="form-label">{{ __('expense_category.nom') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" wire:model="name" {{-- Clé : entrer_nom_categorie --}}
                                placeholder="{{ __('expense_category.entrer_nom_categorie') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            {{-- Clé : description --}}
                            <label for="description"
                                class="form-label">{{ __('expense_category.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" wire:model="description"
                                rows="3" {{-- Clé : entrer_description --}} placeholder="{{ __('expense_category.entrer_description') }}"></textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{-- Clé : fermer --}}
                            {{ __('expense_category.fermer') }}
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{-- Clés : enregistrer_modifications / creer --}}
                            {{ $isEditMode ? __('expense_category.enregistrer_modifications') : __('expense_category.creer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
