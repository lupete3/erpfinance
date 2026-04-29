<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $email = '';

    /**
     * Envoyer un lien de réinitialisation du mot de passe à l'adresse email fournie.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('forgot_password.status_sent'));
    }
};
?>

@section('title', __('forgot_password.page_title'))

@section('page-style')
@vite([
    'resources/assets/vendor/scss/pages/page-auth.scss'
])
@endsection

<div>
    <h4 class="mb-1">{{ __('forgot_password.heading') }}</h4>
    <p class="mb-6">{{ __('forgot_password.description') }}</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="sendPasswordResetLink" class="mb-6">
        <div class="mb-6">
            <label for="email" class="form-label">{{ __('forgot_password.email') }}</label>
            <input
                wire:model="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                id="email"
                required
                autofocus
                autocomplete="email"
                placeholder="{{ __('forgot_password.email_placeholder') }}"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary d-grid w-100 mb-6">
            {{ __('forgot_password.send_reset_link') }}
        </button>
    </form>

    <div class="text-center">
        <a href="{{ route('login') }}" class="d-flex justify-content-center" wire:navigate>
            <i class="bx bx-chevron-left scaleX-n1-rtl me-1"></i>
            {{ __('forgot_password.back_to_login') }}
        </a>
    </div>
</div>