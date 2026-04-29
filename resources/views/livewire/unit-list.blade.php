<div>
    {{-- Barre de recherche et bouton d'ajout --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Utilisation de la clé 'unit.rechercher_unites' --}}
            <input type="text" class="form-control" placeholder="{{ __('unit.rechercher_unites') }}" wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#unitModal">
            <i class="bx bx-plus me-1"></i> {{-- Utilisation de la clé 'unit.ajouter' --}}
            {{ __('unit.ajouter') }}
        </button>
    </div>

    {{-- Tableau des unités --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Utilisation des clés pour l'en-tête du tableau --}}
                    <th>{{ __('unit.id') }}</th>
                    <th>{{ __('unit.nom') }}</th>
                    <th>{{ __('unit.abreviation') }}</th>
                    <th>{{ __('unit.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($units as $index => $unit)
                    <tr wire:key="{{ $unit->id }}">
                        <td>{{ $index+1 }}</td>
                        <td><strong>{{ $unit->name }}</strong></td>
                        <td>{{ $unit->abbreviation }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $unit->id }})" data-bs-toggle="modal" data-bs-target="#unitModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{-- Utilisation de la clé 'unit.editer' --}}
                                        {{ __('unit.editer') }}
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="confirmDelete({{ $unit->id }})">
                                        <i class="bx bx-trash me-1"></i> {{-- Utilisation de la clé 'unit.supprimer' --}}
                                        {{ __('unit.supprimer') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {{-- Utilisation de la clé 'unit.aucune_unite' --}}
                            {{ __('unit.aucune_unite') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $units->links() }}
    </div>

    {{-- Modal Création/Édition d'unité --}}
    <div class="modal fade" id="unitModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Utilisation des clés conditionnelles pour le titre du modal --}}
                    <h5 class="modal-title">{{ $isEditMode ? __('unit.editer_unite') : __('unit.creer_unite') }}</h5>
                    {{-- Utilisation de la clé 'unit.fermer' pour l'attribut aria-label --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('unit.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- Utilisation de la clé 'unit.nom' --}}
                            <label class="form-label">{{ __('unit.nom') }}</label>
                            {{-- Utilisation de la clé 'unit.nom_unite' comme placeholder --}}
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="{{ __('unit.nom_unite') }}">
                            {{-- Utilisation de la clé 'unit.nom_requis' ou 'unit.nom_unique' (selon le cas) pour le message d'erreur --}}
                            @error('name') <div class="invalid-feedback">{{ __('unit.nom_requis') }}</div> @enderror {{-- J'utilise nom_requis comme exemple --}}
                        </div>
                        <div class="mb-3">
                            {{-- Utilisation de la clé 'unit.abreviation' --}}
                            <label class="form-label">{{ __('unit.abreviation') }}</label>
                            {{-- Utilisation de la clé 'unit.abreviation_unite' comme placeholder --}}
                            <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" wire:model="abbreviation" placeholder="{{ __('unit.abreviation_unite') }}">
                            @error('abbreviation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Utilisation de la clé 'unit.fermer' --}}
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('unit.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                          <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                          {{-- Utilisation des clés conditionnelles pour le bouton d'action --}}
                          {{ $isEditMode ? __('unit.enregistrer_modifications') : __('unit.creer') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
