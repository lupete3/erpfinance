<div>
    <div class="card mb-3">
        <div class="card-body row">
            <div class="col-md-6 mb-2">
              <input type="text" wire:model.live="search" class="form-control" placeholder="{{ __('supplier_report.search_supplier') }}">
            </div>

            <div class="col-md-4 mb-2">
                <div class="row">
                  <div class="col-md-6">
                    <input type="date" wire:model.lazy="date_from" class="form-control mb-2" title="{{ __('supplier_report.from') }}">
                  </div>
                  <div class="col-md-6">
                    <input type="date" wire:model.lazy="date_to" class="form-control mb-2" title="{{ __('supplier_report.to') }}">
                  </div>
                </div>
            </div>
            <div class="col-md-2">
                <button wire:click="exportPdf" class="btn btn-danger">
                  <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
                  <i class="bx bxs-file-pdf"></i> {{ __('supplier_report.export_pdf') }}
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('supplier_report.supplier') }}</th>
                        <th>{{ __('supplier_report.contact') }}</th>
                        <th>{{ __('supplier_report.purchase_count') }}</th>
                        <th>{{ __('supplier_report.total_purchases') }}</th>
                        <th>{{ __('supplier_report.total_paid') }}</th>
                        <th>{{ __('supplier_report.balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $supplier)
                        @php
                            $totalAchats = $supplier->purchases->sum('total_amount');
                            $totalPaye = $supplier->purchases->sum('total_paid');
                            $solde = $totalAchats - $totalPaye;
                        @endphp
                        <tr>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone }}</td>
                            <td>{{ $supplier->purchases->count() }}</td>
                            <td>{{ number_format($totalAchats, 2, ',', ' ') }} {{ company()?->devise }}</td>
                            <td>{{ number_format($totalPaye, 2, ',', ' ') }} {{ company()?->devise }}</td>
                            <td class="{{ $solde > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                {{ number_format($solde, 2, ',', ' ') }} {{ company()?->devise }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ __('supplier_report.no_results_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $suppliers->links() }}
    </div>
</div>