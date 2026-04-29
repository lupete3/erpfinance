<div>
    {{-- Search --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher --}}
            <input type="text" class="form-control" placeholder="{{ __('purchase_list.rechercher') }}" wire:model.live.debounce.300ms="search">
        </div>
    </div>

    {{-- Purchases Table --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('purchase_list.id') }}</th>
                    <th>{{ __('purchase_list.fournisseur') }}</th>
                    <th>{{ __('purchase_list.date') }}</th>
                    <th>{{ __('purchase_list.montant_total') }}</th>
                    <th>{{ __('purchase_list.paye') }}</th>
                    <th>{{ __('purchase_list.statut') }}</th>
                    <th>{{ __('purchase_list.magasin') }}</th>
                    <th>{{ __('purchase_list.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($purchases as $purchase)
                    <tr wire:key="{{ $purchase->id }}">
                        <td>{{ $purchase->id }}</td>
                        <td>{{ $purchase->supplier->name ?? __('N/A') }}</td>
                        <td>{{ $purchase->purchase_date }}</td>
                        <td>{{ number_format($purchase->total_amount, 2) }} {{ company()?->devise }}</td>
                        <td>{{ number_format($purchase->total_paid, 2) }} {{ company()?->devise }}</td>
                        <td>
                            @if ($purchase->total_amount - $purchase->total_paid <= 0)
                                {{-- Clé : payé --}}
                                <span class="badge bg-label-success me-1">{{ __('purchase_list.payé') }}</span>
                            @elseif ($purchase->total_paid > 0 && $purchase->total_amount - $purchase->total_paid > 0)
                                {{-- Clé : partiellement_paye --}}
                                <span class="badge bg-label-warning me-1">{{ __('purchase_list.partiellement_paye') }}</span>
                            @else
                                {{-- Clé : non_paye --}}
                                <span class="badge bg-label-primary me-1">{{ __('purchase_list.non_paye') }}</span
                            @endif
                        </td>
                        <td>{{ $purchase->store->name ?? __('N/A') }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" wire:click="viewDetails({{ $purchase->id }})" data-bs-toggle="modal" data-bs-target="#purchaseDetailsModal">
                                <i class="bx bx-show me-1"></i>
                                {{-- Clé : voir --}}
                                {{ __('purchase_list.voir') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">
                            {{-- Clé : aucun_achat --}}
                            {{ __('purchase_list.aucun_achat') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $purchases->links() }}
    </div>

    {{-- Purchase Details Modal --}}
    <div class="modal fade" id="purchaseDetailsModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clé : details_achat --}}
                    <h5 class="modal-title">{{ __('purchase_list.details_achat') }} (ID: {{ $selectedPurchase ? $selectedPurchase->id : '' }})</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="{{ __('purchase_list.fermer') }}"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedPurchase)
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('purchase_list.fournisseur') }}:</strong> {{ $selectedPurchase->supplier->name ?? __('N/A') }}</p>
                                <p><strong>{{ __('purchase_list.date') }}:</strong> {{ $selectedPurchase->purchase_date }}</p>
                                <p><strong>{{ __('purchase_list.statut') }}:</strong> {{ $selectedPurchase->status }}</p>
                                <p><strong>{{ __('purchase_list.magasin') }}:</strong> {{ $selectedPurchase->store->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('purchase_list.montant_total') }}:</strong> {{ number_format($selectedPurchase->total_amount, 2) }} {{ company()?->devise }}</p>
                                <p><strong>{{ __('purchase_list.paye') }}:</strong> {{ number_format($selectedPurchase->total_paid, 2) }} {{ company()?->devise }}</p>
                                {{-- Clé : reste_a_payer --}}
                                <p><strong>{{ __('purchase_list.reste_a_payer') }}:</strong> {{ number_format($selectedPurchase->total_amount - $selectedPurchase->total_paid, 2) }} {{ company()?->devise }}</p>
                                <p>
                                    @if ($selectedPurchase->total_amount - $selectedPurchase->total_paid <= 0)
                                        <span class="badge bg-label-success me-1">{{ __('purchase_list.payé') }}</span>
                                    @elseif ($selectedPurchase->total_paid > 0 && $selectedPurchase->total_amount - $selectedPurchase->total_paid > 0)
                                        <span class="badge bg-label-warning me-1">{{ __('purchase_list.partiellement_paye') }}</span>
                                    @else
                                        <span class="badge bg-label-primary me-1">{{ __('purchase_list.non_paye') }}</span
                                    @endif
                                </p>
                            </div>
                        </div>
                        <hr>
                        {{-- Clé : articles --}}
                        <h6>{{ __('purchase_list.articles') }}:</h6>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('purchase_list.produit') }}</th>
                                    <th>{{ __('purchase_list.quantite') }}</th>
                                    <th>{{ __('purchase_list.prix_unitaire') }}</th>
                                    <th>{{ __('purchase_list.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedPurchase->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->unit_price, 2) }} {{ company()?->devise }}</td>
                                    <td>{{ number_format($item->total_price, 2) }} {{ company()?->devise }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('purchase_list.fermer') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
