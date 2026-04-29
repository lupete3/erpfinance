@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
    </p>

    <h3 style="margin-top: 20px; color: #7367f0;">1. Transferts Matières Premières (Dépôt -> Usine)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Matière Première</th>
                <th class="text-center">Quantité</th>
                <th class="text-end">Dépôt (Après)</th>
                <th class="text-end">Usine (Après)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transferMps as $mvt)
                <tr>
                    <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $mvt->stockMaison->designation ?? 'N/A' }}</strong></td>
                    <td class="text-center">{{ $mvt->quantite }} {{ $mvt->stockMaison->unite ?? '' }}</td>
                    <td class="text-end">{{ $mvt->reste_maison }}</td>
                    <td class="text-end">{{ $mvt->reste_usine }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Aucun mouvement enregistré pour cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h3 style="margin-top: 30px; color: #ff9f43;">2. Transferts Produits Finis (Fournil -> Points de Vente)</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Produit Fini</th>
                <th>Destination</th>
                <th class="text-center">Quantité</th>
                <th class="text-end">Fournil (Après)</th>
                <th class="text-end">Site (Après)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transferPfs as $mvt)
                <tr>
                    <td>{{ $mvt->created_at->format('d/m/Y H:i') }}</td>
                    <td><strong>{{ $mvt->stockPf->designation ?? 'N/A' }}</strong></td>
                    <td>{{ $mvt->site->nom ?? 'N/A' }}</td>
                    <td class="text-center">{{ $mvt->quantite }} pcs</td>
                    <td class="text-end">{{ $mvt->reste_stock_pf }}</td>
                    <td class="text-end">{{ $mvt->reste_boulangerie }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Aucun mouvement enregistré pour cette période.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 10px; color: #666;">
        <p>Total MP transférées : {{ $transferMps->sum('quantite') }} unités</p>
        <p>Total PF transférés : {{ $transferPfs->sum('quantite') }} pièces</p>
    </div>
@endsection