@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th class="text-end">{{ __('Ventes') }}</th>
                <th class="text-end">{{ __('Dépenses') }}</th>
                <th class="text-end">{{ __('Consom') }}</th>
                <th class="text-end">{{ __('Attendu') }}</th>
                <th class="text-end">{{ __('Espèce') }}</th>
                <th class="text-end">{{ __('Écart') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($financials as $f)
                <tr>
                    <td>{{ $f->created_at->format('d/m/Y') }}</td>
                    <td class="text-end">{{ number_format($f->vente, 0, ',', ' ') }} FC</td>
                    <td class="text-end">{{ number_format($f->depense, 0, ',', ' ') }} FC</td>
                    <td class="text-end">{{ number_format($f->consommation, 0, ',', ' ') }} FC</td>
                    <td class="text-end"><strong>{{ number_format($f->total, 0, ',', ' ') }} FC</strong></td>
                    <td class="text-end" style="color: blue;">{{ number_format($f->espece, 0, ',', ' ') }} FC</td>
                    <td class="text-end">
                        @php $diff = $f->espece - $f->total; @endphp
                        <span style="color: {{ $diff < 0 ? 'red' : 'green' }}; font-weight: bold;">
                            {{ number_format($diff, 0, ',', ' ') }} FC
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td class="text-end">TOTAL</td>
                <td class="text-end">{{ number_format($financials->sum('vente'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($financials->sum('depense'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($financials->sum('consommation'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($financials->sum('total'), 0, ',', ' ') }} FC</td>
                <td class="text-end">{{ number_format($financials->sum('espece'), 0, ',', ' ') }} FC</td>
                <td class="text-end">
                    {{ number_format($financials->sum('espece') - $financials->sum('total'), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
@endsection