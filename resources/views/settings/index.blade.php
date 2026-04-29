<x-layouts.app>
    <x-slot:title>
        {{ __('company.company_settings') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('company.company_settings') }}</h5>
        </div>
        <div class="card-body">
            @livewire('settings.company-settings-manager')
        </div>
    </div>
</x-layouts.app>