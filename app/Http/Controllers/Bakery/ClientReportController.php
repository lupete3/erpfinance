<?php

namespace App\Http\Controllers\Bakery;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CommandeClient;
use App\Models\PaiementClient;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientReportController extends Controller
{
    public function exportPdf(Client $client, Request $request)
    {
        $start_date = $request->get('start_date');
        $end_date = $request->get('end_date');

        $commandsQuery = CommandeClient::with(['ventes.product'])
            ->where('client_id', '=', $client->id);

        $paymentsQuery = PaiementClient::with(['commandeClient'])
            ->where('client_id', '=', $client->id);

        if ($start_date) {
            $commandsQuery->whereDate('created_at', '>=', $start_date);
            $paymentsQuery->whereDate('created_at', '>=', $start_date);
        }
        if ($end_date) {
            $commandsQuery->whereDate('created_at', '<=', $end_date);
            $paymentsQuery->whereDate('created_at', '<=', $end_date);
        }

        $commands = $commandsQuery->orderBy('created_at', 'ASC')->get();
        $payments = $paymentsQuery->orderBy('created_at', 'ASC')->get();
        
        $total_achats = $commands->sum('montant');
        $total_paye = $commands->sum('paye');
        $total_dette = $commands->sum('reste');

        $company = CompanySetting::first(['*']);

        $pdf = Pdf::loadView('reports.bakery.client-fiche', [
            'client' => $client,
            'commands' => $commands,
            'payments' => $payments,
            'total_achats' => (float)$total_achats,
            'total_paye' => (float)$total_paye,
            'total_dette' => (float)$total_dette,
            'company' => $company,
            'start_date' => is_string($start_date) ? $start_date : null,
            'end_date' => is_string($end_date) ? $end_date : null,
        ]);

        return $pdf->stream('Fiche_Client_' . str_replace(' ', '_', $client->nom) . '.pdf');
    }
}
