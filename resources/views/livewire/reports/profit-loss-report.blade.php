<div>
    <div class="d-flex justify-content-between mb-3">
        <div class="d-flex gap-2">
            <input type="date" wire:model.live="start_date" class="form-control">
            <input type="date" wire:model.live="end_date" class="form-control">
        </div>
        <button wire:click="exportPdf" class="btn btn-danger" wire:target="exportPdf" wire:loading.attr="disabled">
          <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
          <i class="ti ti-file-type-pdf"></i> {{ __('Exporter PDF') }}
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Rapport Profits & Pertes') }}</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <tr>
                    <th>{{ __('Total Ventes') }}</th>
                    <td>{{ number_format($total_sales, 2, ',', ' ') }} {{ company()->devise }}</td>
                </tr>
                <tr>
                    <th>{{ __('Total Achats') }}</th>
                    <td>{{ number_format($total_purchases, 2, ',', ' ') }} {{ company()->devise }}</td>
                </tr>
                <tr>
                    <th>{{ __('Total Dépenses') }}</th>
                    <td>{{ number_format($total_expenses, 2, ',', ' ') }} {{ company()->devise }}</td>
                </tr>
                <tr class="table-info">
                    <th>{{ __('Profit Brut') }}</th>
                    <td>{{ number_format($profit_brut, 2, ',', ' ') }} {{ company()->devise }}</td>
                </tr>
                <tr class="table-success">
                    <th>{{ __('Profit Net') }}</th>
                    <td>{{ number_format($profit_net, 2, ',', ' ') }} {{ company()->devise }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>
