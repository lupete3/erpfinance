<div>
    {{-- Bouton Ajouter --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#saleReturnModal">
            <i class="bx bx-plus me-1"></i>
            {{-- Clé : ajouter --}}
            {{ __('sale_return.ajouter') }}
        </button>
    </div>

    {{-- Tableau des retours de ventes --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- En-têtes du tableau --}}
                    <th>{{ __('sale_return.client') }}</th>
                    <th>{{ __('sale_return.id_vente') }}</th>
                    <th>{{ __('sale_return.produit') }}</th>
                    <th>{{ __('sale_return.quantite') }}</th>
                    <th>{{ __('sale_return.magasin') }}</th>
                    <th>{{ __('sale_return.date') }}</th>
                    <th>{{ __('sale_return.raison') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($saleReturns as $return)
                    <tr wire:key="{{ $return->id }}">
                        <td>{{ $return->sale->client->name }}</td>
                        <td>
                            <a href="#" wire:click.prevent="$dispatch('showSaleDetails', { saleId: {{ $return->sale_id }} })">
                                {{ $return->sale_id }}
                            </a>
                        </td>
                        {{-- Clé : na (supposons que "N/A" peut réutiliser la clé du module 'sale') --}}
                        <td>{{ $return->product->name ?? __('sale.na') }}</td>
                        <td>{{ $return->quantity }}</td>
                        <td>{{ $return->sale->store->name }}</td>
                        <td>{{ $return->return_date }}</td>
                        <td>{{ Str::limit($return->reason, 30) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            {{-- Clé : aucun_retour --}}
                            {{ __('sale_return.aucun_retour') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $saleReturns->links() }}
    </div>

    {{-- Modal Retour de vente --}}
    <div class="modal fade" id="saleReturnModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clé : creer_retour_vente --}}
                    <h5 class="modal-title">{{ __('sale_return.creer_retour_vente') }}</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('sale_return.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- Clé : id_vente --}}
                            <label class="form-label">{{ __('sale_return.id_vente') }}</label>
                            {{-- Clé : entrez_id_vente --}}
                            <input type="number" class="form-control @error('sale_id') is-invalid @enderror" wire:model.live="sale_id" placeholder="{{ __('sale_return.entrez_id_vente') }}">
                            @error('sale_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($saleProducts)
                        <div class="mb-3">
                            {{-- Clé : produit --}}
                            <label class="form-label">{{ __('sale_return.produit') }}</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" wire:model="product_id">
                                {{-- Clé : selectionner_produit --}}
                                <option value="">{{ __('sale_return.selectionner_produit') }}</option>
                                @foreach($saleProducts as $item)
                                    <option value="{{ $item->product->id }}">
                                        {{ $item->product->name }} ({{ __('sale_return.achete') }} {{ $item->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            {{-- Clé : quantite --}}
                            <label class="form-label">{{ __('sale_return.quantite') }}</label>
                            {{-- Clé : entrez_quantite --}}
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" wire:model="quantity" placeholder="{{ __('sale_return.entrez_quantite') }}">
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            {{-- Clé : raison --}}
                            <label class="form-label">{{ __('sale_return.raison') }}</label>
                            {{-- Clé : entrez_raison --}}
                            <textarea class="form-control @error('reason') is-invalid @enderror" wire:model="reason" rows="3" placeholder="{{ __('sale_return.entrez_raison') }}"></textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('sale_return.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{-- Clé : enregistrer_retour --}}
                            {{ __('sale_return.enregistrer_retour') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
