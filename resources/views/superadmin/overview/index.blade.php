<x-layouts.app>
    <x-slot:title>
        {{ __('Statistique Clients') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Statistique Clients') }}</h5>
        </div>
        <div class="card-body">
            @livewire('super-admin.super-admin-overview')
        </div>
    </div>
</x-layouts.app>
