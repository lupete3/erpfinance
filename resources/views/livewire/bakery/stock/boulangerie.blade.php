<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Points de Vente') }}
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
            class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <h5 class="card-title mb-0">{{ __('Situation des stocks en points de vente') }}</h5>

            <div class="d-flex flex-wrap align-items-center gap-2">
                @if(!Auth::user()->isBakeryUser() || Auth::user()->hasRoleString('admin'))
                    <div style="min-width: 200px;">
                        <select class="form-select" wire:model.live="site_id">
                            <option value="">{{ __('Tous les points de vente') }}</option>
                            @foreach($sites as $site)
                                <option value="{{ $site->id }}">{{ $site->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="badge bg-label-info p-2 px-3">
                        <i class="bx bx-map-pin me-1"></i> {{ Auth::user()->site->nom ?? 'Site inconnu' }}
                    </div>
                @endif

                <div class="input-group input-group-merge" style="width: 250px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Filtrer par produit...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
            </div>
        </div>

        <div class="card-datatable table-responsive">
            <table class="table border-top table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        @if(!Auth::user()->isBakeryUser())
                            <th>{{ __('Point de Vente') }}</th>
                        @endif
                        <th>{{ __('Produit Fini') }}</th>
                        <th>{{ __('Prix Unitaire') }}</th>
                        <th>{{ __('Solde') }}</th>
                        <th>{{ __('Valeur') }}</th>
                        <th>{{ __('Dernière mise à jour') }}</th>
                        @if(Auth::user()->role === 'admin')
                            <th class="text-center">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($produits->currentPage() - 1) * $produits->perPage() + 1; @endphp
                    @forelse($produits as $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            @if(!Auth::user()->isBakeryUser())
                                <td><span class="badge bg-label-secondary">{{ $item->site->nom }}</span></td>
                            @endif
                            <td><strong>{{ $item->stockProduitFinis->designation }}</strong></td>
                            <td>{{ number_format($item->stockProduitFinis->prix, 0, ',', ' ') }} FC</td>
                            <td>
                                <span class="badge bg-label-{{ $item->solde <= 5 ? 'danger' : 'info' }}">
                                    {{ $item->solde }} {{ __('pcs') }}
                                </span>
                            </td>
                            <td>{{ number_format($item->stockProduitFinis->prix * $item->solde, 0, ',', ' ') }} FC</td>
                            <td class="small">{{ $item->updated_at->diffForHumans() }}</td>
                            @if(Auth::user()->role === 'admin')
                                <td class="text-center">
                                    <button class="btn btn-sm btn-icon btn-label-primary" wire:click="openAdjustmentModal({{ $item->id }})" title="{{ __('Ajuster le Stock') }}">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->isBakeryUser() ? (Auth::user()->role === 'admin' ? 7 : 6) : (Auth::user()->role === 'admin' ? 8 : 7) }}" class="text-center py-5 text-muted">
                                <i class="bx bx-info-circle d-block mb-2 fs-2"></i>
                                {{ __('Aucun stock trouvé pour ce site ou critère.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
    <!-- Modal for Adjustment -->
    <div wire:ignore.self class="modal fade" id="adjustmentModalBoulangerie" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header shadow-sm mt-0">
                    <h5 class="modal-title">{{ __('Ajuster le Stock Point de Vente') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateAdjustment">
                    <div class="modal-body">
                        <div class="row g-3 text-start">
                            <div class="col-12">
                                <label class="form-label">{{ __('Nouveau Solde en Stock') }}</label>
                                <div class="input-group">
                                    <input type="number" step="0.01"
                                        class="form-control @error('adjustmentQuantity') is-invalid @enderror"
                                        wire:model="adjustmentQuantity" placeholder="0.00">
                                    <span class="input-group-text">{{ __('pcs') }}</span>
                                </div>
                                @error('adjustmentQuantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary shadow">
                            <i class="bx bx-check me-1"></i> {{ __('Confirmer l\'Ajustement') }}
                        </button>
                    </div>
                </form>
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
                @if($produits->total() > 0)
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="{{ Auth::user()->isBakeryUser() ? 4 : 5 }}" class="fw-bold text-end pe-4">
                                {{ __('Valeur Totale du Stock Sélectionné') }}
                            </td>
                            <td colspan="3" class="fw-bold text-info">{{ number_format($tot, 0, ',', ' ') }} FC</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $produits->links() }}
        </div>
    </div>
</div>