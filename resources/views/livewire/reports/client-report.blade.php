<div>
    <div class="row mb-3">
        <div class="col-md-3 mb-2">
            <label>{{ __('client_report.period') }} :</label>
            <input type="date" wire:model.lazy="filter_start" class="form-control d-inline w-auto">
        </div>
        <div class="col-md-3 mb-2">
            <label>{{ __('client_report.to') }}</label>
            <input type="date" wire:model.lazy="filter_end" class="form-control d-inline w-auto">
        </div>
    </div>
    <hr>

    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <div class="card text-bg-info">
                <div class="card-body">
                    <h5>{{ __('client_report.total_invoiced') }}</h5>
                    <h3>{{ number_format($total_factures, 2) }} {{ company()?->devise }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success">
                <div class="card-body">
                    <h5>{{ __('client_report.total_paid') }}</h5>
                    <h3>{{ number_format($total_regles, 2) }} {{ company()?->devise }}</h3>
                </div>
            </div>
        </div>
    </div>

    <h5>{{ __('client_report.top_clients') }}</h5>
    <ul class="list-group mb-4">
        @foreach($top_clients as $tc)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $tc->client->name ?? __('client_report.deleted_client') }}</span>
                <span><b>{{ number_format($tc->total_achats, 2) }} {{ company()?->devise }}</b></span>
            </li>
        @endforeach
    </ul>
    <hr>
    <h5>{{ __('client_report.client_list') }}</h5>
    <div class="table-responsive">
      <table class="table table-bordered">
          <thead class="table-light">
              <tr>
                  <th>{{ __('client_report.name') }}</th>
                  <th>{{ __('client_report.phone') }}</th>
                  <th>{{ __('client_report.total_purchases') }}</th>
                  <th>{{ __('client_report.total_paid_col') }}</th>
                  <th>{{ __('client_report.balance_due') }}</th>
              </tr>
          </thead>
          <tbody>
              @forelse($clients as $client)
                  @php
                      $total = $client->sales->sum('total_amount');
                      $paye = $client->sales->sum('total_paid');
                  @endphp
                  <tr>
                      <td>{{ $client->name }}</td>
                      <td>{{ $client->phone }}</td>
                      <td>{{ number_format($total, 2) }} {{ company()?->devise }}</td>
                      <td>{{ number_format($paye, 2) }} {{ company()?->devise }}</td>
                      <td class="{{ $total > $paye ? 'text-danger fw-bold' : 'text-success' }}">
                          {{ number_format($total - $paye, 2) }} {{ company()?->devise }}
                      </td>
                  </tr>
              @empty
                  <tr>
                      <td colspan="5" class="text-center">{{ __('client_report.no_clients_found') }}</td>
                  </tr>
              @endforelse
          </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-center mt-2">
        {{ $clients->links() }}
    </div>
</div>