<div>
    <div class="row mb-3">
        <div class="col-12 col-md-3 mb-2">
            <div class="input-group">
              @if (Auth::user()->role_id == 1)
                <select wire:model.lazy="store_id" class="form-select">
                    <option value="">{{ __('stock_report.all_stores_option') }}</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                    @endforeach
                </select>
              @endif
            </div>
        </div>
        <div class="col-12 col-md-6 mb-2">
            <input type="text" wire:model.live="search" class="form-control" placeholder="{{ __('stock_report.search_product') }}">
        </div>
        <div class="col-12 col-md-3 mb-2">
          <button wire:click="exportPDF" class="btn btn-danger" wire:target="exportPdf" wire:loading.attr="disabled">
              <span wire:loading class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i class="bx bx-file"></i> {{ __('stock_report.export_pdf') }}
          </button>
        </div>
    </div>

    <div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>{{ __('stock_report.product') }}</th>
                <th>{{ __('stock_report.category') }}</th>
                <th>{{ __('stock_report.store') }}</th>
                <th>{{ __('stock_report.current_stock') }}</th>
                <th>{{ __('stock_report.purchase_value') }}</th>
                <th>{{ __('stock_report.sale_value') }}</th>
                <th>{{ __('stock_report.expected_profit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $prod)
                @php
                    $stock = $store_id
                        ? ($prod->stores->first()->pivot->quantity ?? 0)
                        : $prod->stores->sum('pivot.quantity');
                    $valeurAchat = $stock * $prod->purchase_price;
                    $valeurVente = $stock * $prod->sale_price;
                    $benefice = $valeurVente - $valeurAchat;
                @endphp
                <tr>
                    <td>{{ $prod->name }}</td>
                    <td>{{ $prod->category->name ?? '-' }}</td>
                    <td>
                        @if($store_id)
                            {{ $prod->stores->first()->name ?? '-' }}
                        @else
                            {{ __('stock_report.all_stores') }}
                        @endif
                    </td>
                    <td>{{ $stock }}</td>
                    <td>{{ number_format($valeurAchat, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($valeurVente, 2, ',', ' ') }} {{ company()?->devise }}</td>
                    <td>{{ number_format($benefice, 2, ',', ' ') }} {{ company()?->devise }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="d-flex justify-content-center mt-2">
        {{ $products->links() }}
    </div>
</div>