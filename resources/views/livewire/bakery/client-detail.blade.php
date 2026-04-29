<div>
    {{-- Breadcrumbs --}}
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">{{ __('Boulangerie') }} / {{ __('Clients') }} /</span>
        {{ __('Fiche Client') }} : {{ $client->nom }}
    </h4>

    {{-- Summary Cards --}}
    <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <div class="col">
            <div class="card h-100 border-start border-primary border shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md bg-label-primary me-3 flex-shrink-0">
                            <span class="avatar-initial rounded"><i class="bx bx-cart"></i></span>
                        </div>
                        <h5 class="card-title mb-0">{{ __('Total Achats') }}</h5>
                    </div>
                    <h3 class="fw-bold mb-0">{{ number_format($total_achats, 0, ',', ' ') }} <small
                            class="text-muted">CDF</small></h3>
                    <p class="text-muted small mb-0">{{ __('Historique complet ou filtré') }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-start border-success border shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md bg-label-success me-3 flex-shrink-0">
                            <span class="avatar-initial rounded"><i class="bx bx-check-circle"></i></span>
                        </div>
                        <h5 class="card-title mb-0">{{ __('Total Payé') }}</h5>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">{{ number_format($total_paye, 0, ',', ' ') }} <small
                            class="text-muted">CDF</small></h3>
                    <p class="text-muted small mb-0">{{ __('Somme des paiements validés') }}</p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-start border-danger border shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="avatar avatar-md bg-label-danger me-3 flex-shrink-0">
                            <span class="avatar-initial rounded"><i class="bx bx-money"></i></span>
                        </div>
                        <h5 class="card-title mb-0">{{ __('Solde / Dette') }}</h5>
                    </div>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($total_dette, 0, ',', ' ') }} <small
                            class="text-muted">CDF</small></h3>
                    <p class="text-muted small mb-0">{{ __('Reste à payer') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Actions --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">{{ __('De') }}</label>
                    <input type="date" class="form-control" wire:model.live="start_date">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-uppercase fw-bold">{{ __('À') }}</label>
                    <input type="date" class="form-control" wire:model.live="end_date">
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('bakery.clients.export', ['client' => $client->id, 'start_date' => $start_date, 'end_date' => $end_date]) }}"
                        class="btn btn-outline-danger me-2" target="_blank">
                        <i class="bx bxs-file-pdf me-1"></i> {{ __('Exporter en PDF') }}
                    </a>
                    <a href="{{ route('bakery.clients') }}" class="btn btn-primary">
                        <i class="bx bx-arrow-back me-1"></i> {{ __('Retour') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Commands List --}}
        <div class="col-12 col-xl-7">
            <div class="card h-100 shadow-sm">
                <div class="card-header border-bottom bg-light bg-opacity-50">
                    <h5 class="card-title mb-0"><i class="bx bx-list-ul me-2"></i>{{ __('Historique des Achats') }}
                    </h5>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table border-top table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Réf') }}</th>
                                <th>{{ __('Détails Articles') }}</th>
                                <th class="text-end">{{ __('Montant') }}</th>
                                <th class="text-center">{{ __('Statut') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($commands as $cmd)
                                <tr class="border-bottom">
                                    <td class="small py-3">
                                        <div class="fw-bold">{{ $cmd->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $cmd->created_at->format('H:i') }}</div>
                                    </td>
                                    <td><span class="badge bg-label-secondary small">#{{ $cmd->id }}</span></td>
                                    <td>
                                        <ul class="list-unstyled mb-0 small">
                                            @foreach ($cmd->ventes as $vente)
                                                <li><span class="fw-bold">{{ $vente->quantite }}</span>
                                                    {{ $vente->product->designation ?? $vente->designation}} ({{ $vente->prix ?? $vente->prix}})
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-bold">{{ number_format($cmd->montant, 0, ',', ' ') }}</div>
                                        <div class="small text-danger">{{ __('Reste') }}:
                                            {{ number_format($cmd->reste, 0, ',', ' ') }}</div>
                                    </td>
                                    <td class="text-center">
                                        @if ($cmd->reste <= 0)
                                            <span class="badge bg-label-success">{{ __('Payé') }}</span>
                                        @elseif($cmd->paye > 0)
                                            <span class="badge bg-label-warning">{{ __('Partiel') }}</span>
                                        @else
                                            <span class="badge bg-label-danger">{{ __('Dette') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        {{ __('Aucun achat trouvé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer px-3 py-2 border-top">
                    {{ $commands->links() }}
                </div>
            </div>
        </div>

        {{-- Payments History --}}
        <div class="col-12 col-xl-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header border-bottom bg-light bg-opacity-50">
                    <h5 class="card-title mb-0"><i
                            class="bx bx-credit-card me-2"></i>{{ __('Historique des Paiements') }}</h5>
                </div>
                <div class="card-datatable table-responsive">
                    <table class="table border-top table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ __('Date') }}</th>
                                <th class="text-end">{{ __('Montant') }}</th>
                                <th>{{ __('Sur Commande') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $pay)
                                <tr class="border-bottom">
                                    <td class="small py-3">
                                        <div class="fw-bold">{{ $pay->created_at->format('d/m/Y') }}</div>
                                        <div class="text-muted small">{{ $pay->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="text-end fw-bold text-success">
                                        + {{ number_format($pay->montant, 0, ',', ' ') }} <small>CDF</small>
                                    </td>
                                    <td>
                                        <span class="small">#{{ $pay->commande_client_id }}</span>
                                        <div class="text-muted small">{{ $pay->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        {{ __('Aucun paiement trouvé.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer px-3 py-2 border-top">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {

            .btn,
            .breadcrumb,
            header,
            footer,
            aside,
            .card-footer,
            .mb-4:has(.form-control) {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            body {
                background: white !important;
            }

            .container-xxl {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }

            h4 {
                text-align: center;
                margin-bottom: 30px !important;
            }
        }
    </style>
</div>
