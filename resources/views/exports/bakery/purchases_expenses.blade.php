@extends('components.layouts.pdf')

@section('title', $title)
@section('report-title', $title)

@section('content')
    <p><strong>Période :</strong> Du {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} au
        {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>

    <div class="section-title">Historique des Achats Matières Premières</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Fournisseur') }}</th>
                <th>{{ __('Article') }}</th>
                <th class="text-center">{{ __('Quantité') }}</th>
                <th class="text-end">{{ __('Montant') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $pur)
                <tr>
                    <td>{{ $pur->created_at->format('d/m/Y') }}</td>
                    <td>{{ $pur->fournisseur->nom ?? 'N/A' }}</td>
                    <td>{{ $pur->stockMaison->designation ?? 'N/A' }}</td>
                    <td class="text-center">{{ $pur->quantite }} {{ $pur->stockMaison->unite ?? '' }}</td>
                    <td class="text-end">{{ number_format($pur->prix_achat * $pur->quantite, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="4" class="text-end">TOTAL ACHATS</td>
                <td class="text-end">
                    {{ number_format($purchases->sum(fn($p) => $p->prix_achat * $p->quantite), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>

    <div class="page-break"></div>

    <div class="section-title">Dépenses Diverses</div>
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Motif') }}</th>
                <th>{{ __('Bénéficiaire') }}</th>
                <th class="text-end">{{ __('Montant') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $exp)
                <tr>
                    <td>{{ $exp->created_at->format('d/m/Y') }}</td>
                    <td>{{ $exp->motif }}</td>
                    <td>{{ $exp->personne ?? 'N/A' }}</td>
                    <td class="text-end" style="color: red;">{{ number_format($exp->montant, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="font-weight: bold; background-color: #eee;">
                <td colspan="3" class="text-end">TOTAL DÉPENSES</td>
                <td class="text-end">{{ number_format($expenses->sum('montant'), 0, ',', ' ') }} FC</td>
            </tr>
        </tfoot>
    </table>
@endsection