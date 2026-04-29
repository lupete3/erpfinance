<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture A4 #{{ $commande->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 40px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .company-info h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h2 {
            margin: 0;
            color: #7f8c8d;
            font-size: 24px;
            text-transform: uppercase;
        }

        .details-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .info-box {
            width: 45%;
        }

        .info-box h3 {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            font-size: 16px;
            text-transform: uppercase;
            color: #7f8c8d;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            float: right;
            width: 300px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .total-row.grand-total {
            border-top: 2px solid #333;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            font-size: 20px;
            color: #2c3e50;
        }

        .footer {
            clear: both;
            margin-top: 100px;
            text-align: center;
            font-size: 12px;
            color: #7f8c8d;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-info">
            <h1>{{ company()?->name }}</h1>
            <p>
                {{ company()?->adress }}<br>
                Tél: {{ company()?->phone }}<br>
                Email: {{ company()?->email ?? 'N/A' }}
            </p>
        </div>
        <div class="invoice-title">
            <h2>Facture</h2>
            <p>
                N°: <strong>#{{ $commande->id }}</strong><br>
                Date: {{ $commande->created_at->format('d/m/Y') }}<br>
                Site: {{ $commande->site->nom }}
            </p>
        </div>
    </div>

    <div class="details-row">
        <div class="info-box">
            <h3>Facturé à</h3>
            <p>
                <strong>{{ $commande->client->nom ?? 'Client Anonyme' }}</strong><br>
                Tél: {{ $commande->client->telephone ?? 'N/A' }}<br>
                Adresse: {{ $commande->client->adresse ?? 'N/A' }}
            </p>
        </div>
        <div class="info-box">
            <h3>Informations Complémentaires</h3>
            <p>
                Vendeur: {{ Auth::user()->name }}<br>
                Mode de paiement: Cash<br>
                Observation: {{ $commande->observation ?? '-' }}
            </p>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Désignation</th>
                <th class="text-right">Prix Unitaire</th>
                <th class="text-right">Quantité</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->ventes as $vente)
                <tr>
                    <td>{{ $vente->designation }}</td>
                    <td class="text-right">{{ number_format($vente->prix, 0, ',', ' ') }} FC</td>
                    <td class="text-right">{{ (int) $vente->quantite }}</td>
                    <td class="text-right">{{ number_format($vente->prix * $vente->quantite, 0, ',', ' ') }} FC</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Sous-Total:</span>
            <span>{{ number_format($commande->montant, 0, ',', ' ') }} FC</span>
        </div>
        <div class="total-row">
            <span>Montant Payé:</span>
            <span>{{ number_format($commande->paye, 0, ',', ' ') }} FC</span>
        </div>
        <div class="total-row grand-total">
            <span>Reste à Payer:</span>
            <span>{{ number_format($commande->reste, 0, ',', ' ') }} FC</span>
        </div>
    </div>

    <div class="footer">
        {{ company()?->name }} - RCCM: {{ company()?->rccm ?? 'N/A' }} - ID Nat: {{ company()?->id_nat ?? 'N/A' }}<br>
        Merci de votre confiance !
    </div>

    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
                // window.close(); // Optional: close after print
            }, 500);
        }
    </script>
</body>

</html>