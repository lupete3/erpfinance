<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} /</span> {{ __('Paramètres & Administration') }}
    </h4>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="nav-align-top mb-4">
        <div class="d-flex overflow-x-auto pb-2 scrollbar-hidden">
            <ul class="nav nav-tabs flex-nowrap" role="tablist">
                <li class="nav-item text-nowrap">
                    <button type="button" class="nav-link @if($activeTab == 'sites') active @endif" role="tab"
                        wire:click="$set('activeTab', 'sites')">
                        <i class="bx bx-map-pin me-1"></i> {{ __('Points de Vente (Sites)') }}
                    </button>
                </li>
                <li class="nav-item text-nowrap">
                    <button type="button" class="nav-link @if($activeTab == 'users') active @endif" role="tab"
                        wire:click="$set('activeTab', 'users')">
                        <i class="bx bx-users me-1"></i> {{ __('Gestion Utilisateurs') }}
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content border-top-0 shadow-none p-0 bg-transparent pt-3">
            {{-- Sites Tab --}}
            <div class="tab-pane fade @if($activeTab == 'sites') show active @endif">
                <div class="card">
                    <div
                        class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">{{ __('Liste des Points de Vente') }}</h5>
                        <button class="btn btn-primary text-nowrap" wire:click="openSiteModal()">
                            <i class="bx bx-plus me-1"></i> {{ __('Ajouter un Site') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Nom du Site') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sites as $s)
                                    <tr>
                                        <td>#{{ $s->id }}</td>
                                        <td><strong>{{ $s->nom }}</strong></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-icon btn-label-warning me-1"
                                                wire:click="syncSiteStock({{ $s->id }})" wire:loading.attr="disabled" title="{{ __('Synchroniser le Stock') }}">
                                                <i class="bx bx-sync"></i>
                                            </button>
                                            <button class="btn btn-sm btn-icon btn-label-primary"
                                                wire:click="openSiteModal({{ $s->id }})">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Users Tab --}}
            <div class="tab-pane fade @if($activeTab == 'users') show active @endif">
                <div class="card">
                    <div
                        class="card-header border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                        <h5 class="mb-0">{{ __('Utilisateurs Boulangerie') }}</h5>
                        <button class="btn btn-primary text-nowrap" wire:click="openUserModal()">
                            <i class="bx bx-user-plus me-1"></i> {{ __('Ajouter un Utilisateur') }}
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Nom') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Rôle') }}</th>
                                    <th>{{ __('Site Affecté') }}</th>
                                    <th class="text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><span class="badge bg-label-secondary text-capitalize">{{ $user->role }}</span>
                                        </td>
                                        <td><span class="badge bg-label-info">{{ $user->site->nom ?? 'N/A' }}</span></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-icon btn-label-primary"
                                                wire:click="openUserModal({{ $user->id }})">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer py-2">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Site Modal --}}
    <div wire:ignore.self class="modal fade" id="siteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">{{ $editingSiteId ? __('Modifier le Site') : __('Ajouter un Site') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeSite">
                    <div class="modal-body">
                        <div class="col-12">
                            <label class="form-label">{{ __('Nom du Point de Vente') }}</label>
                            <input type="text" class="form-control @error('site_nom') is-invalid @enderror"
                                wire:model="site_nom">
                            @error('site_nom') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Enregistrer') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- User Modal --}}
    <div wire:ignore.self class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title">
                        {{ $editingUserId ? __('Modifier l\'Utilisateur') : __('Ajouter un Utilisateur') }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="storeUser">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">{{ __('Nom complet') }}</label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror"
                                    wire:model="user_name">
                                @error('user_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Email') }}</label>
                                <input type="email" class="form-control @error('user_email') is-invalid @enderror"
                                    wire:model="user_email">
                                @error('user_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Mot de passe') }} @if($editingUserId) <small
                                class="text-muted">({{ __('Optionnel') }})</small> @endif</label>
                                <input type="password" class="form-control @error('user_password') is-invalid @enderror"
                                    wire:model="user_password">
                                @error('user_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Rôle') }}</label>
                                <select class="form-select @error('user_role') is-invalid @enderror"
                                    wire:model="user_role">
                                    <option value="admin">{{ __('Administrateur') }}</option>
                                    <option value="geran_depot_magasin">{{ __('Gérant Dépôt Magasin (MP)') }}</option>
                                    <option value="geran_depot_usine">{{ __('Gérant Dépôt Usine (Production)') }}
                                    </option>
                                    <option value="geran_depot_boulangerie">
                                        {{ __('Gérant Dépôt Boulangerie (Ventes)') }}
                                    </option>
                                </select>
                                @error('user_role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Affectation Site') }}</label>
                                <select class="form-select @error('user_site_id') is-invalid @enderror"
                                    wire:model="user_site_id">
                                    <option value="">-- {{ __('Choisir Site') }} --</option>
                                    @foreach($sites as $site)
                                        <option value="{{ $site->id }}">{{ $site->nom }}</option>
                                    @endforeach
                                </select>
                                @error('user_site_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">{{ __('Annuler') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Enregistrer') }}</button>
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