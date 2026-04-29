<div>
    {{-- Barre de recherche et bouton d'ajout --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher_fournisseurs --}}
            <input type="text" class="form-control" placeholder="{{ __('supplier.rechercher_fournisseurs') }}"
                wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#supplierModal">
            <i class="bx bx-plus me-1"></i> {{-- Clé : ajouter --}}
            {{ __('supplier.ajouter') }}
        </button>
    </div>

    {{-- Tableau des fournisseurs --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Clés pour l'en-tête du tableau --}}
                    <th>{{ __('supplier.id') }}</th>
                    <th>{{ __('supplier.nom') }}</th>
                    <th>{{ __('supplier.email') }}</th>
                    <th>{{ __('supplier.telephone') }}</th>
                    <th>{{ __('supplier.dette') }}</th>
                    <th>{{ __('supplier.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($suppliers as $index => $supplier)
                    <tr wire:key="{{ $supplier->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td>{{ $supplier->email }}</td>
                        <td>{{ $supplier->phone }}</td>
                        <td>{{ number_format($supplier->debt, 2) }} {{ company()?->devise }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $supplier->id }})"
                                        data-bs-toggle="modal" data-bs-target="#supplierModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{-- Clé : editer --}}
                                        {{ __('supplier.editer') }}
                                    </a>
                                    @if (Auth::user()->role_id == 1)
                                        <a class="dropdown-item" href="#" wire:click="confirmDelete({{ $supplier->id }})">
                                            <i class="bx bx-trash me-1"></i> {{-- Clé : supprimer --}}
                                            {{ __('supplier.supprimer') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            {{-- Clé : aucun_fournisseur --}}
                            {{ __('supplier.aucun_fournisseur') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>

    {{-- Modal Création/Édition Fournisseur --}}
    <div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clés conditionnelles pour le titre du modal --}}
                    <h5 class="modal-title">
                        {{ $isEditMode ? __('supplier.editer_fournisseur') : __('supplier.creer_fournisseur') }}</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('supplier.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                {{-- Clé : nom --}}
                                <label class="form-label">{{ __('supplier.nom') }}</label>
                                {{-- Clé : nom_fournisseur --}}
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="{{ __('supplier.nom_fournisseur') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- Clé : email --}}
                                <label class="form-label">{{ __('supplier.email') }}</label>
                                {{-- Clé : email_fournisseur --}}
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="{{ __('supplier.email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                {{-- Clé : telephone --}}
                                <label class="form-label">{{ __('supplier.telephone') }}</label>
                                {{-- Clé : telephone_fournisseur --}}
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone" placeholder="{{ __('supplier.telephone_fournisseur') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- Clé : dette_initiale --}}
                                <label class="form-label">{{ __('supplier.dette_initiale') }}</label>
                                <input type="number" step="any" class="form-control @error('debt') is-invalid @enderror"
                                    wire:model="debt" placeholder="0.00" readonly>
                                @error('debt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            {{-- Clé : adresse --}}
                            <label class="form-label">{{ __('supplier.adresse') }}</label>
                            {{-- Clé : adresse_fournisseur --}}
                            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address"
                                rows="3" placeholder="{{ __('supplier.adresse_fournisseur') }}"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('supplier.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{-- Clés conditionnelles pour le bouton d'action --}}
                            {{ $isEditMode ? __('supplier.enregistrer_modifications') : __('supplier.creer') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>