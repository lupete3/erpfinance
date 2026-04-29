<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $name = '';
    public string $email = '';

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id)
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }
};
?>

@section('title', __('profile.profile'))

<section>
    @include('partials.settings-heading')

    <x-settings.layout>
        <div class="row">
            <div class="col-md-4">
                {{-- Profile Info Card --}}
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="user-avatar-section">
                            <div class="d-flex align-items-center flex-column">
                                <div class="avatar mb-3 bg-label-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    @php
                                        $initials = collect(explode(' ', Auth::user()->name))
                                            ->filter()
                                            ->map(fn($word) => strtoupper(substr($word, 0, 1)))
                                            ->take(2)
                                            ->implode('');
                                    @endphp

                                    <span class="display-6 fw-bold">
                                        <i class="menu-icon tf-icons bx bx-user-circle"></i>
                                    </span>
                                </div>
                                <div class="user-info text-center">
                                    <h4 class="mb-2">{{ Auth::user()->name }}</h4>
                                    <span class="badge bg-label-secondary text-capitalize">{{ str_replace('_', ' ', Auth::user()->role) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="info-container mt-4 pt-2">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <span class="fw-bold me-2">{{ __('profile.email') }}:</span>
                                    <span>{{ Auth::user()->email }}</span>
                                </li>
                                <li class="mb-3">
                                    <span class="fw-bold me-2">{{ __('Statut') }}:</span>
                                    @if(auth()->user()->hasVerifiedEmail())
                                        <span class="badge bg-label-success">{{ __('Vérifié') }}</span>
                                    @else
                                        <span class="badge bg-label-warning">{{ __('Non Vérifié') }}</span>
                                    @endif
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                {{-- Edit Profile Card --}}
                <div class="card mb-4">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">{{ __('profile.update_info') }}</h5>
                    </div>
                    <div class="card-body pt-4">
                        <form wire:submit="updateProfileInformation">
                            <div class="row g-3">
                                <div class="col-md-12 mb-3">
                                    <label for="name" class="form-label">{{ __('profile.name') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input type="text" id="name" wire:model="name" class="form-control" placeholder="{{ __('profile.name_placeholder') }}" required autofocus autocomplete="name">
                                    </div>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="email" class="form-label">{{ __('profile.email') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                        <input type="email" id="email" wire:model="email" class="form-control" placeholder="{{ __('profile.email_placeholder') }}" required autocomplete="email">
                                    </div>
                                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                                    @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                                        <div class="mt-3">
                                            <p class="text-warning small mb-0">
                                                {{ __('profile.email_not_verified') }}
                                                <a href="#" wire:click.prevent="resendVerificationNotification" class="text-info fw-bold">{{ __('profile.resend_verification') }}</a>
                                            </p>

                                            @if (session('status') === 'verification-link-sent')
                                                <p class="mt-2 text-success small fw-bold">
                                                    {{ __('profile.verification_link_sent') }}
                                                </p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="bx bx-save me-1"></i> {{ __('profile.save_changes') }}
                                </button>
                                <x-action-message class="text-success fw-bold" on="profile-updated">
                                    <i class="bx bx-check-double me-1"></i> {{ __('profile.saved') }}
                                </x-action-message>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>