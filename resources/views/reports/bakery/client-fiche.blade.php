<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Fiche Client') }} - {{ $client->nom }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .company-info { float: left; width: 60%; }
        .report-info { float: right; width: 35%; text-align: right; }
        .clear { clear: both; }
        .title { text-align: center; text-transform: uppercase; margin: 20px 0; font-size: 18px; font-weight: bold; text-decoration: underline; }
        .client-section { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .summary-box { border: 1px solid #ddd; padding: 10px; margin-bottom: 20px; }
        .summary-item { display: inline-block; width: 32%; text-align: center; }
        .summary-label { display: block; font-size: 10px; color: #777; text-transform: uppercase; }
        .summary-value { display: block; font-size: 14px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #eee; padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 11px; }
        td { padding: 8px; border: 1px solid #ddd; vertical-align: top; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #777; text-align: center; border-top: 1px solid #ddd; padding-top: 5px; }
        .badge { padding: 2px 5px; border-radius: 3px; font-size: 9px; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-danger { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <h2 style="margin: 0;">{{ (string)($company->name ?? config('app.name')) }}</h2>
            <p style="margin: 5px 0;">
                {{ (string)($company->address ?? '') }}<br>
                {{ __('Tél') }}: {{ (string)($company->phone ?? '') }}<br>
                {{ (string)($company->email ?? '') }}
            </p>
        </div>
        <div class="report-info">
            <p style="margin: 0;"><strong>{{ __('Date') }}:</strong> {{ date('d/m/Y') }}</p>
            @if($start_date || $end_date)
                <p style="margin: 5px 0;"><strong>{{ __('Période') }}:</strong> <br> 
                    {{ $start_date ? date('d/m/Y', strtotime($start_date)) : '...' }} - 
                    {{ $end_date ? date('d/m/Y', strtotime($end_date)) : '...' }}
                </p>
            @endif
        </div>
        <div class="clear"></div>
    </div>

    <div class="title">{{ __('FICHE DE SITUATION CLIENT') }}</div>

    <div class="client-section">
        <table style="border: none; margin-bottom: 0;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;">
                    <strong>{{ __('Nom du Client') }}:</strong> {{ $client->nom }}<br>
                    <strong>{{ __('Contact') }}:</strong> {{ (string)($client->telephone ?? '-') }}
                </td>
                <td style="border: none; width: 50%;">
                    <strong>{{ __('Adresse') }}:</strong> {{ (string)($client->adresse ?? '-') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">{{ __('Total Achats') }}</span>
            <span class="summary-value">{{ number_format($total_achats, 0, ',', ' ') }} CDF</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">{{ __('Total Payé') }}</span>
            <span class="summary-value" style="color: #28a745;">{{ number_format($total_paye, 0, ',', ' ') }} CDF</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">{{ __('Solde / Dette') }}</span>
            <span class="summary-value" style="color: #dc3545;">{{ number_format($total_dette, 0, ',', ' ') }} CDF</span>
        </div>
    </div>

    <h3>1. {{ __('Historique des Achats') }}</h3>
    <table>
        <thead>
            <tr>
                <th width="12%">{{ __('Date') }}</th>
                <th width="8%">{{ __('Réf') }}</th>
                <th width="45%">{{ __('Détails Articles') }}</th>
                <th width="20%" class="text-right">{{ __('Montant Total') }}</th>
                <th width="15%" class="text-center">{{ __('Statut') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commands as $cmd)
                <tr>
                    <td>{{ $cmd->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">#{{ $cmd->id }}</td>
                    <td>
                        @foreach($cmd->ventes as $vente)
                            • {{ (float)$vente->quantite }} {{ (string)($vente->product->designation ?? $vente->designation ?? '') . ' (' . (string)($vente->prix ?? '') . ')' }}<br>
                        @endforeach
                    </td>
                    <td class="text-right">
                        {{ number_format($cmd->montant, 0, ',', ' ') }}<br>
                        <small style="color: #dc3545;">Rest.: {{ number_format($cmd->reste, 0, ',', ' ') }}</small>
                    </td>
                    <td class="text-center">
                        @if($cmd->reste <= 0)
                            <span class="badge badge-success">{{ __('PAYE') }}</span>
                        @elseif($cmd->paye > 0)
                            <span class="badge badge-warning">{{ __('PARTIEL') }}</span>
                        @else
                            <span class="badge badge-danger">{{ __('DETTE') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>2. {{ __('Historique des Paiements') }}</h3>
    <table>
        <thead>
            <tr>
                <th width="20%">{{ __('Date') }}</th>
                <th width="50%">{{ __('Référence Commande') }}</th>
                <th width="30%" class="text-right">{{ __('Montant Versé') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $pay)
                <tr>
                    <td>{{ $pay->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ __('Commande') }} #{{ $pay->commande_client_id }}</td>
                    <td class="text-right" style="font-weight: bold; color: #28a745;">
                        {{ number_format($pay->montant, 0, ',', ' ') }} CDF
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        {{ __('Document généré par') }} {{ config('app.name') }} - {{ date('d/m/Y H:i') }} - {{ __('Page') }} 1
    </div>
</body>
</html>
