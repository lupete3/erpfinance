<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Stock') }} /</span>
        {{ __('Transferts Inter-Sites') }}
    </h4>

    {{-- Alert Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Main Container --}}
    <div class="row">
        {{-- Report/History List --}}
        <div class="{{ $isCreateMode ? 'col-md-7' : 'col-md-12' }}">
            <div class="card">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Rapport des Transferts') }}</h5>
                    <button class="btn btn-primary" wire:click="toggleCreateMode">
                        <i class="bx bx-{{ $isCreateMode ? 'list-ul' : 'plus' }} me-1"></i>
                        {{ $isCreateMode ? __('Voir Liste') : __('Nouveau Transfert') }}
                    </button>
                </div>
                
                {{-- Filters --}}
                <div class="card-body bg-light-subtle py-3 border-bottom">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" wire:model.live="filter_from_site">
                                <option value="">{{ __('De : Tous') }}</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select form-select-sm" wire:model.live="filter_to_site">
                                <option value="">{{ __('Vers : Tous') }}</option>
                                @foreach($sites as $site)
                                    <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control form-control-sm" wire:model.live="filter_start_date">
                        </div>
                        <div class="col-md-3">
                            <input type="date" class="form-control form-control-sm" wire:model.live="filter_end_date">
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive">
                    <table class="table border-top table-hover">
                        <thead>
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Origine') }}</th>
                                <th>{{ __('Destination') }}</th>
                                <th>{{ __('Articles') }}</th>
                                <th>{{ __('Par') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transfers as $transfer)
                                <tr class="border-bottom">
                                    <td class="small">{{ $transfer->transfer_date }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $transfer->fromSite->nom }}</span></td>
                                    <td><span class="badge bg-label-info">{{ $transfer->toSite->nom }}</span></td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach($transfer->items as $item)
                                                <li><strong>{{ $item->quantity }}</strong> {{ $item->product->designation }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="small">{{ $transfer->user->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bx bx-info-circle d-block mb-2 fs-2"></i>
                                        {{ __('Aucun transfert trouvé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer px-3 py-2 border-top">
                    {{ $transfers->links() }}
                </div>
            </div>
        </div>

        {{-- Form for New Transfer --}}
        @if($isCreateMode)
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header border-bottom bg-primary">
                        <h5 class="card-title mb-0 text-white">{{ __('Nouveau Transfert Interne') }}</h5>
                    </div>
                    <form wire:submit.prevent="submitTransfer">
                        <div class="card-body py-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('Site de Départ (Origine)') }}</label>
                                    <select class="form-select @error('from_site_id') is-invalid @enderror" wire:model="from_site_id">
                                        <option value="">{{ __('Sélectionner source') }}</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('from_site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('Site de Destination') }}</label>
                                    <select class="form-select @error('to_site_id') is-invalid @enderror" wire:model="to_site_id">
                                        <option value="">{{ __('Sélectionner destination') }}</option>
                                        @foreach($sites as $site)
                                            <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('to_site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold">{{ __('Date & Heure') }}</label>
                                    <input type="datetime-local" class="form-control @error('transfer_date') is-invalid @enderror" wire:model="transfer_date">
                                    @error('transfer_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <hr class="my-4">

                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-bold mb-0">{{ __('Articles à transférer') }}</label>
                                        <button type="button" class="btn btn-xs btn-outline-primary" wire:click="addItem">
                                            <i class="bx bx-plus me-1"></i> {{ __('Ajouter ligne') }}
                                        </button>
                                    </div>
                                    
                                    @foreach($items as $index => $item)
                                        <div class="row g-2 mb-3 border p-2 rounded bg-light-subtle">
                                            <div class="col-7">
                                                <select class="form-select form-select-sm @error('items.'.$index.'.stock_pf_id') is-invalid @enderror" wire:model="items.{{ $index }}.stock_pf_id">
                                                    <option value="">{{ __('Produit') }}</option>
                                                    @foreach($products as $prod)
                                                        <option value="{{ $prod->id }}">{{ $prod->designation . ' ' . $prod->prix_unite }}</option>
                                                    @endforeach
                                                </select>
                                                @error('items.'.$index.'.stock_pf_id') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-4">
                                                <input type="number" step="0.01" class="form-control form-control-sm @error('items.'.$index.'.quantity') is-invalid @enderror" 
                                                    wire:model="items.{{ $index }}.quantity" placeholder="{{ __('Qté') }}">
                                                @error('items.'.$index.'.quantity') <div class="invalid-feedback small">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-1 d-flex align-items-center">
                                                @if(count($items) > 1)
                                                    <button type="button" class="btn btn-text-danger btn-xs p-0" wire:click="removeItem({{ $index }})">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label fw-bold">{{ __('Notes / Observation') }}</label>
                                    <textarea class="form-control" wire:model="notes" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer border-top bg-light">
                            <button type="submit" class="btn btn-primary w-100 py-2 shadow" wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i class="bx bx-check-circle me-1"></i> {{ __('Valider le Transfert') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
