<div class="" style="min-height: 90vh;">

    <a href="{{ route('dashboard') }}" class="dashboard-btn" title="{{ __('menu.tableau_de_bord') }}">
        <i class="bx bx-home"></i>
    </a>

    <div class="row g-2">

        <!-- 🧩 Zone Produits -->
        <section class="col-12 col-lg-8 d-flex flex-column ">

            <!-- 🔍 Barre de recherche et filtres -->
            <header
                class="pos-header d-flex flex-wrap align-items-center justify-content-between mb-3 p-3 shadow-lg rounded-3 bg-white">

                <!-- Titre produits -->
                <h4 class="fw-bold mb-2 mb-md-0 text-primary d-flex align-items-center">
                    <i class="bx bx-store fs-3 me-2"></i> {{ __('pos.produits') }}
                </h4>

                <!-- Barre de recherche -->
                <div class="flex-grow-1 mx-0 mx-md-3 my-2 my-md-0">
                    <div class="search-input-group">
                        <i class="bx bx-search text-muted me-2"></i>
                        <input type="search" class="search-input" placeholder="{{ __('pos.rechercher') }}..."
                            wire:model.live="search" autofocus>
                    </div>
                </div>

                <!-- Filtres catégories et marques -->
                <div class="d-flex flex-wrap gap-2">
                    <select class="form-select form-select-lg rounded-pill px-3 shadow-lg"
                        wire:model.live="selectedCategory">
                        <option value="">{{ __('pos.toutes_categories') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-lg rounded-pill px-3 shadow-lg"
                        wire:model.live="selectedBrand">
                        <option value="">{{ __('pos.toutes_marques') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
            </header>

            <!-- 🧱 Grille produits -->
            <div class="pos-grid flex-grow-1 overflow-none">
                @if (!empty($products))
                    <div class="row g-3 pb-5">
                        @forelse($products as $product)
                            <div class="col-6 col-md-3">
                                <div class="card border-0 shadow-lg h-100 product-item"
                                    wire:click.prevent="addItem({{ $product->id }})"
                                    style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="card-body text-center p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="fw-bold text-truncate mb-1">{{ $product->name }}</h6>
                                            <small class="text-muted d-block">
                                                {{ __('pos.stock') }}:
                                                {{ $product->stores()->where('store_id', Auth::user()->stores()->first()->id)->first()->pivot->quantity ?? 0 }}
                                            </small>
                                        </div>
                                        <button class="btn btn-outline-primary fw-bold rounded-pill mt-2">
                                            {{ number_format($product->sale_price, 2) }} {{ company()?->devise }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">{{ __('pos.aucun_produit') }}</div>
                        @endforelse
                    </div>

                    <div class="mt-auto">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </section>

        <!-- 🧾 Panier -->
        <section class="col-12 col-lg-4 d-flex flex-column bg-white shadow rounded-3 p-3">

            <!-- En-tête -->
            <header class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-secondary mb-0 d-flex align-items-center">
                    <i class="bx bx-cart fs-3 me-2"></i> {{ __('pos.panier_facture') }}
                </h4>
                <button class="btn btn-lg btn-outline-danger rounded-pill px-3" wire:click="clearCart"
                    @if (!$cart) disabled @endif>
                    <i class="bx bx-trash me-1"></i> {{ __('pos.supprimer') }}
                </button>
            </header>

            <!-- Liste des articles -->
            <div class="flex-grow-1 overflow-auto" style="max-height: 50vh;">
                @if (!$cart)
                    <div class="text-center text-muted py-5">{{ __('pos.panier_vide') }}</div>
                @else
                    @foreach ($cart as $index => $item)
                        <div wire:key="{{ $index }}" class="cart-item">
                            <div class="item-info">
                                <div class="item-name">{{ $item['name'] }}</div>
                                <div class="item-price">{{ number_format($item['price'], 2) }}
                                    {{ company()?->devise }}</div>
                            </div>
                            <div class="item-controls">
                                <input type="number" class="quantity-input" value="{{ $item['quantity'] }}"
                                    min="1"
                                    wire:change="updateQuantity({{ $index }}, $event.target.value)">
                                <div class="item-subtotal">{{ number_format($item['subtotal'], 2) }}
                                    {{ company()?->devise }}</div>
                                <button class="remove-btn" wire:click="removeItem({{ $index }})">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Totaux -->
            <div class="mt-3 border-top pt-3">
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('pos.sous_total') }}</span>
                    <strong>{{ number_format($subtotal, 2) }} {{ company()?->devise }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ __('pos.reduction') }}</span>
                    <input type="number" min="0" step="0.01"
                        class="form-control form-control-lg text-end rounded-pill w-50" wire:model.live="discount"
                        placeholder="0.00" />
                </div>
                <div class="d-flex justify-content-between align-items-center border-top pt-2 mb-3">
                    <span class="fw-bold fs-5">{{ __('pos.total') }}</span>
                    <span class="fw-bold fs-4 text-success">{{ number_format($total, 2) }}
                        {{ company()?->devise }}</span>
                </div>

                <!-- Client + Paiement -->
                <div class="row g-2">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">{{ __('pos.client') }}</label>
                        <select class="form-select form-select-lg rounded-pill px-3 shadow-lg" wire:model="client_id">
                            <option value="">{{ __('pos.selectionner_client') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">{{ __('pos.montant_paye') }}</label>
                        <input type="number" min="0" step="0.01" class="form-control rounded-pill"
                            wire:model="total_paid" placeholder="0.00" />
                    </div>
                </div>

                <!-- Boutons actions -->
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button class="btn btn-lg btn-success flex-fill  py-2" wire:click="saveSale"
                        wire:loading.attr="disabled" @if (!$cart) disabled @endif>
                        <span wire:loading class="spinner-border spinner-border-lg me-2"></span>
                        <i class="bx bx-check-circle me-2"></i>{{ __('pos.valider_vente') }}
                    </button>
                    <button class="btn btn-lg btn-outline-secondary flex-fill rounded-pill py-2" wire:click="clearCart"
                        @if (!$cart) disabled @endif>
                        <i class="bx bx-refresh me-2"></i>{{ __('pos.annuler') }}
                    </button>
                </div>
            </div>
        </section>

    </div>
</div>
