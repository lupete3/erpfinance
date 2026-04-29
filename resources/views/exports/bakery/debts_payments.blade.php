@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </p>

    <div class="section-title">Historique des Paiements Clients</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('sale.client') }}</th>
                <th>{{ __('Commande') }}</th>
                <th class="text-end">{{ __('Montant Versé') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $p->client->name ?? 'N/A' }}</td>
                    <td>#{{ $p->commande_client_id }}</td>
                    <td class="text-end" style="color: green;">{{ number_format($p->montant, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="3" class="text-end">TOTAL VERSÉ</td>
                <td class="text-end">{{ number_format($payments->sum('montant'), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>

    <div class="section-title" style="color: red;">Dettes Clients en Attente</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('sale.client') }}</th>
                <th>{{ __('Commande') }}</th>
                <th class="text-end">{{ __('Reste à Payer') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debts as $d)
                <tr>
                    <td>{{ $d->client->name ?? 'N/A' }}</td>
                    <td>#{{ $d->id }} ({{ $d->created_at->format('d/m/Y') }})</td>
                    <td class="text-end" style="color: red;">{{ number_format($d->reste, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="2" class="text-end">TOTAL DETTES</td>
                <td class="text-end">{{ number_format($debts->sum('reste'), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
@endsection