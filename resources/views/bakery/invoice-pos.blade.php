<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Facture POS #{{ $commande->id }}</title>
    <style>
        @media print {
            @page {
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
                font-family: 'Courier New', monospace;
                font-size: 14px;
                line-height: 1.2;
            }
        }

        body {
            margin: 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.2;
            padding: 5px;
            width: 80mm;
            /* Standard POS width */
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .line {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            text-align: left;
            padding: 2px 0;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            font-size: 12px;
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="center bold" style="font-size: 18px;">{{ company()?->name }}</div>
    <div class="center">{{ $commande->site->nom }}</div>
    <div class="center">{{ company()?->adress }}</div>
    <div class="center">Tél: {{ company()?->phone }}</div>
    <div class="line"></div>

    <div class="center bold">FACTURE POS #{{ $commande->id }}</div>
    <div class="center">{{ $commande->created_at->format('d/m/Y H:i') }}</div>
    <div class="line"></div>

    <div><strong>Client:</strong> {{ $commande->client->nom ?? 'Client Anonyme' }}</div>
    <div><strong>Vendeur:</strong> {{ Auth::user()->name }}</div>
    <div class="line"></div>

    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th class="text-right">Qté</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->ventes as $vente)
                <tr>
                    <td>{{ $vente->designation }}</td>
                    <td class="text-right">{{ (int) $vente->quantite }}</td>
                    <td class="text-right">{{ number_format($vente->prix * $vente->quantite, 0, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="line"></div>
    <div class="bold text-right" style="font-size: 16px;">
        TOTAL: {{ number_format($commande->montant, 0, ',', ' ') }} FC
    </div>
    <div class="text-right">
        Payé: {{ number_format($commande->paye, 0, ',', ' ') }} FC
    </div>
    @if($commande->reste > 0)
        <div class="text-right bold text-danger">
            Reste: {{ number_format($commande->reste, 0, ',', ' ') }} FC
        </div>
    @endif

    <div class="line"></div>
    <div class="center footer">
        Merci de votre confiance !<br>
        Les marchandises vendues ne sont ni reprises ni échangées.
    </div>

    <script>
        window.onload = function () {
            setTimeout(function () {
                window.print();
                window.onafterprint = function () { window.close(); };
            }, 500);
        }
    </script>
</body>

</html>