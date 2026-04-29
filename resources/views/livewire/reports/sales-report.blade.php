<div>
    <div class="card mb-3">
        <div class="card-body row g-2">
          @if (Auth::user()->role_id == 1)
            <div class="col-md-2">
                <select class="form-select" wire:model.live="store_id">
                    <option value="">{{ __('sale_report.all_stores') }}</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
          @endif
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="client_id">
                    <option value="">{{ __('sale_report.all_clients') }}</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="status">
                    <option value="">{{ __('sale_report.all_statuses') }}</option>
                    <option value="validated">{{ __('sale_report.validated') }}</option>
                    <option value="pending">{{ __('sale_report.pending') }}</option>
                    <option value="returned">{{ __('sale_report.returned') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="date_type">
                    <option value="all">{{ __('sale_report.all_periods') }}</option>
                    <option value="today">{{ __('sale_report.today') }}</option>
                    <option value="month">{{ __('sale_report.this_month') }}</option>
                    <option value="year">{{ __('sale_report.this_year') }}</option>
                    <option value="range">{{ __('sale_report.range') }}</option>
                </select>
            </div>
            @if($date_type == 'range')
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model="start_date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model="end_date">
                </div>
            @endif
            <div class="col-md-2">
                <button class="btn btn-danger w-100" wire:click="exportPdf" wire:target="exportPdf" wire:loading.attr="disabled">
                  <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i class="bx bx-download"></i> {{ __('sale_report.export_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row text-center mb-3">
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('sale_report.total_sales_number') }}</strong>
                <h5>{{ $total_sales }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('sale_report.total_sales') }}</strong>
                <h5>{{ number_format($total_amount,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('sale_report.total_paid') }}</strong>
                <h5 class="text-success">{{ number_format($total_paid,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('sale_report.total_due') }}</strong>
                <h5 class="text-danger">{{ number_format($total_due,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('sale_report.client') }}</th>
                        <th>{{ __('sale_report.total_amount') }}</th>
                        <th>{{ __('sale_report.paid') }}</th>
                        <th>{{ __('sale_report.remaining') }}</th>
                        <th>{{ __('sale_report.store') }}</th>
                        <th>{{ __('sale_report.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->client?->name ?? __('sale_report.free_client') }}</td>
                            <td>{{ number_format($sale->total_amount,2) }}</td>
                            <td class="text-success">{{ number_format($sale->total_paid,2) }}</td>
                            <td class="text-danger">{{ number_format($sale->total_amount - $sale->total_paid,2) }}</td>
                            <td>{{ $sale->store?->name ?? __('N/A') }}</td>
                            <td>{{ $sale->sale_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('sale_report.no_sales_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2">
            {{ $sales->links() }}
        </div>
    </div>
</div>