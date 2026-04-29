@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </p>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('N°') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('sale.client') }}</th>
                <th class="text-end">{{ __('Montant Total') }}</th>
                <th class="text-end">{{ __('Payé') }}</th>
                <th class="text-end">{{ __('Reste') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>#{{ $sale->id }}</td>
                    <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->client->name ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($sale->montant, 0, ',', ' ') }} FC</td>
                    <td class="text-end">{{ number_format($sale->paye, 0, ',', ' ') }} FC</td>
                    <td class="text-end">{{ number_format($sale->reste, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="3" class="text-end">TOTAL</td>
                <td class="text-end">{{ number_format($sales->sum('montant'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($sales->sum('paye'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($sales->sum('reste'), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
@endsection