<div class="row">
    {{-- Colonne Gauche: Liste des Produits et Panier --}}
    <div class="col-md-7">
        <div class="card mb-3">
            <div class="card-header">
                {{-- Clé : produits --}}
                <h5 class="mb-0">{{ __('purchase.produits') }}</h5>
            </div>
            <div class="card-body">
                {{-- Barre de recherche --}}
                <div class="mb-3">
                    {{-- Clé : rechercher_produits --}}
                    <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                           placeholder="{{ __('purchase.rechercher_produits') }}">
                </div>

                {{-- Liste des produits pour l'ajout au panier --}}
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('purchase.produit') }}</th>
                                <th>{{ __('purchase.ref') }}</th>
                                <th>{{ __('purchase.stock') }}</th>
                                <th>{{ __('purchase.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                                <tr wire:key="prod-{{ $product->id }}">
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->reference }}</td>
                                    <td>
                                        {{ $product->stores->sum('pivot.quantity') ?? 0 }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" wire:click="addToCart({{ $product->id }})" title="Ajouter">
                                            <i class="bx bx-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">{{ __('purchase.aucun_produit') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Panier d'achat --}}
        <div class="card">
            <div class="card-header">
                {{-- Clé : details_achat --}}
                <h5 class="mb-0">{{ __('purchase.details_achat') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('purchase.produit') }}</th>
                                <th>{{ __('purchase.quantite') }}</th>
                                <th>{{ __('purchase.prix') }}</th>
                                <th>{{ __('purchase.sous_total') }}</th>
                                <th>{{ __('purchase.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($cart as $index => $item)
                                <tr wire:key="{{ $index }}">
                                    <td><strong>{{ $item['name'] }}</strong></td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" value="{{ $item['quantity'] }}" wire:change="updateQuantity({{ $index }}, $event.target.value)" style="min-width: 80px;">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm" value="{{ $item['price'] }}" wire:change="updatePrice({{ $index }}, $event.target.value)" style="min-width: 100px;">
                                    </td>
                                    <td>{{ number_format($item['subtotal'], 2) }}</td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" wire:click="removeItem({{ $index }})">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">{{ __('purchase.panier_vide') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Colonne Droite: Formulaire de Finalisation --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                {{-- Clé : details_achat --}}
                <h5 class="mb-0">{{ __('purchase.details_achat') }}</h5>
            </div>
            <form wire:submit.prevent="savePurchase">
                <div class="card-body">
                    {{-- Magasin --}}
                    <div class="mb-3">
                        {{-- Clé : magasin --}}
                        <label class="form-label">{{ __('purchase.magasin') }}</label>
                        <select class="form-select @error('store_id') is-invalid @enderror" wire:model.live="store_id" {{ Auth::user()->role_id != 1 ? 'disabled' : '' }}>
                            {{-- Clé : selectionner_magasin --}}
                            <option value="">{{ __('purchase.selectionner_magasin') }}</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Fournisseur --}}
                    <div class="mb-3">
                        {{-- Clé : fournisseur --}}
                        <label class="form-label">{{ __('purchase.fournisseur') }}</label>
                        <select class="form-select @error('supplier_id') is-invalid @enderror" wire:model="supplier_id">
                            {{-- Clé : selectionner_fournisseur --}}
                            <option value="">{{ __('purchase.selectionner_fournisseur') }}</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Total à payer --}}
                    <div class="mb-3">
                        <p class="h4">
                            <strong>{{ __('purchase.total') }}:</strong>
                            {{ number_format($total, 2) }} {{ company()?->devise }}
                        </p>
                        <hr>
                    </div>

                    {{-- Montant Payé --}}
                    <div class="mb-3">
                        {{-- Clé : montant_paye --}}
                        <label class="form-label">{{ __('purchase.montant_paye') }}</label>
                        <input type="number" step="0.01" class="form-control @error('total_paid') is-invalid @enderror" wire:model="total_paid" min="0" placeholder="0.00">
                        @error('total_paid') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="card-footer d-grid gap-2">
                    {{-- Clé : annuler --}}
                    <button type="button" class="btn btn-outline-secondary" wire:click="cancelPurchase">
                        <i class="bx bx-x me-1"></i> {{ __('purchase.annuler') }}
                    </button>
                    {{-- Clé : finaliser_achat --}}
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled" wire:target="savePurchase">
                        <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="bx bx-check me-1"></i> {{ __('purchase.finaliser_achat') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

