<div>
    {{-- Filtres --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <label for="start_date" class="form-label">{{ __('Date de début') }}</label>
            <input type="date" class="form-control" wire:model.live="start_date">
        </div>
        <div class="col-md-3">
            <label for="end_date" class="form-label">{{ __('Date de fin') }}</label>
            <input type="date" class="form-control" wire:model.live="end_date">
        </div>
    </div>

    {{-- Tableau du rapport des ventes --}}
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>{{ __('ID') }}</th>
                    <th>{{ __('Client') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Montant total') }}</th>
                    <th>{{ __('Payé') }}</th>
                    <th>{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                @forelse ($sales as $sale)
                    <tr wire:key="{{ $sale->id }}">
                        <td>{{ $sale->id }}</td>
                        <td>{{ $sale->client->name ?? __('N/A') }}</td>
                        <td>{{ $sale->sale_date }}</td>
                        <td>{{ number_format($sale->total_amount, 2) }} {{ company()->devise }}</td>
                        <td>{{ number_format($sale->total_paid, 2) }} {{ company()->devise }}</td>
                        <td><span class="badge bg-label-primary me-1">{{ $sale->status }}</span></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">{{ __('Aucune vente trouvée pour la période sélectionnée.') }}</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">{{ __('Total :') }}</th>
                    <th>{{ number_format($total_amount, 2) }} {{ company()->devise }}</th>
                    <th>{{ number_format($total_paid, 2) }} {{ company()->devise }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $sales->links() }}
    </div>
</div>
