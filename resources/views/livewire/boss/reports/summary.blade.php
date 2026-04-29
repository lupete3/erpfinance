<?php

use App\Models\Dotation;
use App\Models\Expense;
use App\Models\Store;
use function Livewire\Volt\{state, computed};

state([
    'date_from' => date('Y-m-01'),
    'date_to' => date('Y-m-d'),
    'store_id' => '',
    'currency' => 'USD',
    'stores' => fn() => Store::all(),
]);

$summary = computed(function () {
    $storeId = $this->store_id;
    $currency = $this->currency;

    $dotations = Dotation::query()
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->where('currency', $currency)
        ->whereBetween('date_dotation', [$this->date_from, $this->date_to])
        ->get();

    $expenses = Expense::query()
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->where('currency', $currency)
        ->whereBetween('expense_date', [$this->date_from, $this->date_to])
        ->get();

    $totalIn = $dotations->sum('amount');
    $totalOut = $expenses->sum('amount');

    return [
        'totalIn' => $totalIn,
        'totalOut' => $totalOut,
        'balance' => $totalIn - $totalOut,
        'dotationsCount' => $dotations->count(),
        'expensesCount' => $expenses->count(),
    ];
});

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Analyse /</span> Récapitulatif Entrées & Sorties
    </h4>

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
                <div class="col-md-3">
                    <label class="form-label">Succursale</label>
                    <select class="form-select" wire:model.live="store_id">
                        <option value="">Toutes (Global)</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Devise</label>
                    <select class="form-select" wire:model.live="currency">
                        <option value="USD">USD</option>
                        <option value="CDF">CDF</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Carte Entrées -->
        <div class="col-md-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <div class="badge p-2 bg-label-success mb-2"><i class="bx bx-down-arrow-alt fs-3"></i></div>
                    <h5 class="card-title">Total Entrées (Dotations)</h5>
                    <h3 class="fw-bold text-success">{{ number_format($this->summary['totalIn'], 2) }} {{ $currency }}</h3>
                    <small class="text-muted">{{ $this->summary['dotationsCount'] }} opération(s)</small>
                </div>
            </div>
        </div>

        <!-- Carte Sorties -->
        <div class="col-md-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <div class="badge p-2 bg-label-danger mb-2"><i class="bx bx-up-arrow-alt fs-3"></i></div>
                    <h5 class="card-title">Total Sorties (Dépenses)</h5>
                    <h3 class="fw-bold text-danger">{{ number_format($this->summary['totalOut'], 2) }} {{ $currency }}</h3>
                    <small class="text-muted">{{ $this->summary['expensesCount'] }} opération(s)</small>
                </div>
            </div>
        </div>

        <!-- Carte Solde -->
        <div class="col-md-4">
            <div class="card text-center mb-4 {{ $this->summary['balance'] >= 0 ? 'bg-label-primary' : 'bg-label-warning' }}">
                <div class="card-body">
                    <div class="badge p-2 bg-primary mb-2 text-white"><i class="bx bx-calculator fs-3"></i></div>
                    <h5 class="card-title">Solde Net (Balance)</h5>
                    <h3 class="fw-bold">{{ number_format($this->summary['balance'], 2) }} {{ $currency }}</h3>
                    <small>Reste en caisse pour la période</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Résumé par Succursale</h5>
            <a href="{{ route('finance.boss.reports.summary.export', [
                'from' => $date_from,
                'to' => $date_to,
                'store' => $store_id,
                'cur' => $currency
            ]) }}" target="_blank" class="btn btn-danger btn-sm">
                <i class="bx bxs-file-pdf me-1"></i> Exporter Bilan PDF
            </a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Succursale</th>
                        <th class="text-success text-end">Entrées (+)</th>
                        <th class="text-danger text-end">Sorties (-)</th>
                        <th class="text-end">Balance (=)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $activeStores = $store_id ? Store::where('id', $store_id)->get() : $stores;
                    @endphp
                    @foreach($activeStores as $s)
                        @php
                            $in = Dotation::where('store_id', $s->id)->where('currency', $currency)->whereBetween('date_dotation', [$date_from, $date_to])->sum('amount');
                            $out = Expense::where('store_id', $s->id)->where('currency', $currency)->whereBetween('expense_date', [$date_from, $date_to])->sum('amount');
                        @endphp
                        <tr>
                            <td>{{ $s->name }}</td>
                            <td class="text-success text-end">{{ number_format($in, 2) }}</td>
                            <td class="text-danger text-end">{{ number_format($out, 2) }}</td>
                            <td class="text-end fw-bold {{ ($in - $out) >= 0 ? 'text-primary' : 'text-warning' }}">
                                {{ number_format($in - $out, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
