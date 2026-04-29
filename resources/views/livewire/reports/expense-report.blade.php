<div>
    <div class="card mb-3">
        <div class="card-body row g-2">
          @if (Auth::user()->role_id == 1)
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="store_id">
                    <option value="">{{ __('expense_report.all_stores') }}</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
          @endif
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="category_id">
                    <option value="">{{ __('expense_report.all_categories') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" wire:model.lazy="date_type">
                    <option value="all">{{ __('expense_report.all_periods') }}</option>
                    <option value="today">{{ __('expense_report.today') }}</option>
                    <option value="month">{{ __('expense_report.this_month') }}</option>
                    <option value="year">{{ __('expense_report.this_year') }}</option>
                    <option value="range">{{ __('expense_report.range') }}</option>
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
                <input type="text" class="form-control" wire:model.live="search" placeholder="{{ __('expense_report.search') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-danger w-100" wire:click="exportPdf" wire:target="exportPdf" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                    <i class="bx bx-download"></i> {{ __('expense_report.export_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <div class="row text-center mb-3">
        <div class="col-md-6 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('expense_report.total_expenses_count') }}</strong>
                <h5>{{ $total_expenses }}</h5>
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="card p-2 shadow-sm">
                <strong>{{ __('expense_report.total_amount') }}</strong>
                <h5 class="text-danger">{{ number_format($total_amount,2) }} {{ company()?->devise }}</h5>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('expense_report.id') }}</th>
                        <th>{{ __('expense_report.description') }}</th>
                        <th>{{ __('expense_report.amount') }}</th>
                        <th>{{ __('expense_report.category') }}</th>
                        <th>{{ __('expense_report.store') }}</th>
                        <th>{{ __('expense_report.user') }}</th>
                        <th>{{ __('expense_report.date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                        <tr>
                            <td>{{ $exp->id }}</td>
                            <td>{{ $exp->description }}</td>
                            <td class="text-danger">{{ number_format($exp->amount,2) }} {{ company()?->devise }}</td>
                            <td>{{ $exp->category?->name ?? '-' }}</td>
                            <td>{{ $exp->store?->name ?? '-' }}</td>
                            <td>{{ $exp->user?->name ?? '-' }}</td>
                            <td>{{ $exp->expense_date }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">{{ __('expense_report.no_expense_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center mt-2">
            {{ $expenses->links() }}
        </div>
    </div>
</div>