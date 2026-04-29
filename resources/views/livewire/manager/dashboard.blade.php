<?php

use App\Models\Expense;
use App\Models\Dotation;
use App\Models\Store;
use function Livewire\Volt\{state, computed};

state([
    'selected_store_id' => fn() => Auth::user()->store_id,
    'stores' => fn() => Auth::user()->hasRoleString('Boss') ? Store::all() : [],
]);

$stats = computed(function () {
    $storeId = Auth::user()->hasRoleString('Boss') ? $this->selected_store_id : Auth::user()->store_id;

    $queryDotations = Dotation::query()->when($storeId, fn($q) => $q->where('store_id', $storeId));
    $queryExpenses = Expense::query()->when($storeId, fn($q) => $q->where('store_id', $storeId));

    return [
        'totalDotationsUSD' => (float) (clone $queryDotations)->where('currency', 'USD')->sum('amount'),
        'totalExpensesUSD' => (float) (clone $queryExpenses)->where('currency', 'USD')->sum('amount'),
        'totalDotationsCDF' => (float) (clone $queryDotations)->where('currency', 'CDF')->sum('amount'),
        'totalExpensesCDF' => (float) (clone $queryExpenses)->where('currency', 'CDF')->sum('amount'),
        'recentExpenses' => (clone $queryExpenses)->with('category', 'store')->latest()->take(5)->get(),
    ];
});

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">Tableau de Bord - Supervision</h4>
        
        @if(Auth::user()->hasRoleString('Boss'))
            <div class="d-flex align-items-center">
                <label class="me-2 mb-0">Succursale :</label>
                <select class="form-select form-select-sm w-auto" wire:model.live="selected_store_id">
                    <option value="">Toutes (Global)</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="row">
        <!-- Solde Caisse USD -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-wallet fs-3"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Solde Caisse (USD)</span>
                    <h3 class="card-title text-white mb-2">{{ number_format($this->stats['totalDotationsUSD'] - $this->stats['totalExpensesUSD'], 2) }} USD</h3>
                    <div class="mt-2">
                        <small class="opacity-75">Entrées: {{ number_format($this->stats['totalDotationsUSD'], 2) }} | Sorties: {{ number_format($this->stats['totalExpensesUSD'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Solde Caisse CDF -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar flex-shrink-0">
                            <i class="bx bx-wallet fs-3"></i>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Solde Caisse (CDF)</span>
                    <h3 class="card-title text-white mb-2">{{ number_format($this->stats['totalDotationsCDF'] - $this->stats['totalExpensesCDF'], 2) }} CDF</h3>
                    <div class="mt-2">
                        <small class="opacity-75">Entrées: {{ number_format($this->stats['totalDotationsCDF'], 2) }} | Sorties: {{ number_format($this->stats['totalExpensesCDF'], 2) }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières dépenses -->
    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Dernières dépenses
            <a href="{{ route('finance.manager.expenses.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
        </h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Succursale</th>
                        <th>Catégorie</th>
                        <th>Bénéficiaire</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->stats['recentExpenses'] as $expense)
                        <tr>
                            <td>{{ $expense->expense_date }}</td>
                            <td><small>{{ $expense->store->name }}</small></td>
                            <td><span class="badge bg-label-info">{{ $expense->category?->name }}</span></td>
                            <td>{{ $expense->beneficiary }}</td>
                            <td><span class="fw-bold text-danger">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Aucune donnée récente.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
