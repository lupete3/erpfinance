<div>
    <div class="card mb-3">
        <div class="card-body row g-2">
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="store_id">
                    <option value="">{{ __('cash_report.all_stores') }}</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="date_type">
                    <option value="all">{{ __('cash_report.all_periods') }}</option>
                    <option value="today">{{ __('cash_report.today') }}</option>
                    <option value="month">{{ __('cash_report.this_month') }}</option>
                    <option value="year">{{ __('cash_report.this_year') }}</option>
                    <option value="range">{{ __('cash_report.range') }}</option>
                </select>
            </div>
            @if($date_type == 'range')
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model.lazy="start_date">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" wire:model.lazy="end_date">
                </div>
            @endif
            <div class="col-md-2">
                <input type="text" class="form-control" wire:model.live="search" placeholder="{{ __('cash_report.search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-danger w-100" wire:click="exportPdf" wire:target="exportPdf" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i class="bx bx-download"></i> {{ __('cash_report.export_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row text-center mb-3">
        <div class="col-md-3 mb-2">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-success">{{ __('cash_report.total_in') }}</h6>
                    <h4>{{ number_format($total_in, 2) }} {{ company()?->devise }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="text-danger">{{ __('cash_report.total_out') }}</h6>
                    <h4>{{ number_format($total_out, 2) }} {{ company()?->devise }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-2">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-primary">{{ __('cash_report.net_balance') }}</h6>
                    <h4>{{ number_format($net_balance, 2) }} {{ company()?->devise }}</h4>
                </div>
            </div>
        </div>
        @if($current_balance !== null)
        <div class="col-md-3 mb-2">
            <div class="card border-dark">
                <div class="card-body">
                    <h6 class="text-dark">{{ __('cash_report.current_balance') }}</h6>
                    <h4>{{ number_format($current_balance, 2) }} {{ company()?->devise }}</h4>
                </div>
            </div>
        </div>
        @endif
    </div>


    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('cash_report.id') }}</th>
                        <th>{{ __('cash_report.type') }}</th>
                        <th>{{ __('cash_report.amount') }}</th>
                        <th>{{ __('cash_report.store') }}</th>
                        <th>{{ __('cash_report.user') }}</th>
                        <th>{{ __('cash_report.description') }}</th>
                        <th>{{ __('cash_report.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tr)
                        <tr>
                            <td>{{ $tr->id }}</td>
                            <td>
                                @if($tr->type == 'in')
                                    <span class="badge bg-success">{{ __('cash_report.in') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('cash_report.out') }}</span>
                                @endif
                            </td>
                            <td class="{{ $tr->type == 'in' ? 'text-success' : 'text-danger' }}">
                                {{ number_format($tr->amount,2) }} {{ company()?->devise }}
                            </td>
                            <td>{{ $tr->cashRegister?->store?->name ?? '-' }}</td>
                            <td>{{ $tr->user?->name ?? '-' }}</td>
                            <td>{{ $tr->description }}</td>
                            <td>{{ $tr->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('cash_report.no_transaction_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2">
            {{ $transactions->links() }}
        </div>
    </div>
</div>