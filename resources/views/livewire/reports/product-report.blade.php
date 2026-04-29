<div>

    @if (Auth::user()->role_id == 1)
    <div class="row">
        <div class="col-12 col-md-4 mb-3">
            <select class="form-select" wire:model.lazy="selectedStoreId">
                <option value="">{{ __('product_report.selectionner_magasin') }}</option>
                @foreach ($stores as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @endif

    @if ($selectedStoreId)
        <div class="card mb-3">
            <div class="card-body row g-2">
                <div class="col-md-2">
                    <input type="text" class="form-control" placeholder="{{ __('product_report.rechercher') }}"
                        wire:model.live="search">
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.lazy="category_id">
                        <option value="">{{ __('product_report.toutes_categories') }}</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.lazy="brand_id">
                        <option value="">{{ __('product_report.toutes_marques') }}</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" wire:model.lazy="stock_status">
                        <option value="">{{ __('product_report.tous') }}</option>
                        <option value="low">{{ __('product_report.stock_faible') }}</option>
                        <option value="out">{{ __('product_report.rupture') }}</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control" placeholder="{{ __('product_report.prix_min') }}"
                        wire:model.lazy="min_price">
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control" placeholder="{{ __('product_report.prix_max') }}"
                        wire:model.lazy="max_price">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-danger" wire:click="exportPdf" wire:target="exportPdf"
                        wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="bx bx-download"></i> {{ __('product_report.exporter_pdf') }}</button>
                </div>
            </div>
        </div>

        <div class="row text-center mb-3">
            <div class="col-12 col-md-3 mb-2">
                <div class="card p-2 shadow-sm">
                    <strong>{{ __('product_report.total_produits') }}</strong>
                    <h5>{{ $total_products }}</h5>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2">
                <div class="card p-2 shadow-sm">
                    <strong>{{ __('product_report.valeur_stock_achat') }}</strong>
                    <h5>{{ number_format($total_stock_value, 2) }} {{ company()?->devise }}</h5>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2">
                <div class="card p-2 shadow-sm">
                    <strong>{{ __('product_report.valeur_stock_vente') }}</strong>
                    <h5>{{ number_format($total_stock_potential, 2) }} {{ company()?->devise }}</h5>
                </div>
            </div>
            <div class="col-12 col-md-3 mb-2">
                <div class="card p-2 shadow-sm">
                    <strong>{{ __('product_report.stock_faible_total') }}</strong>
                    <h5 class="text-danger">{{ $low_stock }}</h5>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('product_report.ref') }}</th>
                            <th>{{ __('product_report.nom') }}</th>
                            <th>{{ __('product_report.categorie') }}</th>
                            <th>{{ __('product_report.marque') }}</th>
                            <th>{{ __('product_report.stock') }}</th>
                            <th>{{ __('product_report.prix_achat') }}</th>
                            <th>{{ __('product_report.prix_vente') }}</th>
                            <th>{{ __('product_report.valeur_stock') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $prod)
                            <tr>
                                <td>{{ $prod->reference }}</td>
                                <td>{{ $prod->name }}</td>
                                <td>{{ $prod->category?->name }}</td>
                                <td>{{ $prod->brand?->name }}</td>
                                @php
                                    $storeStock =
                                        $prod->stores->where('id', $selectedStoreId)->first()->pivot->quantity ?? 0;
                                @endphp

                                <td>
                                    <span class="@if ($storeStock <= $prod->stock_alert) text-danger @endif">
                                        {{ $storeStock }}
                                    </span>
                                </td>
                                <td>{{ number_format($prod->purchase_price, 2) }}</td>
                                <td>{{ number_format($prod->sale_price, 2) }}</td>
                                <td>{{ number_format($storeStock * $prod->sale_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ __('product_report.aucun_produit') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>