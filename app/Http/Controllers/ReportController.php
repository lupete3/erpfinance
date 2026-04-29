<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Dotation;
use App\Models\Store;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    private function getLogoBase64($logoPath)
    {
        if (!$logoPath) return null;
        
        $path = storage_path('app/public/' . $logoPath);
        if (!file_exists($path)) return null;

        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $storeId = $request->get('store');
        $categoryId = $request->get('cat');
        $currency = $request->get('cur', 'USD');

        if ($user->hasRoleString('Gérant')) {
            $storeId = $user->store_id;
        }

        $expenses = Expense::query()
            ->when($storeId, fn($q) => $q->where('store_id', $storeId))
            ->when($categoryId, fn($q) => $q->where('expense_category_id', $categoryId))
            ->where('currency', $currency)
            ->whereBetween('expense_date', [$dateFrom, $dateTo])
            ->with(['store', 'category'])
            ->latest('expense_date')
            ->get();

        $storeName = $storeId ? Store::find($storeId)->name : 'Toutes les succursales';
        $total = $expenses->sum('amount');
        
        $company = company();
        $logoBase64 = $this->getLogoBase64($company->logo);

        $data = [
            'expenses' => $expenses,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'storeName' => $storeName,
            'currency' => $currency,
            'total' => $total,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'company' => $company,
            'logoBase64' => $logoBase64,
        ];

        return Pdf::loadView('reports.expenses-pdf', $data)->stream("Rapport_Depenses_{$dateFrom}_au_{$dateTo}.pdf");
    }

    public function exportSummary(Request $request)
    {
        $dateFrom = $request->get('from');
        $dateTo = $request->get('to');
        $storeId = $request->get('store');
        $currency = $request->get('cur', 'USD');

        $stores = $storeId ? Store::where('id', $storeId)->get() : Store::all();
        
        $summaryData = [];
        foreach ($stores as $store) {
            $in = Dotation::where('store_id', $store->id)->where('currency', $currency)->whereBetween('date_dotation', [$dateFrom, $dateTo])->sum('amount');
            $out = Expense::where('store_id', $store->id)->where('currency', $currency)->whereBetween('expense_date', [$dateFrom, $dateTo])->sum('amount');
            
            $summaryData[] = [
                'name' => $store->name,
                'in' => $in,
                'out' => $out,
                'balance' => $in - $out
            ];
        }

        $company = company();
        $logoBase64 = $this->getLogoBase64($company->logo);

        $data = [
            'summary' => $summaryData,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currency' => $currency,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'company' => $company,
            'logoBase64' => $logoBase64,
        ];

        return Pdf::loadView('reports.summary-pdf', $data)->stream("Bilan_Financier_{$dateFrom}_au_{$dateTo}.pdf");
    }
}
