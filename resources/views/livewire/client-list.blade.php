<div>
    {{-- Search and Add button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher_clients --}}
            <input type="text" class="form-control" placeholder="{{ __('client.rechercher_clients') }}"
                wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#clientModal">
            <i class="bx bx-plus me-1"></i> {{-- Clé : ajouter --}}
            {{ __('client.ajouter') }}
        </button>
    </div>

    {{-- Clients Table --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Clés pour l'en-tête du tableau --}}
                    <th>{{ __('client.id') }}</th>
                    <th>{{ __('client.nom') }}</th>
                    <th>{{ __('client.email') }}</th>
                    <th>{{ __('client.telephone') }}</th>
                    <th>{{ __('client.dette') }}</th>
                    <th>{{ __('client.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($clients as $index => $client)
                    <tr wire:key="{{ $client->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $client->name }}</strong></td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->phone }}</td>
                        <td>{{ $client->debt }} {{ company()?->devise }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $client->id }})"
                                        data-bs-toggle="modal" data-bs-target="#clientModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{-- Clé : modifier --}}
                                        {{ __('client.modifier') }}
                                    </a>
                                    @if (Auth::user()->role_id == 1)
                                        <a class="dropdown-item" href="#" wire:click="confirmDelete({{ $client->id }})">
                                            <i class="bx bx-trash me-1"></i> {{-- Clé : supprimer --}}
                                            {{ __('client.supprimer') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            {{-- Clé : aucun_client --}}
                            {{ __('client.aucun_client') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $clients->links() }}
    </div>

    {{-- Client Modal --}}
    <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clés conditionnelles pour le titre du modal --}}
                    <h5 class="modal-title">{{ $isEditMode ? __('client.modifier_client') : __('client.creer_client') }}
                    </h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('client.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                {{-- Clé : nom_client --}}
                                <label for="name" class="form-label">{{ __('client.nom_client') }}</label>
                                {{-- Clé : entrez_nom --}}
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="{{ __('client.entrez_nom') }}">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- Clé : email_client --}}
                                <label for="email" class="form-label">{{ __('client.email_client') }}</label>
                                {{-- Clé : entrez_email --}}
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="{{ __('client.entrez_email') }}">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                {{-- Clé : telephone_client --}}
                                <label for="phone" class="form-label">{{ __('client.telephone_client') }}</label>
                                {{-- Clé : entrez_telephone --}}
                                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                    wire:model="phone" placeholder="{{ __('client.entrez_telephone') }}">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                {{-- Clé : dette_initiale --}}
                                <label for="debt" class="form-label">{{ __('client.dette_initiale') }}</label>
                                <input type="number" step="any" class="form-control @error('debt') is-invalid @enderror"
                                    wire:model="debt" placeholder="0.00" readonly>
                                @error('debt') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            {{-- Clé : adresse_client --}}
                            <label for="address" class="form-label">{{ __('client.adresse_client') }}</label>
                            {{-- Clé : entrez_adresse --}}
                            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address"
                                rows="3" placeholder="{{ __('client.entrez_adresse') }}"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('client.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{-- Clés conditionnelles pour le bouton d'action --}}
                            {{ $isEditMode ? __('client.enregistrer_modifications') : __('client.creer') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>