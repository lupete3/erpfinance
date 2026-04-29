<?php

use App\Models\Store;
use App\Models\Dotation;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use function Livewire\Volt\{state, computed};

state([
    'currency' => 'USD', // Filtre global par devise pour les analytiques
]);

$stats = computed(function () {
    return [
        'totalStores' => Store::count(),
        'in' => (float) Dotation::where('currency', $this->currency)->sum('amount'),
        'out' => (float) Expense::where('currency', $this->currency)->sum('amount'),
    ];
});

$topStores = computed(function () {
    return Store::withSum(['expenses' => function($q) {
        $q->where('currency', $this->currency);
    }], 'amount')
    ->orderByDesc('expenses_sum_amount')
    ->take(5)
    ->get();
});

$categoriesBreakdown = computed(function () {
    return ExpenseCategory::withSum(['expenses' => function($q) {
        $q->where('currency', $this->currency);
    }], 'amount')
    ->having('expenses_sum_amount', '>', 0)
    ->orderByDesc('expenses_sum_amount')
    ->get();
});

$recentActivities = computed(function () {
    $expenses = Expense::with(['store', 'category'])->latest()->take(5)->get()->map(fn($e) => [
        'type' => 'Dépense',
        'icon' => 'bx-trending-down',
        'color' => 'danger',
        'title' => $e->category->name ?? 'Dépense',
        'subtitle' => $e->store->name,
        'amount' => '-' . number_format($e->amount, 2) . ' ' . $e->currency,
        'date' => $e->expense_date,
    ]);

    $dotations = Dotation::with('store')->latest()->take(5)->get()->map(fn($d) => [
        'type' => 'Dotation',
        'icon' => 'bx-trending-up',
        'color' => 'success',
        'title' => 'Dotation envoyée',
        'subtitle' => $d->store->name,
        'amount' => '+' . number_format($d->amount, 2) . ' ' . $d->currency,
        'date' => $d->date_dotation,
    ]);

    return $expenses->concat($dotations)->sortByDesc('date')->take(7);
});

?>

<div>
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="fw-bold py-3 mb-0">Tableau de Bord Stratégique</h4>
            <p class="text-muted">Surveillance en temps réel de vos actifs financiers.</p>
        </div>
        <div class="col-md-6 text-md-end pt-3">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-{{ $currency == 'USD' ? 'primary' : 'outline-primary' }}" wire:click="$set('currency', 'USD')">USD</button>
                <button type="button" class="btn btn-{{ $currency == 'CDF' ? 'success' : 'outline-success' }}" wire:click="$set('currency', 'CDF')">CDF</button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Volume d'Entrées ({{ $currency }})</span>
                            <h3 class="card-title mb-0 text-success fw-bold">{{ number_format($this->stats['in'], 2) }}</h3>
                        </div>
                        <div class="avatar bg-light-success p-2 rounded">
                            <i class="bx bx-down-arrow-circle fs-3 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Cumul total des dotations envoyées</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-muted">Total des Dépenses ({{ $currency }})</span>
                            <h3 class="card-title mb-0 text-danger fw-bold">{{ number_format($this->stats['out'], 2) }}</h3>
                        </div>
                        <div class="avatar bg-light-danger p-2 rounded">
                            <i class="bx bx-up-arrow-circle fs-3 text-danger"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Fonds consommés par les succursales</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="d-block mb-1 text-white-50">Balance Nette ({{ $currency }})</span>
                            <h3 class="card-title mb-0 text-white fw-bold">{{ number_format($this->stats['in'] - $this->stats['out'], 2) }}</h3>
                        </div>
                        <div class="avatar bg-white-20 p-2 rounded text-white">
                            <i class="bx bx-wallet fs-3"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-white-50">Liquidités théoriques disponibles</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Distribution par Catégorie -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Dépenses par Catégorie</h5>
                    <small class="text-muted">Top catégories en {{ $currency }}</small>
                </div>
                <div class="card-body">
                    @forelse($this->categoriesBreakdown as $cat)
                        @php 
                            $percent = $this->stats['out'] > 0 ? ($cat->expenses_sum_amount / $this->stats['out']) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold">{{ $cat->name }}</span>
                                <span class="text-muted">{{ number_format($cat->expenses_sum_amount, 2) }} {{ $currency }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center py-5 text-muted">Aucune dépense enregistrée.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Activités Récentes -->
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Activités Récentes</h5>
                    <i class="bx bx-dots-vertical-rounded"></i>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        @foreach($this->recentActivities as $act)
                            <li class="d-flex mb-4 pb-1">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-label-{{ $act['color'] }}"><i class="bx {{ $act['icon'] }}"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">{{ $act['title'] }}</h6>
                                        <small class="text-muted d-block">{{ $act['subtitle'] }}</small>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <h6 class="mb-0 {{ $act['color'] == 'success' ? 'text-success' : 'text-danger' }}">{{ $act['amount'] }}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Succursales -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <h5 class="card-header">Top 5 Succursales par Dépenses</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <th>Succursale</th>
                                <th>Email</th>
                                <th class="text-end">Total Dépensé ({{ $currency }})</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->topStores as $store)
                                @php 
                                    $percent = $this->stats['out'] > 0 ? ($store->expenses_sum_amount / $this->stats['out']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td><span class="fw-bold">{{ $store->name }}</span></td>
                                    <td>{{ $store->email }}</td>
                                    <td class="text-end fw-bold">{{ number_format($store->expenses_sum_amount, 2) }}</td>
                                    <td style="width: 200px;">
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percent }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <style>
        .bg-light-success { background-color: rgba(40, 199, 111, 0.12) !important; }
        .bg-light-danger { background-color: rgba(234, 84, 85, 0.12) !important; }
        .bg-white-20 { background-color: rgba(255, 255, 255, 0.2) !important; }
        .card { transition: transform 0.2s; }
        .card:hover { transform: translateY(-5px); }
    </style>
</div>
