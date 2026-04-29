<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport de Dépenses</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .logo-section { float: left; width: 30%; }
        .company-section { float: right; width: 65%; text-align: right; }
        .company-name { font-size: 18px; font-weight: bold; text-transform: uppercase; color: #222; }
        .clear { clear: both; }
        .report-title { text-align: center; font-size: 16px; margin: 20px 0; font-weight: bold; text-decoration: underline; }
        .info-table { width: 100%; margin-bottom: 15px; background: #f9f9f9; padding: 10px; border: 1px solid #eee; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th { background-color: #444; color: white; border: 1px solid #333; padding: 6px; text-align: left; }
        .data-table td { border: 1px solid #ccc; padding: 6px; }
        .text-end { text-align: right; }
        .total-box { margin-top: 15px; text-align: right; font-size: 13px; font-weight: bold; border-top: 2px solid #444; padding-top: 5px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #777; border-top: 1px solid #eee; padding-top: 5px; }
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

    <div class="report-title">RAPPORT DÉTAILLÉ DES DÉPENSES</div>

    <table class="info-table">
        <tr>
            <td><strong>Période :</strong> Du {{ date('d/m/Y', strtotime($dateFrom)) }} au {{ date('d/m/Y', strtotime($dateTo)) }}</td>
            <td class="text-end"><strong>Succursale :</strong> {{ $storeName }}</td>
        </tr>
        <tr>
            <td><strong>Devise :</strong> {{ $currency }}</td>
            <td class="text-end"><strong>Généré le :</strong> {{ $generatedAt }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Succursale</th>
                <th>Catégorie</th>
                <th>Bénéficiaire</th>
                <th>Référence</th>
                <th class="text-end">Montant</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ date('d/m/Y', strtotime($expense->expense_date)) }}</td>
                    <td>{{ $expense->store->name }}</td>
                    <td>{{ $expense->category->name }}</td>
                    <td>{{ $expense->beneficiary }}</td>
                    <td>{{ $expense->reference }}</td>
                    <td class="text-end fw-bold">{{ number_format($expense->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-box">
        TOTAL PÉRIODE ({{ $currency }}) : {{ number_format($total, 2) }}
    </div>

    <div class="footer">
        {{ $company->name ?? 'ERP FINANCE' }} - Logiciel de gestion financière. Page 1/1
    </div>
</body>
</html>
