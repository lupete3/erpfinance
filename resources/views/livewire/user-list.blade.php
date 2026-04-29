<div>
    {{-- Barre de recherche et bouton d'ajout --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="{{ __('users.search_placeholder') }}" wire:model.live.debounce.300ms="search">
        </div>
        <button class="btn btn-primary" wire:click="create" data-bs-toggle="modal" data-bs-target="#userModal">
            <i class="bx bx-plus me-1"></i> {{ __('users.add_user') }}
        </button>
    </div>

    {{-- Tableau des utilisateurs --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('users.id') }}</th>
                    <th>{{ __('users.name') }}</th>
                    <th>{{ __('users.email') }}</th>
                    <th>{{ __('users.role') }}</th>
                    <th>{{ __('users.actions') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($users as $user)
                    <tr wire:key="{{ $user->id }}">
                        <td>{{ $user->id }}</td>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-label-info">{{ $user->role->name ?? 'N/A' }}</span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#" wire:click="edit({{ $user->id }})" data-bs-toggle="modal" data-bs-target="#userModal">
                                        <i class="bx bx-edit-alt me-1"></i> {{ __('users.edit') }}
                                    </a>
                                    <a class="dropdown-item" href="#" wire:click="confirmDelete({{ $user->id }})">
                                        <i class="bx bx-trash me-1"></i> {{ __('users.delete') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">{{ __('users.no_user_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->links() }}
    </div>

    {{-- Modal Création/Édition utilisateur --}}
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEditMode ? __('users.edit_user') : __('users.create_user') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('users.close') }}"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('users.name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('users.email') }}</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('users.role') }}</label>
                            <select class="form-select @error('role_id') is-invalid @enderror" wire:model="role_id">
                                <option value="">{{ __('users.select_role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            @error('role_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label">{{ __('users.password') }}</label>
                            <div class="input-group input-group-merge">
                                <input 
                                    type="{{ $showPassword ? 'text' : 'password' }}" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    wire:model="password"
                                >
                                <span 
                                    class="input-group-text cursor-pointer" 
                                    wire:click="$toggle('showPassword')"
                                >
                                    <i class="bx {{ $showPassword ? 'bx-show' : 'bx-hide' }}"></i>
                                </span>
                            </div>
                            @if($isEditMode)
                                <small class="form-text text-muted">{{ __('users.leave_blank_to_keep') }}</small>
                            @endif
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-password-toggle">
                            <label class="form-label">{{ __('users.password_confirmation') }}</label>
                            <div class="input-group input-group-merge">
                                <input 
                                    type="{{ $showPasswordConfirmation ? 'text' : 'password' }}" 
                                    class="form-control" 
                                    wire:model="password_confirmation"
                                >
                                <span 
                                    class="input-group-text cursor-pointer" 
                                    wire:click="$toggle('showPasswordConfirmation')"
                                >
                                    <i class="bx {{ $showPasswordConfirmation ? 'bx-show' : 'bx-hide' }}"></i>
                                </span>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('users.close') }}</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                            <i class="bx bx-check me-1"></i>
                          {{ $isEditMode ? __('users.save_changes') : __('users.create') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>