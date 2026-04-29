<div>
    <div class="row">
        {{-- Catalog Section --}}
        <div class="col-lg-8 col-md-12">
            <div class="card mb-4 mt-2">
                <div class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center bg-label-primary py-2 text-white gap-3">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-3">
                        <h5 class="card-title mb-0 text-primary fw-bold">{{ __('Catalogue Produits') }}</h5>
                        <div class="btn-group btn-group-sm mb-0 ms-sm-2" role="group">
                            <button type="button" class="btn btn-outline-primary @if($posMode == 'catalogue') active @endif" wire:click="switchMode('catalogue')">
                                <i class="bx bx-grid-alt me-1"></i> {{ __('Catalogue') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary @if($posMode == 'inventaire') active @endif" wire:click="switchMode('inventaire')">
                                <i class="bx bx-list-check me-1"></i> {{ __('Inventaire') }}
                            </button>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                        <div class="input-group input-group-merge" style="min-width: 150px;">
                            <span class="input-group-text"><i class="bx bx-store text-primary"></i></span>
                            <select class="form-select border-start-0 ps-0" wire:model.live="selectedSiteId">
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group input-group-merge" style="min-width: 180px;">
                            <span class="input-group-text"><i class="bx bx-search text-primary"></i></span>
                            <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}" wire:model.live.debounce.300ms="searchProduct">
                        </div>
                    </div>
                </div>
                <div class="card-body pt-4">
                    @if($posMode == 'catalogue')
                        <div class="row g-3 overflow-auto" style="max-height: 600px;">
                            @foreach($products as $prod)
                                <div class="col-md-4 col-sm-6">
                                    <div class="card h-100 border shadow-none @if($prod->solde <= 0) bg-light @endif" 
                                        style="cursor: pointer; transition: 0.3s;" 
                                        @if($prod->solde > 0) wire:click="addToCart({{ $prod->id }})" @endif>
                                        <div class="card-body p-3 text-center position-relative">
                                            @if($prod->solde <= 0)
                                                <span class="badge bg-danger position-absolute top-0 start-0 m-2">{{ __('Rupture') }}</span>
                                            @else
                                                <span class="badge bg-label-success position-absolute top-0 end-0 m-2">{{ $prod->solde }}</span>
                                            @endif
                                            <div class="avatar avatar-lg mx-auto mb-2">
                                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-package fs-2"></i></span>
                                            </div>
                                            <h6 class="mb-1 text-truncate">{{ $prod->stockProduitFinis->designation }}</h6>
                                            <p class="mb-0 text-primary fw-bold">{{ number_format($prod->stockProduitFinis->prix, 0, ',', ' ') }} FC</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Mode Inventaire --}}
                        <div class="table-responsive overflow-auto" style="max-height: 600px;">
                            <table class="table table-hover table-sm">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>{{ __('Produit') }}</th>
                                        <th class="text-center">{{ __('En Stock') }}</th>
                                        <th class="text-center">{{ __('Restant Rayon') }}</th>
                                        <th class="text-center text-primary">{{ __('Vendu') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $prod)
                                        <tr>
                                            <td>
                                                <h6 class="mb-0 small fw-bold">{{ $prod->stockProduitFinis->designation }}</h6>
                                                <span class="text-muted small">{{ number_format($prod->stockProduitFinis->prix, 0, ',', ' ') }} FC</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-label-secondary">{{ $prod->solde }}</span>
                                            </td>
                                            <td class="text-center" style="width: 150px;">
                                                <input type="number" step="any"
                                                    class="form-control form-control-sm text-center" 
                                                    placeholder="0"
                                                    wire:model.live.debounce.500ms="remains.{{ $prod->id }}"
                                                    min="0" max="{{ $prod->solde }}">
                                            </td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ $cart[$prod->id]['quantity'] ?? 0 }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Order Section --}}
        <div class="col-lg-4 col-md-12">
            <div class="card mt-2 shadow-sm border-2 border-primary">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-white d-flex align-items-center">
                        <i class="bx bx-cart me-2"></i> {{ __('Facturation') }}
                    </h5>
                    @if(count($cart) > 0)
                        <button class="btn btn-xs btn-danger" wire:click="clearCart" onclick="confirm('Voulez-vous vider tout le panier ?') || event.stopImmediatePropagation()">
                            <i class="bx bx-trash me-1"></i> {{ __('Vider') }}
                        </button>
                    @endif
                </div>
                <div class="card-body py-4">
                    {{-- Cart Items --}}
                    <div class="mb-4 overflow-auto" style="max-height: 250px;">
                        <ul class="list-group list-group-flush border-bottom">
                            @forelse($cart as $id => $item)
                                <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                    <div style="width: 40%;">
                                        <h6 class="mb-0 small fw-bold text-truncate">{{ $item['name'] }}</h6>
                                        <span class="text-muted small">{{ number_format($item['price'], 0, ',', ' ') }} FC</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <button class="btn btn-xs btn-outline-secondary p-1" wire:click="decreaseQty({{ $id }})"><i class="bx bx-minus"></i></button>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm text-center p-0" 
                                               style="width: 50px;" 
                                               value="{{ $item['quantity'] }}" 
                                               wire:change="updateQty({{ $id }}, $event.target.value)">
                                        <button class="btn btn-xs btn-outline-secondary p-1" wire:click="increaseQty({{ $id }})"><i class="bx bx-plus"></i></button>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 text-end" style="width: 100px;">
                                        <span class="fw-bold small w-100">{{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }}</span>
                                        <button class="btn btn-xs btn-text-danger p-0" wire:click="removeFromCart({{ $id }})">
                                            <i class="bx bx-trash fs-5"></i>
                                        </button>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-4 text-muted small">
                                    <i class="bx bx-cart-alt fs-2 d-block mb-2"></i>
                                    {{ __('Panier vide.') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Totals --}}
                    <div class="bg-light p-3 rounded mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="h6 mb-0">{{ __('TOTAL À PAYER') }}</span>
                            <span class="h5 mb-0 text-primary fw-bold">{{ number_format($this->cartTotal, 0, ',', ' ') }} FC</span>
                        </div>
                    </div>

                    {{-- Form --}}
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase">{{ __('sale.client') }}</label>
                            <select class="form-select @error('selectedClientId') is-invalid @enderror" wire:model="selectedClientId">
                                <option value="">-- {{ __('Choisir Client') }} --</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                                @endforeach
                            </select>
                            @error('selectedClientId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold text-uppercase">{{ __('Montant Reçu (Payé)') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">FC</span>
                                <input type="number" step="any" class="form-control @error('montantRecu') is-invalid @enderror" wire:model.live="montantRecu">
                            </div>
                            @error('montantRecu') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-2 rounded @if($this->reste > 0) bg-label-danger @else bg-label-success @endif">
                                <span class="small fw-bold">{{ __('DETTE / RESTE') }}</span>
                                <span class="fw-bold">{{ number_format($this->reste, 0, ',', ' ') }} FC</span>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <button class="btn btn-primary w-100 py-2 d-flex justify-content-center align-items-center" wire:click="store" wire:loading.attr="disabled">
                                <i class="bx bx-check me-2"></i> {{ __('Valider la Vente') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Print Modal --}}
    <div class="modal fade" id="printModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Vente validée !') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-check fs-1"></i></span>
                    </div>
                    <h4>{{ __('Opération réussie') }}</h4>
                    <p class="text-muted">{{ __('La vente a été enregistrée. Choisissez le format d\'impression :') }}</p>
                    
                    <div class="d-grid gap-3 mt-4">
                        <button type="button" class="btn btn-primary btn-lg" id="btn_print_pos">
                            <i class="bx bx-printer me-2"></i> {{ __('Format POS (Thermique)') }}
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg" id="btn_print_a4">
                            <i class="bx bx-file me-2"></i> {{ __('Format A4 (Standard)') }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Fermer et Nouvelle Vente') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            let currentOrderId = null;
            const printModal = new bootstrap.Modal(document.getElementById('printModal'));

            Livewire.on('orderCreated', (event) => {
                currentOrderId = event[0].id;
                printModal.show();
            });

            document.getElementById('btn_print_pos').addEventListener('click', () => {
                if (currentOrderId) {
                    window.open(`/bakery/invoice/pos/${currentOrderId}`, '_blank');
                }
            });

            document.getElementById('btn_print_a4').addEventListener('click', () => {
                if (currentOrderId) {
                    window.open(`/bakery/invoice/a4/${currentOrderId}`, '_blank');
                }
            });

            window.addEventListener('showAlert', (event) => {
                alert(event.detail[0].message);
            });
        });
    </script>
    @endpush
</div>
