<div>
    {{-- Recherche --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            {{-- Clé : rechercher --}}
            <input type="text" class="form-control" placeholder="{{ __('customer_debt.rechercher') }}"
                wire:model.live.debounce.300ms="search">
        </div>
    </div>

    {{-- Tableau des dettes clients --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('customer_debt.nom_client') }}</th>
                    <th>{{ __('customer_debt.dette_totale') }}</th>
                    <th>{{ __('customer_debt.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($clients as $index => $client)
                    <tr wire:key="{{ $client->id }}">
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $client->name }}</strong></td>
                        <td>{{ number_format($client->debt, 2) }} {{ company()?->devise }}</td>
                        <td>
                            <button class="btn btn-success btn-sm" wire:click="selectClient({{ $client->id }})"
                                data-bs-toggle="modal" data-bs-target="#paymentModal">
                                <i class="bx bx-dollar me-1"></i>
                                {{-- Clé : ajouter_paiement --}}
                                {{ __('customer_debt.ajouter_paiement') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            {{-- Clé : aucun_client --}}
                            {{ __('customer_debt.aucun_client') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $clients->links() }}
    </div>

    {{-- Modal de paiement --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{-- Clé : ajouter_paiement_pour --}}
                        {{ __('customer_debt.ajouter_paiement_pour') }}
                        {{ $selectedClient ? $selectedClient->name : '' }}
                    </h5>
                    {{-- Clé : fermer --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('customer_debt.fermer') }}"></button>
                </div>
                <form wire:submit.prevent="savePayment">
                    <div class="modal-body">
                        @if($selectedClient)
                            {{-- Clé : dette_actuelle --}}
                            <p><strong>{{ __('customer_debt.dette_actuelle') }}</strong>
                                {{ number_format($selectedClient->debt, 2) }} {{ company()?->devise }}</p>
                            <div class="mb-3">
                                {{-- Clé : selectionner_vente --}}
                                <label for="selectedSale"
                                    class="form-label">{{ __('customer_debt.selectionner_vente') }}</label>
                                <select class="form-select @error('selectedSale') is-invalid @enderror"
                                    wire:model.lazy="selectedSale">
                                    {{-- Clé : choisir_vente --}}
                                    <option value="">{{ __('customer_debt.choisir_vente') }}</option>
                                    @foreach($salesUnpaid as $sale)
                                        @php
                                            $reste = $sale->total_amount - $sale->total_paid;
                                        @endphp
                                        <option value="{{ $sale->id }}">
                                            Vente #{{ $sale->id }} - Total: {{ number_format($sale->total_amount, 2) }} /
                                            Payé: {{ number_format($sale->total_paid, 2) }} /
                                            Reste: {{ number_format($reste, 2) }} {{ company()?->devise }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('selectedSale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        <div class="mb-3">
                            {{-- Clé : montant_paiement --}}
                            <label for="payment_amount"
                                class="form-label">{{ __('customer_debt.montant_paiement') }}</label>
                            <input type="number" step="any"
                                class="form-control @error('payment_amount') is-invalid @enderror"
                                wire:model="payment_amount" placeholder="0.00">
                            @error('payment_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            {{-- Clé : description_optionnelle --}}
                            <label for="payment_description"
                                class="form-label">{{ __('customer_debt.description_optionnelle') }}</label>
                            <textarea class="form-control" wire:model="payment_description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- Clé : fermer --}}
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('customer_debt.fermer') }}</button>
                        @if ($selectedClient)
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                                {{-- Clé : enregistrer_paiement --}}
                                {{ __('customer_debt.enregistrer_paiement') }}
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>