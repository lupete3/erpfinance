<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bilan Financier</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .logo-section { float: left; width: 30%; }
        .company-section { float: right; width: 65%; text-align: right; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #222; }
        .clear { clear: both; }
        .report-title { text-align: center; font-size: 16px; margin: 20px 0; font-weight: bold; text-decoration: underline; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #444; color: white; border: 1px solid #333; padding: 8px; text-align: left; }
        td { border: 1px solid #ccc; padding: 8px; }
        .text-end { text-align: right; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .fw-bold { font-weight: bold; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
        .summary-box { margin-top: 25px; padding: 15px; background: #f4f4f4; border: 1px solid #ddd; }
        .company-ids { font-size: 9px; color: #555; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" style="max-height: 70px;">
            @else
                <div style="font-size: 24px; font-weight: bold; color: #ccc;">LOGO</div>
            @endif
        </div>
        <div class="company-section">
            <div class="company-name">{{ $company->name ?? 'ERP FINANCE' }}</div>
            <div>{{ $company->address ?? '' }}</div>
            <div>Tél : {{ $company->phone ?? '' }} | Email : {{ $company->email ?? '' }}</div>
            <div class="company-ids">
                @if($company->rccm) RCCM : {{ $company->rccm }} @endif
                @if($company->id_nat) | ID NAT : {{ $company->id_nat }} @endif
            </div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="report-title">BILAN FINANCIER GLOBAL (ENTRÉES & SORTIES)</div>
    <div style="text-align: center; margin-bottom: 20px;">
        Période : Du {{ date('d/m/Y', strtotime($dateFrom)) }} au {{ date('d/m/Y', strtotime($dateTo)) }} | Devise : {{ $currency }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Succursale</th>
                <th class="text-end">Entrées (+)</th>
                <th class="text-end">Sorties (-)</th>
                <th class="text-end">Balance (=)</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $grandTotalIn = 0; 
                $grandTotalOut = 0; 
            @endphp
            @foreach($summary as $item)
                @php 
                    $grandTotalIn += $item['in']; 
                    $grandTotalOut += $item['out']; 
                @endphp
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td class="text-end text-success">{{ number_format($item['in'], 2) }}</td>
                    <td class="text-end text-danger">{{ number_format($item['out'], 2) }}</td>
                    <td class="text-end fw-bold {{ $item['balance'] >= 0 ? 'text-primary' : 'text-danger' }}">
                        {{ number_format($item['balance'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background: #eee;">
                <td>TOTAL GÉNÉRAL ({{ $currency }})</td>
                <td class="text-end text-success">{{ number_format($grandTotalIn, 2) }}</td>
                <td class="text-end text-danger">{{ number_format($grandTotalOut, 2) }}</td>
                <td class="text-end {{ ($grandTotalIn - $grandTotalOut) >= 0 ? 'text-primary' : 'text-danger' }}">
                    {{ number_format($grandTotalIn - $grandTotalOut, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="summary-box">
        <div class="fw-bold">RÉSUMÉ DE LA PÉRIODE :</div>
        <p>Après analyse des mouvements de fonds sur l'ensemble des succursales, le solde net disponible est de <strong>{{ number_format($grandTotalIn - $grandTotalOut, 2) }} {{ $currency }}</strong>.</p>
    </div>

    <div class="footer">
        {{ $company->name ?? 'ERP FINANCE' }} - Rapport généré le {{ $generatedAt }}
    </div>
</body>
</html>
