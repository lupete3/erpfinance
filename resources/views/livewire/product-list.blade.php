<div>
    {{-- Search and Add button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher_produits --}}
            <input type="text" class="form-control" placeholder="{{ __('product.rechercher_produits') }}" wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="bx bx-plus me-1"></i> {{-- Clé : ajouter --}}
            {{ __('product.ajouter') }}
        </button>
    </div>

    {{-- Products Table --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- Clés pour l'en-tête du tableau --}}
                    <th>{{ __('product.ref') }}</th>
                    <th>{{ __('product.nom') }}</th>
                    <th>{{ __('product.categorie') }}</th>
                    <th>{{ __('product.prix_vente') }} ({{ company()?->devise }})</th>
                    <th>{{ __('product.stock') }}</th>
                    <th>{{ __('product.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($products as $product)
                    <tr wire:key="{{ $product->id }}">
                        <td>{{ $product->reference }}</td>
                        <td><strong>{{ $product->name }}</strong></td>
                        {{-- Clé : na --}}
                        <td>{{ $product->category->name ?? __('product.na') }}</td>
                        <td>{{ $product->sale_price }} {{ company()?->devise }}</td>
                        <td>
                            @foreach($product->stores as $store)
                                <span class="badge bg-label-{{ $store->pivot->quantity <= $product->min_stock ? 'danger' : 'primary' }}">
                                    {{ $store->name }} : {{ $store->pivot->quantity }} {{ $product->unit->abbreviation ?? '' }}
                                </span><br>
                            @endforeach
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $product->id }})" data-bs-toggle="modal" data-bs-target="#productModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{-- Clé : modifier --}}
                                        {{ __('product.modifier') }}
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="confirmDelete({{ $product->id }})">
                                        <i class="bx bx-trash me-1"></i> {{-- Clé : supprimer --}}
                                        {{ __('product.supprimer') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            {{-- Clé : aucun_produit --}}
                            {{ __('product.aucun_produit') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $products->links() }}
    </div>

    {{-- Product Modal --}}
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clés conditionnelles pour le titre du modal --}}
                    <h5 class="modal-title">{{ $isEditMode ? __('product.modifier_produit') : __('product.creer_produit') }}</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('product.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                      {{-- Stock par magasin --}}
                      <div class="row">
                          @foreach($stores as $store)
                              <div class="col-md-4 mb-3">
                                  {{-- Utilisation de $store->name (ne nécessite pas de traduction) --}}
                                  <label class="form-label">{{ $store->name }} ({{ __('product.stock') }})</label>
                                  <input type="number" class="form-control"
                                          wire:model="storeQuantities.{{ $store->id }}"
                                          placeholder="0" value="0">
                              </div>
                          @endforeach
                      </div>

                      {{-- Nom et Référence --}}
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              {{-- Clé : nom_produit --}}
                              <label for="name" class="form-label">{{ __('product.nom_produit') }}</label>
                              {{-- Clé : entrez_nom_produit --}}
                              <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="{{ __('product.entrez_nom_produit') }}">
                              @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                          <div class="col-md-6 mb-3">
                              {{-- Clé : reference --}}
                              <label for="reference" class="form-label">{{ __('product.reference') }}</label>
                              {{-- Clé : entrez_reference --}}
                              <input type="text" class="form-control @error('reference') is-invalid @enderror" wire:model="reference" placeholder="{{ __('product.entrez_reference') }}">
                              @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                      </div>

                      {{-- Catégorie, Marque et Unité --}}
                      <div class="row">
                          <div class="col-md-4 mb-3">
                              {{-- Clé : categorie_label --}}
                              <label for="category_id" class="form-label">{{ __('product.categorie_label') }}</label>
                              <select class="form-select @error('category_id') is-invalid @enderror" wire:model="category_id">
                                  {{-- Clé : selectionner_categorie --}}
                                  <option value="">{{ __('product.selectionner_categorie') }}</option>
                                  @foreach($categories as $category)
                                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                                  @endforeach
                              </select>
                              @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                          <div class="col-md-4 mb-3">
                              {{-- Clé : marque --}}
                              <label for="brand_id" class="form-label">{{ __('product.marque') }}</label>
                              <select class="form-select @error('brand_id') is-invalid @enderror" wire:model="brand_id">
                                  {{-- Clé : selectionner_marque --}}
                                  <option value="">{{ __('product.selectionner_marque') }}</option>
                                  @foreach($brands as $brand)
                                      <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                  @endforeach
                              </select>
                              @error('brand_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                          <div class="col-md-4 mb-3">
                              {{-- Clé : unite --}}
                              <label for="unit_id" class="form-label">{{ __('product.unite') }}</label>
                              <select class="form-select @error('unit_id') is-invalid @enderror" wire:model="unit_id">
                                  {{-- Clé : selectionner_unite --}}
                                  <option value="">{{ __('product.selectionner_unite') }}</option>
                                  @foreach($units as $unit)
                                      <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                  @endforeach
                              </select>
                              @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                      </div>

                      {{-- Prix d'achat et de vente --}}
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              {{-- Clé : prix_achat --}}
                              <label for="purchase_price" class="form-label">{{ __('product.prix_achat') }}</label>
                              <input type="number" step="0.01" class="form-control @error('purchase_price') is-invalid @enderror" wire:model="purchase_price" placeholder="0.00">
                              @error('purchase_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                          <div class="col-md-6 mb-3">
                              {{-- Clé : prix_vente_label --}}
                              <label for="sale_price" class="form-label">{{ __('product.prix_vente_label') }}</label>
                              <input type="number" step="0.01" class="form-control @error('sale_price') is-invalid @enderror" wire:model="sale_price" placeholder="0.00">
                              @error('sale_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                      </div>

                      {{-- Stock minimum --}}
                      <div class="row">
                          <div class="col-md-12 mb-3">
                              {{-- Clé : stock_minimum --}}
                              <label for="min_stock" class="form-label">{{ __('product.stock_minimum') }}</label>
                              <input type="number" class="form-control @error('min_stock') is-invalid @enderror" wire:model="min_stock" placeholder="0">
                              @error('min_stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                          </div>
                      </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('product.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                            {{-- Clés conditionnelles pour le bouton d'action --}}
                          {{ $isEditMode ? __('product.enregistrer_modifications') : __('product.creer') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
