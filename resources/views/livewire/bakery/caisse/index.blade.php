<div>
    <div class="row g-4">
        {{-- Flash Messages --}}
        <div class="col-12">
            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <div class="d-flex">
                        <i class="bx bx-check-circle me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <div class="d-flex">
                        <i class="bx bx-error-circle me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        {{-- Header & Balance Card --}}
        <div class="col-12">
            <div class="card bg-primary text-white shadow-none border-0 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 mt-4 gap-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md bg-white text-primary rounded me-3">
                                <i class="bx bx-wallet fs-3"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 text-white fw-bold">{{ __('Caisse') }}</h4>
                                <p class="mb-0 opacity-75 small">{{ __('Suivi des fonds') }}</p>
                            </div>
                        </div>
                        <div class="text-md-end">
                            <p class="mb-0 small opacity-75">{{ __('Solde Actuel') }}</p>
                            <h2 class="mb-0 text-white fw-bold">{{ number_format($currentSolde, 0, ',', ' ') }} FC</h2>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-white text-primary fw-bold" data-bs-toggle="modal"
                            data-bs-target="#caisseModal" wire:click="$set('type_operation', 'entree')">
                            <i class="bx bx-plus-circle me-1"></i> {{ __('Entrée de fonds') }}
                        </button>
                        <button class="btn btn-danger text-white fw-bold" data-bs-toggle="modal"
                            data-bs-target="#caisseModal" wire:click="$set('type_operation', 'sortie')">
                            <i class="bx bx-minus-circle me-1"></i> {{ __('Sortie de fonds') }}
                        </button>
                    </div>
                </div>
                {{-- Subtle pattern background --}}
                <div class="position-absolute end-0 bottom-0 p-3 opacity-25">
                    <i class="bx bxs-bank" style="font-size: 8rem;"></i>
                </div>
            </div>
        </div>

        {{-- Table Filter --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div
                    class="card-header border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                    <h5 class="card-title mb-0 fw-bold">{{ __('Historique des Opérations') }}</h5>
                    <div
                        class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                        <div class="input-group input-group-merge" style="min-width: 200px;">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                                wire:model.live.debounce.300ms="search">
                        </div>
                        @if(auth()->user()->role === 'admin')
                            <button class="btn btn-label-secondary text-nowrap" title="{{ __('Recalculer les soldes') }}"
                                wire:click="recalculerSoldes" wire:loading.attr="disabled">
                                <i class="bx bx-refresh me-1" wire:loading.class="bx-spin"></i> {{ __('Recalculer') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">{{ __('Date & Heure') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Motif / Description') }}</th>
                                    <th class="text-end">{{ __('Montant') }}</th>
                                    <th class="text-end text-primary">{{ __('Solde Progressif') }}</th>
                                    <th>{{ __('Utilisateur') }}</th>
                                    <th class="text-center px-4">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($caisses as $op)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="small">{{ $op->created_at->format('d/m/Y') }}</span><br>
                                            <span class="text-muted extra-small">{{ $op->created_at->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            @if($op->type_operation === 'entree')
                                                <span class="badge bg-label-success rounded-pill px-3">
                                                    <i class="bx bx-trending-up me-1"></i> {{ __('Entrée') }}
                                                </span>
                                            @else
                                                <span class="badge bg-label-danger rounded-pill px-3">
                                                    <i class="bx bx-trending-down me-1"></i> {{ __('Sortie') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-medium small">{{ $op->motif }}</span>
                                        </td>
                                        <td
                                            class="text-end fw-bold {{ $op->type_operation === 'entree' ? 'text-success' : 'text-danger' }}">
                                            {{ $op->type_operation === 'entree' ? '+' : '-' }}
                                            {{ number_format($op->montant, 0, ',', ' ') }} FC
                                        </td>
                                        <td class="text-end fw-bold text-primary italic">
                                            {{ number_format($op->solde_apres_operation, 0, ',', ' ') }} FC
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2">
                                                    <span
                                                        class="menu-icon tf-icons bx bx-user-circle rounded-circle small text-uppercase">
                                                        {{ substr($op->user->name ?? 'U', 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="small">{{ $op->user->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center px-4">
                                            @if(auth()->user()->role === 'admin')
                                                <button class="btn btn-sm btn-icon btn-label-danger"
                                                    wire:click="deleteOperation({{ $op->id }})"
                                                    wire:confirm="{{ __('Voulez-vous vraiment supprimer cette opération ? Le solde global sera recalculé.') }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="py-3">
                                                <i class="bx bx-spreadsheet fs-1 text-muted opacity-25"></i>
                                                <p class="text-muted mt-2 mb-0">{{ __('Aucune opération enregistrée.') }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-3 border-top">
                    {{ $caisses->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Caisse Modal --}}
    <div wire:ignore.self class="modal fade" id="caisseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">
                        {{ $type_operation === 'entree' ? __('Enregistrer une Entrée') : __('Enregistrer une Sortie') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="store">
                    <div class="modal-body pb-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Type d\'opération') }}</label>
                                <div class="d-flex gap-3 mt-1">
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" value="entree"
                                            wire:model.live="type_operation" id="typeEntree">
                                        <label class="form-check-label text-success fw-bold" for="typeEntree">
                                            <i class="bx bx-trending-up me-1"></i> {{ __('Entrée') }}
                                        </label>
                                    </div>
                                    <div class="form-check custom-radio">
                                        <input class="form-check-input" type="radio" value="sortie"
                                            wire:model.live="type_operation" id="typeSortie">
                                        <label class="form-check-label text-danger fw-bold" for="typeSortie">
                                            <i class="bx bx-trending-down me-1"></i> {{ __('Sortie') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Montant (FC)') }}</label>
                                <div class="input-group input-group-merge">
                                    <input type="number" step="any"
                                        class="form-control @error('montant') is-invalid @enderror" wire:model="montant"
                                        placeholder="0.00">
                                    <span class="input-group-text fw-bold">FC</span>
                                </div>
                                @error('montant') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">{{ __('Motif / Description') }}</label>
                                <textarea class="form-control @error('motif') is-invalid @enderror" wire:model="motif"
                                    rows="3"
                                    placeholder="{{ __('Ex: Paiement facture électricité, Versement banque...') }}"></textarea>
                                @error('motif') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-4">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit"
                            class="btn {{ $type_operation === 'entree' ? 'btn-success' : 'btn-danger' }} px-4">
                            <i class="bx bx-check-circle me-1"></i> {{ __('Valider l\'opération') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .extra-small {
            font-size: 0.7rem;
        }

        .italic {
            font-style: italic;
        }

        .badge.bg-label-success {
            background-color: #e8fadf !important;
            color: #71dd37 !important;
        }

        .badge.bg-label-danger {
            background-color: #ffe5e5 !important;
            color: #ff3e1d !important;
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('closeModal', (data) => {
                console.log('Closing modal:', data[0].id);
                const modalElement = document.getElementById(data[0].id);
                if (modalElement) {
                    const modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalInstance.hide();
                }
            });
        });
    </script>
</div>