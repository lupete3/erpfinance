<div>
    {{-- Recherche --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher --}}
            <input type="text" class="form-control" placeholder="{{ __('supplier_debt.rechercher') }}"
                wire:model.live.debounce.300ms="search">
        </div>
    </div>

    {{-- Tableau des dettes des fournisseurs --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('supplier_debt.id') }}</th>
                    <th>{{ __('supplier_debt.nom_fournisseur') }}</th>
                    <th>{{ __('supplier_debt.dette_totale') }}</th>
                    <th>{{ __('supplier_debt.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($suppliers as $supplier)
                    <tr wire:key="{{ $supplier->id }}">
                        <td>{{ $supplier->id }}</td>
                        <td><strong>{{ $supplier->name }}</strong></td>
                        <td>{{ number_format($supplier->debt, 2) }} {{ company()?->devise }}</td>
                        <td>
                            <button class="btn btn-success btn-sm" wire:click="selectSupplier({{ $supplier->id }})"
                                data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bx bx-dollar me-1"></i>
                                {{-- Clé : ajouter_paiement --}}
                                {{ __('supplier_debt.ajouter_paiement') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {{-- Clé : aucun_fournisseur --}}
                            {{ __('supplier_debt.aucun_fournisseur') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>

    {{-- Modal de paiement --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{-- Clé : ajouter_paiement_pour --}}
                        {{ __('supplier_debt.ajouter_paiement_pour') }}
                        {{ $selectedSupplier ? $selectedSupplier->name : '' }}
                    </h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('supplier_debt.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="savePayment">
                    <div class="modal-body">
                        @if($selectedSupplier)
                            <p>
                                <strong>{{ __('supplier_debt.dette_actuelle') }}</strong>
                                {{ number_format($selectedSupplier->debt, 2) }} {{ company()?->devise }}
                            </p>

                            {{-- Sélection facture --}}
                            <div class="mb-3">
                                {{-- Clé : selectionner_facture --}}
                                <label class="form-label">{{ __('supplier_debt.selectionner_facture') }}</label>
                                <select class="form-select @error('selectedPurchase') is-invalid @enderror"
                                    wire:model="selectedPurchase" required>
                                    {{-- Clé : choisir_facture --}}
                                    <option value="">{{ __('supplier_debt.choisir_facture') }}</option>
                                    @foreach($purchasesUnpaid as $purchase)
                                        @php
                                            $reste = $purchase->total_amount - $purchase->total_paid;
                                        @endphp
                                        <option value="{{ $purchase->id }}">
                                            Facture #{{ $purchase->id }} - Total: {{ number_format($purchase->total_amount, 2) }}
                                            /
                                            Payé: {{ number_format($purchase->total_paid, 2) }} /
                                            Reste: {{ number_format($reste, 2) }} {{ company()?->devise }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedPurchase') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            {{-- Clé : montant_paiement --}}
                            <label for="payment_amount"
                                class="form-label">{{ __('supplier_debt.montant_paiement') }}</label>
                            <input type="number" step="any"
                                class="form-control @error('payment_amount') is-invalid @enderror"
                                wire:model="payment_amount" placeholder="0.00">
                            @error('payment_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            {{-- Clé : description_optionnelle --}}
                            <label for="payment_description"
                                class="form-label">{{ __('supplier_debt.description_optionnelle') }}</label>
                            <textarea class="form-control" wire:model="payment_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('supplier_debt.fermer') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            {{-- Clé : enregistrer_paiement --}}
                            {{ __('supplier_debt.enregistrer_paiement') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>