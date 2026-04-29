@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th style="font-size: 10px;">Date</th>
                <th style="font-size: 10px;">Produit Fini</th>
                <th class="text-center" style="font-size: 10px;">Qté</th>
                <th class="text-end" style="font-size: 10px;">PU</th>
                <th class="text-end" style="font-size: 10px;">Valeur</th>
                <th style="font-size: 10px;">Ingrédients</th>
                <th class="text-end" style="font-size: 10px;">Coût Prod.</th>
                <th class="text-end" style="font-size: 10px;">Bénéfice</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalValeur = 0;
                $grandTotalCout = 0;
                $grandTotalBenefice = 0;
            @endphp
            @foreach($productions as $p)
                @php
                    $valeur = $p->quantite * ($p->produitFinis->prix ?? 0);
                    $coutMp = $p->compositions->sum(fn($c) => $c->quantite * ($c->prix ?? 0));
                    $coutTotal = $coutMp + $p->charge_personnel + $p->autres_charges;
                    $benefice = $valeur - $coutTotal;
                    
                    $grandTotalValeur += $valeur;
                    $grandTotalCout += $coutTotal;
                    $grandTotalBenefice += $benefice;
                @endphp
                <tr>
                    <td style="font-size: 9px;">{{ $p->created_at->format('d/m/Y') }}</td>
                    <td style="font-size: 9px;"><strong>{{ $p->produitFinis->designation ?? 'N/A' }}</strong></td>
                    <td class="text-center" style="font-size: 9px;">{{ $p->quantite }}</td>
                    <td class="text-end" style="font-size: 9px;">{{ number_format($p->produitFinis->prix ?? 0, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-size: 9px; font-weight: bold;">{{ number_format($valeur, 0, ',', ' ') }}</td>
                    <td style="font-size: 8px;">
                        @foreach($p->compositions as $comp)
                            {{ $comp->designation }} ({{ $comp->quantite }}{{ $comp->unite }}),
                        @endforeach
                    </td>
                    <td class="text-end" style="font-size: 9px;">{{ number_format($coutTotal, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-size: 9px; font-weight: bold; color: {{ $benefice >= 0 ? 'green' : 'red' }};">
                        {{ number_format($benefice, 0, ',', ' ') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" style="font-size: 10px;">{{ __('TOTAUX') }}</td>
                <td class="text-end" style="font-size: 10px;">{{ number_format($grandTotalValeur, 0, ',', ' ') }} FC</td>
                <td></td>
                <td class="text-end" style="font-size: 10px;">{{ number_format($grandTotalCout, 0, ',', ' ') }} FC</td>
                <td class="text-end" style="font-size: 10px; color: {{ $grandTotalBenefice >= 0 ? 'green' : 'red' }};">
                    {{ number_format($grandTotalBenefice, 0, ',', ' ') }} FC
                </td>
            </tr>
        </tfoot>
    </table>
@endsection