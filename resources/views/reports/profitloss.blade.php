<x-layouts.app>
    <x-slot:title>
        {{ __('Rapport de profits et pertes') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('Rapport de profits et pertes') }}</h5>
        </div>
        <div class="card-body">
            @livewire('reports.profit-loss-report')
        </div>
    </div>
</x-layouts.app>
