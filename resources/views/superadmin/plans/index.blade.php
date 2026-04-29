<x-layouts.app>
    <x-slot:title>
        {{ __('Gestions des Plans') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Gestions des Plans') }}</h5>
        </div>
        <div class="card-body">
            @livewire('super-admin.plan-manager')
        </div>
    </div>
</x-layouts.app>
