<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                // 'current_password' sera traduit par le système de validation de Laravel.
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');
            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
};
?>

@section('title', __('password.title'))

<section>
    @include('partials.settings-heading')

    <x-settings.layout>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom">

                                           <h5 class="card-title mb-0">{{ __('password.subheading') }}</h5>
                    </div>
                    <div class="card-body pt-4">
                        <form wire:submit="updatePassword">
                            <div class="mb-4">

                                                   <label for="current_password" class="form-label">{{ __('password.current_password') }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-lock-alt"></i></span>
                                    <input type="password" id="current_password" wire:model="current_password" class="form-control" required autocomplete="current-password" placeholder="············" />
                                </div>

                                                   @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">{{ __('password.new_password') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-key"></i></span>
                                        <input type="password" id="password" wire:model="password" class="form-control" required autocomplete="new-password" placeholder="············" />
                                    </div>
                                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">{{ __('password.confirm_password') }}</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-key"></i></span>
                                        <input type="password" id="password_confirmation" wire:model="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="············" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary me-3">
                                    <i class="bx bx-save me-1"></i> {{ __('password.save') }}
                                </button>
                                <x-action-message class="text-success fw-bold" on="password-updated">
                                    <i class="bx bx-check-double me-1"></i> {{ __('password.saved') }}
                                </x-action-message>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-settings.layout>
</section>