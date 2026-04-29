<x-layouts.app>
    <x-slot:title>
        {{ __('cash_report.index_title') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('cash_report.title') }}</h5>
        </div>
        <div class="card-body">
            @livewire('reports.cash-overview-report')
        </div>
    </div>
</x-layouts.app>
