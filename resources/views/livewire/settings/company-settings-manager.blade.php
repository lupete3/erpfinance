<div>
    <!-- Navigation par Onglets -->
    <ul class="nav nav-tabs mb-4" id="companyTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($activeTab === 'settings') active @endif" id="settings-tab"
                wire:click.prevent="$set('activeTab', 'settings')" type="button" role="tab" aria-controls="settings"
                aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}">
                ⚙️ {{ __('company.settings_tab') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($activeTab === 'history') active @endif" id="history-tab"
                wire:click.prevent="$set('activeTab', 'history')" type="button" role="tab" aria-controls="history"
                aria-selected="{{ $activeTab === 'history' ? 'true' : 'false' }}">
                @if (Auth::user()->tenant_id)
                    📜 {{ __('company.history_tab') }}
                @endif
            </button>
        </li>
    </ul>
    <hr>

    <!-- Contenu des Onglets -->
    <div class="tab-content" id="companyTabsContent">

        <!-- Onglet 1: Paramètres de l'entreprise (Formulaire) -->
        <div class="tab-pane fade @if ($activeTab === 'settings') show active @endif" id="settings" role="tabpanel"
            aria-labelledby="settings-tab">
            <h4 class="mb-3">{{ __('company.company_settings') }}</h4>
            <form wire:submit.prevent="save" enctype="multipart/form-data">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('company.company_name') }}</label>
                        <input type="text" wire:model="name" class="form-control" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('company.email') }}</label>
                        <input type="email" wire:model="email" class="form-control">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('company.phone') }}</label>
                        <input type="text" wire:model="phone" class="form-control">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('company.address') }}</label>
                        <textarea wire:model="address" class="form-control"></textarea>
                        @error('address')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('company.rccm') }}</label>
                        <input type="text" wire:model="rccm" class="form-control">
                        @error('rccm')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('company.id_nat') }}</label>
                        <input type="text" wire:model="id_nat" class="form-control">
                        @error('id_nat')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('company.currency') }}</label>
                        <input type="text" wire:model="devise" required class="form-control">
                        @error('devise')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('company.logo') }}</label>
                    <input type="file" wire:model="new_logo" class="form-control">
                    @if ($logo)
                        <div class="mt-2">
                            <img src="{{ asset($company->logo) }}" alt="{{ __('company.logo') }}" width="120"
                                class="img-thumbnail rounded-md">
                        </div>
                    @endif
                    @error('new_logo')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                    💾 {{ __('company.save') }}
                </button>
            </form>
        </div>

        <!-- Onglet 2: Historique des Souscriptions (Tableau) -->
        @if (Auth::user()->tenant_id)


            <div class="tab-pane fade @if ($activeTab === 'history') show active @endif" id="history"
                role="tabpanel" aria-labelledby="history-tab">
                <h4 class="mb-3">{{ __('company.subscriptions_history') }}</h4>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="">
                            <tr>
                                <th>{{ __('company.plan') }}</th>
                                <th>{{ __('company.duration') }}</th>
                                <th>{{ __('company.start_date') }}</th>
                                <th>{{ __('company.end_date') }}</th>
                                <th>{{ __('company.max_stores') }}</th>
                                <th>{{ __('company.max_users') }}</th>
                                <th>{{ __('company.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $sub)
                                <tr>
                                    <td>
                                        <span class="fw-bold">{{ $sub->plan?->name }}</span><br>
                                        <small class="text-muted">{{ number_format($sub->amount, 2) }} $</small>
                                    </td>
                                    <td>{{ $sub->plan?->duration_days >= 10000 ? __('company.unlimited') : $sub->plan?->duration_days . ' ' . __('company.days') }}
                                    </td>
                                    <td>{{ $sub->start_date }}</td>
                                    <td>
                                        {{ $sub->plan?->duration_days >= 10000 ? __('company.unlimited') : $sub->end_date }}
                                        @if (now()->diffInDays($sub->end_date, false) <= 7 && now()->lt($sub->end_date))
                                            <span class="badge bg-warning ms-2">⚠️
                                                {{ __('company.expiring_soon') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $sub->plan?->max_stores ?? __('company.unlimited') }}</td>
                                    <td>{{ $sub->plan?->max_users ?? __('company.unlimited') }}</td>
                                    <td>
                                        @if ($sub->end_date >= now())
                                            <span class="badge bg-success">{{ __('company.active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('company.expired') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        {{ __('company.no_subscriptions') }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $subscriptions->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
