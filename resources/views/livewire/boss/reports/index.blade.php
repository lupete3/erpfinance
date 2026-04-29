<?php

use App\Models\Expense;
use App\Models\Store;
use App\Models\ExpenseCategory;
use function Livewire\Volt\{state, computed};

state([
    'date_from' => date('Y-m-01'),
    'date_to' => date('Y-m-d'),
    'store_id' => '',
    'category_id' => '',
    'currency' => 'USD',
    'stores' => fn() => Store::all(),
    'categories' => fn() => ExpenseCategory::all(),
]);

$expenses = computed(function () {
    return Expense::query()
        ->when($this->store_id, fn($q) => $q->where('store_id', $this->store_id))
        ->when($this->category_id, fn($q) => $q->where('expense_category_id', $this->category_id))
        ->where('currency', $this->currency)
        ->whereBetween('expense_date', [$this->date_from, $this->date_to])
        ->with(['store', 'category'])
        ->latest('expense_date')
        ->get();
});

$totalAmount = computed(function () {
    return $this->expenses->sum('amount');
});

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Analyse /</span> Rapports de Dépenses
    </h4>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Du</label>
                    <input type="date" class="form-control" wire:model.live="date_from">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Au</label>
                    <input type="date" class="form-control" wire:model.live="date_to">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Succursale</label>
                    <select class="form-select" wire:model.live="store_id">
                        <option value="">Toutes</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Catégorie</label>
                    <select class="form-select" wire:model.live="category_id">
                        <option value="">Toutes</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Devise</label>
                    <select class="form-select" wire:model.live="currency">
                        <option value="USD">USD</option>
                        <option value="CDF">CDF</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Détails des dépenses</h5>
            <div>
                <span class="badge bg-label-primary fs-6 me-2">Total : {{ number_format($this->totalAmount, 2) }} {{ $currency }}</span>
                <a href="{{ route('finance.boss.reports.export', [
                    'from' => $date_from,
                    'to' => $date_to,
                    'store' => $store_id,
                    'cat' => $category_id,
                    'cur' => $currency
                ]) }}" target="_blank" class="btn btn-danger btn-sm">
                    <i class="bx bxs-file-pdf me-1"></i> Exporter PDF
                </a>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
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
                <tbody class="table-border-bottom-0">
                    @forelse($this->expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_date }}</td>
                            <td>{{ $expense->store->name }}</td>
                            <td><span class="badge bg-label-secondary">{{ $expense->category->name }}</span></td>
                            <td>{{ $expense->beneficiary }}</td>
                            <td><small class="text-muted">{{ $expense->reference }}</small></td>
                            <td class="text-end fw-bold">{{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Aucune donnée trouvée pour ces filtres.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
