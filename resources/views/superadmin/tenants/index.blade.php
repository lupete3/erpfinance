<x-layouts.app>
    <x-slot:title>
        {{ __('Gestions Clients') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Gestions Clients') }}</h5>
        </div>
        <div class="card-body">
            @livewire('super-admin.tenant-manager')
        </div>
    </div>
</x-layouts.app>
