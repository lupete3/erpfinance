@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <div class="section-title">Valeur Actuelle des Stocks (Prix de revient/Vente)</div>
    <table class="table">
        <tr>
            <td>{{ __('Stock MP (Dépôt)') }}</td>
            <td class="text-end"><strong>{{ number_format($situation['valeur_stocks']['maison'], 0, ',', ' ') }} FC</strong>
            </td>
        </tr>
        <tr>
            <td>{{ __('Stock MP (Usine)') }}</td>
            <td class="text-end"><strong>{{ number_format($situation['valeur_stocks']['usine'], 0, ',', ' ') }} FC</strong>
            </td>
        </tr>
        <tr>
            <td>{{ __('Stock Produits Finis') }}</td>
            <td class="text-end"><strong>{{ number_format($situation['valeur_stocks']['pf'], 0, ',', ' ') }} FC</strong>
            </td>
        </tr>
        <tr>
            <td>{{ __('Stock Points de Vente') }}</td>
            <td class="text-end"><strong>{{ number_format($situation['valeur_stocks']['boulangerie'], 0, ',', ' ') }}
                    FC</strong></td>
        </tr>
        <tr style="background-color: #eee; font-weight: bold;">
            <td>TOTAL VALEUR STOCK</td>
            <td class="text-end">{{ number_format($situation['valeur_stocks']['total'], 0, ',', ' ') }} FC</td>
        </tr>
    </table>

    <div class="section-title">Flux & Performance sur la Période</div>
    <table class="table">
        <tr>
            <td>{{ __('Total Achats MP') }}</td>
            <td class="text-end" style="color: red;">- {{ number_format($situation['flux']['achats'], 0, ',', ' ') }} FC
            </td>
        </tr>
        <tr>
            <td>{{ __('Valeur Production') }}</td>
            <td class="text-end" style="color: green;">+
                {{ number_format($situation['flux']['production_valeur'], 0, ',', ' ') }} FC</td>
        </tr>
        <tr>
            <td>{{ __('Coût Production (MP+Charges)') }}</td>
            <td class="text-end" style="color: orange;">-
                {{ number_format($situation['flux']['production_cout'], 0, ',', ' ') }} FC</td>
        </tr>
        <tr style="background-color: #eee; font-weight: bold;">
            <td>Bénéfice Théorique sur Production</td>
            <td class="text-end" style="color: green;">
                {{ number_format($situation['flux']['benefice_theorique'], 0, ',', ' ') }} FC</td>
        </tr>
    </table>
@endsection