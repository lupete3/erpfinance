<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Suivi des Dettes Clients') }}
    </h4>

    {{-- Success Message --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div
            class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h5 class="card-title mb-0">{{ __('Commandes avec Reste à Payer') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                {{-- Store Filter --}}
                <div style="min-width: 200px;">
                    <select class="form-select" wire:model.live="filterSiteId">
                        <option value="">{{ __('Tous les points de vente') }}</option>
                        @foreach($sites as $site)
                            <option value="{{ $site->id }}">{{ $site->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="input-group input-group-merge" style="min-width: 250px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Filtrer par client...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>

                <a href="{{ route('bakery.pos') }}" class="btn btn-primary text-nowrap">
                    <i class="bx bx-cart me-1"></i> {{ __('Vente POS') }}
                </a>
            </div>
        </div>
        <div class="card-datatable table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Actions') }}</th>
                        <th>{{ __('Date Vente') }}</th>
                        <th>{{ __('Nom Client') }}</th>
                        <th>{{ __('Total à payer') }}</th>
                        <th>{{ __('Total payé') }}</th>
                        <th class="text-danger">{{ __('Dette') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $tot = 0;
                        $totPaye = 0;
                        $totReste = 0;
                        $id = 1;
                    @endphp

                    @forelse($commandes as $commande)
                        @php
                            $tot += $commande->montant;
                            $totPaye += $commande->paye;
                            $totReste += $commande->reste;
                        @endphp
                        <tr>
                            <td>{{ $id++ }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-sm btn-primary"
                                        wire:click="openPaymentModal({{ $commande->id }})" title="{{ __('Payer') }}">
                                        <i class="bx bx-money me-1"></i> {{ __('Payer') }}
                                    </button>
                                    <button class="btn btn-sm btn-info" wire:click="openDetailsModal({{ $commande->id }})"
                                        title="{{ __('Détails') }}">
                                        <i class="bx bx-list-ul me-1"></i> {{ __('Détails') }}
                                    </button>
                                </div>
                            </td>
                            <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ $commande->client?->nom ?? 'N/A' }}</strong></td>
                            <td>{{ number_format($commande->montant, 0, ',', ' ') }} FC</td>
                            <td class="text-success">{{ number_format($commande->paye, 0, ',', ' ') }} FC</td>
                            <td class="text-danger fw-bold">{{ number_format($commande->reste, 0, ',', ' ') }} FC</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                {{ __('Aucune dette en cours.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($commandes->total() > 0)
                    <tfoot>
                        <tr class="table-secondary fw-bold">
                            <td colspan="4" class="text-end"><strong>{{ __('TOTAL') }}</strong></td>
                            <td><strong>{{ number_format($tot, 0, ',', ' ') }} FC</strong></td>
                            <td><strong>{{ number_format($totPaye, 0, ',', ' ') }} FC</strong></td>
                            <td class="text-danger"><strong>{{ number_format($totReste, 0, ',', ' ') }} FC</strong></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        @if($commandes->total() > 0)
            <div class="card-footer px-3 py-2 border-top">
                {{ $commandes->links() }}
            </div>
        @endif
    </div>

    <!-- Modal for Payment -->
    <div wire:ignore.self class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Enregistrer un Paiement') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storePayment">
                    <div class="modal-body">
                        @if($commandeDetails)
                            <div class="alert alert-info py-2 small mb-4">
                                <strong>{{ __('client.nom') }} :</strong> {{ $commandeDetails->client?->nom ?? 'N/A' }}<br>
                                <strong>{{ __('Total Commande :') }}</strong>
                                {{ number_format($commandeDetails->montant, 0, ',', ' ') }} FC<br>
                                <strong>{{ __('Reste Actuel :') }}</strong> <span
                                    class="text-danger fw-bold">{{ number_format($commandeDetails->reste, 0, ',', ' ') }}
                                    FC</span>
                            </div>
                        @endif
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Point de Vente') }}</label>
                                <select class="form-select @error('selectedSiteId') is-invalid @enderror"
                                    wire:model="selectedSiteId">
                                    <option value="">{{ __('Sélectionner un point de vente...') }}</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                    @endforeach
                                </select>
                                @error('selectedSiteId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('Montant à Verser (FC)') }}</label>
                                <input type="number"
                                    class="form-control form-control-lg fw-bold @error('montantPaye') is-invalid @enderror"
                                    wire:model="montantPaye">
                                @error('montantPaye') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i> {{ __('Valider le Paiement') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Details -->
    <div wire:ignore.self class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ __('Détails de la Commande') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedDetailsCommande)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ __('client.nom') }} :</strong>
                                    {{ $selectedDetailsCommande->client?->nom ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>{{ __('Date') }} :</strong>
                                    {{ $selectedDetailsCommande->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="mb-1"><strong>{{ __('Total') }} :</strong>
                                    {{ number_format($selectedDetailsCommande->montant, 0, ',', ' ') }} FC</p>
                                <p class="mb-0"><strong>{{ __('Reste') }} :</strong> <span
                                        class="text-danger fw-bold">{{ number_format($selectedDetailsCommande->reste, 0, ',', ' ') }}
                                        FC</span></p>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped border">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('Produit') }}</th>
                                        <th class="text-center">{{ __('Qté') }}</th>
                                        <th class="text-end">{{ __('P.U') }}</th>
                                        <th class="text-end">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($selectedDetailsCommande->ventes as $vente)
                                        <tr>
                                            <td>{{ $vente->designation ?? $vente->produit ?? 'N/A' }}</td>
                                            <td class="text-center">{{ $vente->quantite }}</td>
                                            <td class="text-end">{{ number_format($vente->prix, 0, ',', ' ') }} FC</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($vente->quantite * $vente->prix, 0, ',', ' ') }} FC
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Fermer') }}</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:navigated', () => {
                window.addEventListener('openModal', (event) => {
                    const modalElement = document.getElementById(event.detail[0].id);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
                        modal.show();
                    }
                });

                window.addEventListener('closeModal', (event) => {
                    const modalElement = document.getElementById(event.detail[0].id);
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                    }
                });
            });
        </script>
    @endpush
</div>