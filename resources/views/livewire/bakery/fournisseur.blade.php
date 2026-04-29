<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Fournisseurs') }}
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
            <h5 class="card-title mb-0">{{ __('Liste des Fournisseurs') }}</h5>
            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge" style="min-width: 200px;">
                    <span class="input-group-text"><i class="bx bx-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('Rechercher...') }}"
                        wire:model.live.debounce.300ms="search">
                </div>
                <button class="btn btn-primary text-nowrap" wire:click="create">
                    <i class="bx bx-plus me-1"></i> {{ __('Nouveau Fournisseur') }}
                </button>
            </div>
        </div>
        <div class=" card-datatable table-responsive">
            <table class="table border-top">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Nom') }}</th>
                        <th>{{ __('Téléphone') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = ($fournisseurs->currentPage() - 1) * $fournisseurs->perPage() + 1; @endphp
                    @forelse($fournisseurs as $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td><strong>{{ $item->nom }}</strong></td>
                            <td>{{ $item->telephone ?? '-' }}</td>
                            <td>{{ $item->email ?? '-' }}</td>
                            <td class="text-center">
                                @if(!in_array(Auth::user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie']))
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item" wire:click="edit({{ $item->id }})">
                                                <i class="bx bx-edit-alt me-1"></i> {{ __('Modifier') }}
                                            </button>
                                            <button class="dropdown-item text-danger" wire:click="delete({{ $item->id }})"
                                                onclick="confirm('Confirmer la suppression ?') || event.stopImmediatePropagation()">
                                                <i class="bx bx-trash me-1"></i> {{ __('Supprimer') }}
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-label-secondary">{{ __('Consultation seule') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                {{ __('Aucun fournisseur trouvé.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer px-3 py-2 border-top">
            {{ $fournisseurs->links() }}
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div wire:ignore.self class="modal fade" id="fournisseurModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-start">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">
                        {{ $isEditMode ? __('Modifier le Fournisseur') : __('Nouveau Fournisseur') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="{{ $isEditMode ? 'update' : 'store' }}">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Nom / Raison Sociale') }}</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                    wire:model="nom" placeholder="Ex: Moulin d'Or">
                                @error('nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Téléphone') }}</label>
                                <input type="text" class="form-control @error('telephone') is-invalid @enderror"
                                    wire:model="telephone" placeholder="0...">
                                @error('telephone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="contact@...">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit"
                            class="btn btn-primary">{{ $isEditMode ? __('Enregistrer') : __('Ajouter') }}</button>
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
</div>