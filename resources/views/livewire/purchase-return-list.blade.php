<div>
    {{-- Bouton Ajouter --}}
    <div class="d-flex justify-content-end align-items-center mb-3">
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#purchaseReturnModal">
            <i class="bx bx-plus me-1"></i>
            {{-- Clé : ajouter --}}
            {{ __('purchase_return.ajouter') }}
        </button>
    </div>

    {{-- Tableau des retours d'achat --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    {{-- En-têtes du tableau --}}
                    <th>{{ __('purchase_return.id') }}</th>
                    <th>{{ __('purchase_return.id_achat') }}</th>
                    <th>{{ __('purchase_return.produit') }}</th>
                    <th>{{ __('purchase_return.quantite') }}</th>
                    <th>{{ __('purchase_return.date') }}</th>
                    <th>{{ __('purchase_return.raison') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($purchaseReturns as $return)
                    <tr wire:key="{{ $return->id }}">
                        <td>{{ $return->id }}</td>
                        {{-- Supposons qu'il y aura un dispatch pour voir les détails d'achat --}}
                        <td><a href="#" wire:click.prevent="$dispatch('showPurchaseDetails', { purchaseId: {{ $return->purchase_id }} })">{{ $return->purchase_id }}</a></td>
                        {{-- Utilisation de la clé 'produit' --}}
                        <td>{{ $return->product->name ?? __('N/A') }}</td>
                        <td>{{ $return->quantity }}</td>
                        <td>{{ $return->return_date }}</td>
                        <td>{{ Str::limit($return->reason, 30) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            {{-- Clé : aucun_retour --}}
                            {{ __('purchase_return.aucun_retour') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $purchaseReturns->links() }}
    </div>

    {{-- Modal Retour d'achat --}}
    <div class="modal fade" id="purchaseReturnModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- Clé : creer_retour --}}
                    <h5 class="modal-title">{{ __('purchase_return.creer_retour') }}</h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('purchase_return.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- Clé : id_achat --}}
                            <label class="form-label">{{ __('purchase_return.id_achat') }}</label>
                            {{-- Clé : entrez_id_achat --}}
                            <input type="number" class="form-control @error('purchase_id') is-invalid @enderror" wire:model.live="purchase_id" placeholder="{{ __('purchase_return.entrez_id_achat') }}">
                            @error('purchase_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        @if($purchaseProducts)
                        <div class="mb-3">
                            {{-- Clé : produit --}}
                            <label class="form-label">{{ __('purchase_return.produit') }}</label>
                            <select class="form-select @error('product_id') is-invalid @enderror" wire:model="product_id">
                                {{-- Clé : selectionnez_produit --}}
                                <option value="">{{ __('purchase_return.selectionnez_produit') }}</option>
                                @foreach($purchaseProducts as $item)
                                    <option value="{{ $item->product->id }}">
                                        {{ $item->product->name }} ({{ __('purchase_return.achete') }}: {{ $item->quantity }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            {{-- Clé : quantite --}}
                            <label class="form-label">{{ __('purchase_return.quantite') }}</label>
                            {{-- Clé : entrez_quantite --}}
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" wire:model="quantity" placeholder="{{ __('purchase_return.entrez_quantite') }}">
                            @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            {{-- Clé : raison --}}
                            <label class="form-label">{{ __('purchase_return.raison') }}</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" wire:model="reason" rows="3"></textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('purchase_return.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{-- Clé : enregistrer_retour --}}
                            {{ __('purchase_return.enregistrer_retour') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
