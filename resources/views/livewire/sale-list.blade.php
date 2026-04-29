<div>
    {{-- Recherche --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher_ventes --}}
            <input type="text" class="form-control" placeholder="{{ __('sale.rechercher_ventes') }}" wire:model.live.debounce.300ms="search">
        </div>
        @if (Auth::user()->role_id == 1)
        <div class="col-md-4">
            <select name="store_id" wire:model.lazy="store_id" class="form-select">
              {{-- Clé : tous_magasins --}}
              <option value="">{{ __('sale.tous_magasins') }}</option>
              @forelse ($stores as $store)
                <option value="{{ $store->id }}">{{ $store->name }}</option>
              @empty
              @endforelse
            </select>
        </div>
        @endif
    </div>

    {{-- Tableau des ventes --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- En-têtes du tableau --}}
                    <th>{{ __('sale.id') }}</th>
                    <th>{{ __('sale.client') }}</th>
                    <th>{{ __('sale.date') }}</th>
                    <th>{{ __('sale.montant_total') }}</th>
                    <th>{{ __('sale.paye') }}</th>
                    <th>{{ __('sale.statut') }}</th>
                    <th>{{ __('sale.magasin') }}</th>
                    <th>{{ __('sale.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($sales as $sale)
                    <tr wire:key="{{ $sale->id }}">
                        <td>{{ $sale->id }}</td>
                        {{-- Clé : na --}}
                        <td>{{ $sale->client->name ?? __('sale.na') }}</td>
                        <td>{{ $sale->sale_date }}</td>
                        <td>{{ number_format($sale->total_amount, 2) }} {{ company()?->devise }}</td>
                        <td>{{ number_format($sale->total_paid, 2) }} {{ company()?->devise }}</td>
                        <td>
                          {{-- Statuts de paiement --}}
                          @if ($sale->total_amount - $sale->total_paid <= 0)
                            <span class="badge bg-label-success me-1">{{ __('sale.paye_statut') }}</span>
                          @elseif ($sale->total_paid > 0 && $sale->total_amount - $sale->total_paid > 0)
                            <span class="badge bg-label-warning me-1">{{ __('sale.partiellement_paye') }}</span>
                          @else
                            <span class="badge bg-label-primary me-1">{{ __('sale.non_paye') }}</span
                          @endif
                        </td>
                        <td>{{ $sale->store->name }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" wire:click="viewDetails({{ $sale->id }})" data-bs-toggle="modal" data-bs-target="#saleDetailsModal">
                                <i class="bx bx-show me-1"></i> {{-- Clé : voir --}}
                                {{ __('sale.voir') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            {{-- Clé : aucune_vente --}}
                            {{ __('sale.aucune_vente') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $sales->links() }}
    </div>

    {{-- Modal Détails Vente --}}
    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clé : details_vente --}}
                    <h5 class="modal-title">{{ __('sale.details_vente') }} (ID: {{ $selectedSale ? $selectedSale->id : '' }})</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('sale.fermer') }}"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedSale)
                        <div class="row">
                            <div class="col-md-6">
                                {{-- Clés : client_label, date_label, statut_label, magasin_label --}}
                                <p><strong>{{ __('sale.client_label') }}</strong> {{ $selectedSale->client->name ?? __('sale.na') }}</p>
                                <p><strong>{{ __('sale.date_label') }}</strong> {{ $selectedSale->sale_date }}</p>
                                <p><strong>{{ __('sale.statut_label') }}</strong> {{ $selectedSale->status }}</p>
                                <p><strong>{{ __('sale.magasin_label') }}</strong> {{ $selectedSale->store->name }}</p>
                            </div>
                            <div class="col-md-6">
                                {{-- Clés : montant_total_label, montant_paye_label, reste_payer_label, statut_paiement_label --}}
                                <p><strong>{{ __('sale.montant_total_label') }}</strong> {{ number_format($selectedSale->total_amount, 2) }} {{ company()?->devise }}</p>
                                <p><strong>{{ __('sale.montant_paye_label') }}</strong> {{ number_format($selectedSale->total_paid, 2) }} {{ company()?->devise }}</p>
                                <p><strong>{{ __('sale.reste_payer_label') }}</strong> {{ number_format($selectedSale->total_amount - $selectedSale->total_paid, 2) }} {{ company()?->devise }}</p>
                                <p></p><strong>{{ __('sale.statut_paiement_label') }}</strong>
                                  {{-- Statuts de paiement dans le modal --}}
                                  @if ($selectedSale->total_amount - $selectedSale->total_paid <= 0)
                                    <span class="badge bg-label-success me-1">{{ __('sale.paye_statut') }}</span>
                                  @elseif ($selectedSale->total_paid > 0 && $selectedSale->total_amount - $selectedSale->total_paid > 0)
                                    <span class="badge bg-label-warning me-1">{{ __('sale.partiellement_paye') }}</span>
                                  @else
                                    <span class="badge bg-label-primary me-1">{{ __('sale.non_paye') }}</span
                                  @endif
                                </p>
                            </div>
                        </div>
                        <hr>
                        {{-- Clé : articles --}}
                        <h6>{{ __('sale.articles') }}</h6>
                        <div class="table-responsive text-nowrap">
                          <table class="table">
                              <thead>
                                  <tr>
                                      {{-- Clés pour les articles --}}
                                      <th>{{ __('sale.produit') }}</th>
                                      <th>{{ __('sale.quantite') }}</th>
                                      <th>{{ __('sale.prix_unitaire') }}</th>
                                      <th>{{ __('sale.total') }}</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  @foreach($selectedSale->items as $item)
                                  <tr>
                                      <td>{{ $item->product->name }}</td>
                                      <td>{{ $item->quantity }}</td>
                                      <td>{{ number_format($item->unit_price, 2) }} {{ company()?->devise }}</td>
                                      <td>{{ number_format($item->total_price, 2) }} {{ company()?->devise }}</td>
                                  </tr>
                                  @endforeach
                              </tbody>
                          </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    {{-- Clé : imprimer --}}
                    <button type="button" class="btn btn-outline-primary" wire:click="printInvoice()" wire:loading.attr="disabled" wire:target="printInvoice">
                        <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                        <i class="bx bx-printer me-1"></i>
                      {{ __('sale.imprimer') }}</button>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('sale.fermer') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
