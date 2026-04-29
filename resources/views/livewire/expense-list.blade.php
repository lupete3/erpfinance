<div>
    {{-- Recherche + Ajouter --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" {{-- Clé : rechercher_depenses --}}
                placeholder="{{ __('expense.rechercher_depenses') }}" wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#expenseModal">
            <i class="bx bx-plus me-1"></i>
            {{-- Clé : ajouter --}}
            {{ __('expense.ajouter') }}
        </button>
    </div>

    {{-- Tableau --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Clé : date --}}
                    <th>{{ __('expense.date') }}</th>
                    {{-- Clé : categorie --}}
                    <th>{{ __('expense.categorie') }}</th>
                    {{-- Clé : magasin --}}
                    <th>{{ __('expense.magasin') }}</th>
                    {{-- Clé : montant --}}
                    <th>{{ __('expense.montant') }}</th>
                    {{-- Clé : description --}}
                    <th>{{ __('expense.description') }}</th>
                    {{-- Clé : statut --}}
                    <th>{{ __('expense.statut') }}</th>
                    {{-- Clé : actions --}}
                    <th>{{ __('expense.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenses as $expense)
                    <tr wire:key="{{ $expense->id }}">
                        <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                        <td>{{ $expense->category->name ?? '-' }}</td>
                        <td>{{ $expense->store->name ?? '-' }}</td>
                        <td>{{ number_format($expense->amount, 2, ',', ' ') }} {{ company()?->devise }}</td>
                        <td>{{ Str::limit($expense->description, 50) }}</td>
                        <td>
                            @switch($expense->status)
                                @case('pending')
                                    <span class="badge bg-label-warning me-1">
                                        {{-- Clé : en_attente --}}
                                        {{ __('expense.en_attente') }}
                                    </span>
                                @break

                                @case('validated')
                                    <span class="badge bg-label-success me-1">
                                        {{-- Clé : valide --}}
                                        {{ __('expense.valide') }}
                                    </span>
                                @break

                                @default
                                    <span class="badge bg-label-danger me-1">
                                        {{-- Clé : annule --}}
                                        {{ __('expense.annule') }}
                                    </span>
                            @endswitch
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @if ($expense->status == 'pending')
                                        @if (Auth::user()->role_id == 1)
                                            <a class="dropdown-item" href="#"
                                                wire:click="confirmValidate({{ $expense->id }}, 'validated')">
                                                <i class="bx bx-check me-1"></i>
                                                {{-- Clé : valider --}}
                                                {{ __('expense.valider') }}
                                            </a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="confirmValidate({{ $expense->id }}, 'cancelled')">
                                                <i class="bx bx-x me-1"></i>
                                                {{-- Clé : rejeter --}}
                                                {{ __('expense.rejeter') }}
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="#" wire:click="edit({{ $expense->id }})"
                                            data-bs-toggle="modal" data-bs-target="#expenseModal">
                                            <i class="bx bx-edit-alt me-1"></i>
                                            {{-- Clé : modifier --}}
                                            {{ __('expense.modifier') }}
                                        </a>
                                        <a class="dropdown-item" href="#"
                                            wire:click="confirmDelete({{ $expense->id }})">
                                            <i class="bx bx-trash me-1"></i>
                                            {{-- Clé : supprimer --}}
                                            {{ __('expense.supprimer') }}
                                        </a>
                                    @else
                                        <a class="dropdown-item text-danger" href="#">
                                            {{-- Clé : aucune_action --}}
                                            {{ __('expense.aucune_action') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                {{-- Clé : aucune_depense --}}
                                {{ __('expense.aucune_depense') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $expenses->links() }}
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{-- Clés : modifier_depense / nouvelle_depense --}}
                            {{ $isEditMode ? __('expense.modifier_depense') : __('expense.nouvelle_depense') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="{{ __('expense.fermer') }}"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    {{-- Clé : champ_categorie --}}
                                    <label for="category_id" class="form-label">{{ __('expense.champ_categorie') }}</label>
                                    <select id="category_id" class="form-control @error('category_id') is-invalid @enderror"
                                        wire:model="category_id">
                                        {{-- Clé : selectionner --}}
                                        <option value="">{{ __('expense.selectionner') }}</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @if (Auth::user()->role_id == 1)
                                    <div class="mb-3 col-md-6">
                                        {{-- Clé : champ_magasin --}}
                                        <label for="store_id" class="form-label">{{ __('expense.champ_magasin') }}</label>
                                        <select id="store_id" class="form-control @error('store_id') is-invalid @enderror"
                                            wire:model="store_id">
                                            {{-- Clé : selectionner --}}
                                            <option value="">{{ __('expense.selectionner') }}</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('store_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    {{-- Clé : champ_montant --}}
                                    <label for="amount" class="form-label">{{ __('expense.champ_montant') }}</label>
                                    <input type="number" step="any"
                                        class="form-control @error('amount') is-invalid @enderror" wire:model="amount"
                                        id="amount">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    {{-- Clé : champ_date --}}
                                    <label for="date" class="form-label">{{ __('expense.champ_date') }}</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        wire:model="date" id="date">
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                {{-- Clé : champ_description --}}
                                <label for="description" class="form-label">{{ __('expense.champ_description') }}</label>
                                <textarea id="description" class="form-control @error('description') is-invalid @enderror" wire:model="description"
                                    rows="3"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                {{-- Clé : fermer --}}
                                {{ __('expense.fermer') }}
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                                <i class="bx bx-check me-1"></i>
                                {{-- Clés : mettre_a_jour / enregistrer --}}
                                {{ $isEditMode ? __('expense.mettre_a_jour') : __('expense.enregistrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
