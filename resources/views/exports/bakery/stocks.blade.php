@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
        <div>
            <strong>Date d'état :</strong> {{ now()->format('d/m/Y H:i') }}
        </div>
        @if(isset($stocks['selected_site']))
            <div>
                <strong>Site :</strong> {{ $stocks['selected_site'] }}
            </div>
        @endif
    </div>

    @isset($stocks['maison'])
    <div class="section-title">{{ __('Stock MP (Dépôt)') }}</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Article') }}</th>
                <th class="text-end">{{ __('Solde') }}</th>
                <th class="text-end">{{ __('PU') }}</th>
                <th class="text-end">{{ __('Valeur') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMaison = 0; @endphp
            @foreach($stocks['maison'] as $s)
                @php 
                    $valeur = $s->solde * $s->prix;
                    $totalMaison += $valeur;
                @endphp
                <tr>
                    <td>{{ $s->designation }}</td>
                    <td class="text-end">{{ number_format($s->solde, 1, ',', ' ') }} {{ $s->unite }}</td>
                    <td class="text-end">{{ number_format($s->prix, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($valeur, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3"><strong>TOTAL</strong></td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($totalMaison, 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
    @endisset

    @isset($stocks['usine'])
    <div class="section-title" @if(isset($stocks['maison'])) style="margin-top: 20px;" @endif>{{ __('Stock MP (Usine)') }}</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Article') }}</th>
                <th class="text-end">{{ __('Solde') }}</th>
                <th class="text-end">{{ __('PU') }}</th>
                <th class="text-end">{{ __('Valeur') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $totalUsine = 0; @endphp
            @foreach($stocks['usine'] as $s)
                @php 
                    $prix = $s->stockMaison->prix ?? 0;
                    $valeur = $s->solde * $prix;
                    $totalUsine += $valeur;
                @endphp
                <tr>
                    <td>{{ $s->stockMaison->designation ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($s->solde, 1, ',', ' ') }} {{ $s->stockMaison->unite ?? '' }}</td>
                    <td class="text-end">{{ number_format($prix, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($valeur, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3"><strong>TOTAL</strong></td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($totalUsine, 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
    @endisset

    @isset($stocks['pf'])
    <div class="section-title" @if(isset($stocks['maison']) || isset($stocks['usine'])) style="margin-top: 20px;" @endif>{{ __('Stock Produits Finis') }}</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Produit') }}</th>
                <th class="text-end">{{ __('Solde') }}</th>
                <th class="text-end">{{ __('PU') }}</th>
                <th class="text-end">{{ __('Valeur') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPf = 0; @endphp
            @foreach($stocks['pf'] as $s)
                @php 
                    $valeur = $s->solde * $s->prix;
                    $totalPf += $valeur;
                @endphp
                <tr>
                    <td>{{ $s->designation }}</td>
                    <td class="text-end">{{ number_format($s->solde, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($s->prix, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($valeur, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3"><strong>TOTAL</strong></td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($totalPf, 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
    @endisset

    @isset($stocks['boulangerie'])
    <div class="section-title" @if(isset($stocks['maison']) || isset($stocks['usine']) || isset($stocks['pf'])) style="margin-top: 20px;" @endif>{{ __('Stock Points de Vente') }}</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Produit') }}</th>
                <th class="text-end">{{ __('Solde') }}</th>
                <th class="text-end">{{ __('PU') }}</th>
                <th class="text-end">{{ __('Valeur') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $totalBoul = 0; @endphp
            @foreach($stocks['boulangerie'] as $s)
                @php 
                    $prix = $s->stockProduitFinis->prix ?? 0;
                    $valeur = $s->solde * $prix;
                    $totalBoul += $valeur;
                @endphp
                <tr>
                    <td>{{ $s->stockProduitFinis->designation ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($s->solde, 0, ',', ' ') }}</td>
                    <td class="text-end">{{ number_format($prix, 0, ',', ' ') }}</td>
                    <td class="text-end" style="font-weight: bold;">{{ number_format($valeur, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa;">
                <td colspan="3"><strong>TOTAL</strong></td>
                <td class="text-end" style="font-weight: bold;">{{ number_format($totalBoul, 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
    @endisset
@endsection