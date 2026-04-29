<x-layouts.app>
    <x-slot:title>
        {{ __('product_report.index_title') }}
    </x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ __('product_report.title') }}</h5>
        </div>
        <div class="card-body">
            @livewire('reports.product-report')
        </div>
    </div>
</x-layouts.app>
