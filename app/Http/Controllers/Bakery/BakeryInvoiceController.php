<?php

namespace App\Http\Controllers\Bakery;

use App\Http\Controllers\Controller;
use App\Models\CommandeClient;
use Illuminate\Http\Request;

class BakeryInvoiceController extends Controller
{
    /**
     * Print in POS (Thermal) format.
     */
    public function printPos($id)
    {
        $commande = CommandeClient::with(['client', 'ventes', 'site'])->findOrFail($id);
        return view('bakery.invoice-pos', compact('commande'));
    }

    /**
     * Print in A4 format.
     */
    public function printA4($id)
    {
        $commande = CommandeClient::with(['client', 'ventes', 'site'])->findOrFail($id);
        return view('bakery.invoice-a4', compact('commande'));
    }
}
