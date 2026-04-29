<?php

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string|min:4')]
    public string $password = '';

    public bool $remember = false;

    public bool $showPassword = false;

    /**
     * Messages personnalisés de validation
     */
    protected function messages(): array
    {
        return [
            'email.required' => __('login.required_email'),
            'email.email' => __('login.invalid_email_format'),
            'password.required' => __('login.required_password'),
            'password.min' => __('login.too_short_password'),
        ];
    }

    public function togglePasswordVisibility()
    {
        $this->showPassword = !$this->showPassword;
    }

    /**
     * Tente de connecter l'utilisateur
     */
    public function login(): void
    {
        try {
            $this->validate();

            $this->ensureIsNotRateLimited();

            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                RateLimiter::hit($this->throttleKey());

                notyf()->error(__('auth.failed'));
                return;
            }

            // Succès de connexion
            RateLimiter::clear($this->throttleKey());
            Session::regenerate();

            notyf()->success(__('login.success_message'));

            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        } catch (ValidationException $e) {
            // Gestion des erreurs de validation
            $errors = collect($e->errors())->flatten()->toArray();
            notyf()->error(implode("\n", $errors));
            throw $e;
        }
    }

    /**
     * Vérifie le nombre de tentatives de connexion
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        $message = __('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        notyf()->warning($message);

        throw ValidationException::withMessages(['email' => $message]);
    }

    /**
     * Clé unique de limitation par IP + email
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }
};
?>

@section('title', __('login.login_page_title'))

@section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

<div>
    <ul class="navbar-nav flex-row align-items-center ms-md-auto">
        @livewire('language-switcher')
    </ul>
    <x-auth-header :title="__('login.welcome', ['app' => company()?->name ?? config('app.name')])" :description="__('login.login_description')" />

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-info mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="mb-6">
        <div class="mb-6">
            <label for="email" class="form-label">{{ __('login.email') }}</label>
            <input wire:model="email" type="email" class="form-control @error('email') is-invalid @enderror"
                id="email" required autofocus autocomplete="email"
                placeholder="{{ __('login.email_placeholder') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-6 form-password-toggle">
            <div class="d-flex justify-content-between">
                <label for="password" class="form-label">{{ __('login.password') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate>
                        <span>{{ __('login.forgot_password') }}</span>
                    </a>
                @endif
            </div>

            <div class="input-group input-group-merge">
                <input wire:model="password"
                    type="{{ $showPassword ? 'text' : 'password' }}"
                    class="form-control @error('password') is-invalid @enderror"
                    id="password"
                    required
                    autocomplete="current-password"
                    placeholder="{{ __('login.password_placeholder') }}">

                <span class="input-group-text cursor-pointer" wire:click="togglePasswordVisibility">
                    @if ($showPassword)
                        <i class="bx bx-show"></i>
                    @else
                        <i class="bx bx-hide"></i>
                    @endif
                </span>

                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-8">
            <div class="d-flex justify-content-between mt-8">
                <div class="form-check mb-0 ms-2">
                    <input wire:model="remember" type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">
                        {{ __('login.remember_me') }}
                    </label>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <button type="submit" class="btn btn-primary d-grid w-100" wire:loading.attr="disabled"
                wire:target="login">
                <span wire:loading wire:target="login" class="spinner-border spinner-border-sm me-2"
                    role="status"></span>
                <span wire:loading.remove wire:target="login">
                    {{ __('login.login') }}
                </span>
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <!-- <p class="text-center">
            <span>{{ __('login.new_here') }}</span>
            <a href="{{ route('register') }}" wire:navigate>
                <span>{{ __('login.register') }}</span>
            </a>
        </p> -->
    @endif
</div>