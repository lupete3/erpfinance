<div>
    <div class="card mb-3">
        <div class="card-body row g-2">
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="supplier_id">
                    <option value="">{{ __('purchase_report.all_suppliers') }}</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="status">
                    <option value="">{{ __('purchase_report.all_status') }}</option>
                    <option value="validated">{{ __('purchase_report.validated') }}</option>
                    <option value="pending">{{ __('purchase_report.pending') }}</option>
                    <option value="returned">{{ __('purchase_report.returned') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="date_type">
                    <option value="all">{{ __('purchase_report.all_periods') }}</option>
                    <option value="today">{{ __('purchase_report.today') }}</option>
                    <option value="month">{{ __('purchase_report.this_month') }}</option>
                    <option value="year">{{ __('purchase_report.this_year') }}</option>
                    <option value="range">{{ __('purchase_report.range') }}</option>
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
                  <i class="bx bx-download"></i> {{ __('purchase_report.export_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row text-center mb-3">
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('purchase_report.purchase_count') }}</strong>
                <h5>{{ $total_purchases }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('purchase_report.total_purchases') }}</strong>
                <h5>{{ number_format($total_amount,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('purchase_report.total_paid') }}</strong>
                <h5 class="text-success">{{ number_format($total_paid,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('purchase_report.supplier_debt') }}</strong>
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
                        <th>{{ __('purchase_report.supplier') }}</th>
                        <th>{{ __('purchase_report.total_amount') }}</th>
                        <th>{{ __('purchase_report.paid') }}</th>
                        <th>{{ __('purchase_report.remaining') }}</th>
                        <th>{{ __('purchase_report.status') }}</th>
                        <th>{{ __('purchase_report.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->supplier?->name }}</td>
                            <td>{{ number_format($purchase->total_amount,2) }}</td>
                            <td class="text-success">{{ number_format($purchase->total_paid,2) }}</td>
                            <td class="text-danger">{{ number_format($purchase->total_amount - $purchase->total_paid,2) }}</td>
                            <td>
                                <span class="badge
                                    @if($purchase->status=='validated') bg-success
                                    @elseif($purchase->status=='pending') bg-warning
                                    @else bg-danger @endif">
                                    {{ __(ucfirst($purchase->status)) }}
                                </span>
                            </td>
                            <td>{{ $purchase->purchase_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('purchase_report.no_purchases_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2">
            {{ $purchases->links() }}
        </div>
    </div>
</div>