<div>
    <div class="flex-grow-1">

        <div
            class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2 text-primary">{{ Str::upper(company()?->name ?? config('app.name')) }}</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group me-2">
                    @if (Auth::user()->role_id == 1)
                        <label for="storeFilter" class="form-label">{{ __('dashboard.filtrer_par_magasin') }}</label>
                        <select wire:model.lazy="storeId" id="storeFilter" class="form-select">
                            <option value="">{{ __('dashboard.tous_les_magasins') }}</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        </div>

        <!-- ===== Stats Cards ===== -->
        <div class="row g-4 mb-4">
            <!-- Ventes Aujourd'hui -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="d-block text-muted mb-1">{{ __('dashboard.ventes_aujourdhui') }}</span>
                                <h3 class="card-title mb-2">{{ number_format($todaySales, 2, ',', ' ') }}
                                    {{ company()?->devise }}</h3>
                                <span
                                    class="badge rounded-pill bg-label-{{ $salesGrowth >= 0 ? 'success' : 'danger' }}">
                                    <i
                                        class="bx {{ $salesGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }} me-1"></i>
                                    {{ number_format($salesGrowth, 1) }}% {{ __('dashboard.vs_hier') }}
                                </span>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="menu-icon tf-icons bx bx-cart"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ventes du mois -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="d-block text-muted mb-1">{{ __('dashboard.ventes_du_mois') }}</span>
                                <h3 class="card-title mb-2">{{ number_format($currentMonthSales, 2, ',', ' ') }}
                                    {{ company()?->devise }}</h3>
                                <span
                                    class="badge rounded-pill bg-label-{{ $monthSalesGrowth >= 0 ? 'success' : 'danger' }}">
                                    <i
                                        class="bx {{ $monthSalesGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }} me-1"></i>
                                    {{ number_format($monthSalesGrowth, 1) }}% {{ __('dashboard.vs_mois_passe') }}
                                </span>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="menu-icon tf-icons bx bx-bar-chart"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Achats du mois -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="d-block text-muted mb-1">{{ __('dashboard.achats_du_mois') }}</span>
                                <h3 class="card-title mb-2">{{ number_format($currentMonthPurchases, 2, ',', ' ') }}
                                    {{ company()?->devise }}</h3>
                                <span
                                    class="badge rounded-pill bg-label-{{ $monthPurchasesGrowth >= 0 ? 'success' : 'danger' }}">
                                    <i
                                        class="bx {{ $monthPurchasesGrowth >= 0 ? 'bx-up-arrow-alt' : 'bx-down-arrow-alt' }} me-1"></i>
                                    {{ number_format($monthPurchasesGrowth, 1) }}% {{ __('dashboard.vs_mois_passe') }}
                                </span>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="menu-icon tf-icons bx bx-cart-download"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Solde en caisse -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="d-block text-muted mb-1">{{ __('dashboard.solde_en_caisse') }}</span>
                                <h3 class="card-title mb-2">{{ $current_balance }} {{ company()?->devise }}</h3>
                            </div>
                            <div class="avatar flex-shrink-0">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="menu-icon tf-icons bx bx-box"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Charts & Recent Sales ===== -->
        <div class="row g-4 mb-4">
            <!-- Sales Chart -->
            <div class="col-lg-8">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('dashboard.ventes_hebdomadaires') }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" wire:ignore style="max-height: 320px"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('dashboard.ventes_recentes') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentSales as $sale)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="tf-icons bx bx-receipt"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">#INV-{{ $sale->id }}</div>
                                            <small class="text-muted">{{ $sale->sale_date }}</small>
                                        </div>
                                    </div>
                                    <span
                                        class="fw-semibold">{{ number_format($sale->total_paid, 2, ',', ' ') }}{{ company()?->devise }}</span>
                                </li>
                            @empty
                                <span class="badge rounded-pill bg-label-info">
                                    <p class="text-muted mb-0">{{ __('dashboard.aucune_vente') }}</p>
                                </span>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== Popular Products & Quick Actions ===== -->
        <div class="row g-4">
            <!-- Produits populaires -->
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('dashboard.produits_populaires') }}</h5>
                        <a href="{{ route('products.index') }}"
                            class="btn btn-sm btn-outline-primary">{{ __('dashboard.afficher_tous') }}</a>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @forelse ($popularProducts as $product)
                                <div class="col-12 col-md-4 col-lg-4">
                                    <div class="card h-100 border-1 product-card">
                                        <div class="card-body text-center">
                                            <h6 class="card-title mb-1 text-truncate" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </h6>
                                            <div class="text-primary fw-semibold mb-1">
                                                {{ number_format($product->sale_price, 2, ',', ' ') }}{{ company()?->devise }}
                                            </div>
                                            <small class="text-muted d-block">
                                                {{ __('dashboard.stock') }} :
                                                {{ $product->stores->sum('pivot.quantity') }}
                                            </small>
                                            <small class="text-success d-block fw-semibold">
                                                {{ __('dashboard.vendus') }} : {{ $product->total_sold ?? 0 }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center">
                                    <span class="badge rounded-pill bg-label-info">
                                        <p class="text-muted mb-0">{{ __('dashboard.aucun_produit_vendu') }}</p>
                                    </span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Achats récents -->
            <div class="col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ __('dashboard.achats_recents') }}</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse ($recentPurchases as $purchase)
                                <li class="list-group-item d-flex align-items-center justify-content-between px-0">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="tf-icons bx bx-receipt"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-medium">#ORD-{{ $purchase->id }}</div>
                                            <small class="text-muted">{{ $purchase->purchase_date }}</small>
                                        </div>
                                    </div>
                                    <span
                                        class="fw-semibold">{{ number_format($purchase->total_paid, 2, ',', ' ') }}{{ company()?->devise }}</span>
                                </li>
                            @empty
                                <span class="badge rounded-pill bg-label-info">
                                    <p class="text-muted mb-0">{{ __('dashboard.aucun_achat') }}</p>
                                </span>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="col-lg-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('dashboard.actions_rapides') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <a href="{{ route('pos.index') }}"
                                    class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                                    <i class="tf-icons bx bx-receipt fs-2 mb-2"></i>
                                    <span>{{ __('dashboard.nouvelle_vente') }}</span>
                                </a>
                            </div>
                            <div class="col-6 col-md-3">
                                <a href="{{ route('clients.index') }}"
                                    class="btn btn-outline-success w-100 d-flex flex-column align-items-center py-3">
                                    <i class="tf-icons bx bx-user-plus fs-2 mb-2"></i>
                                    <span>{{ __('dashboard.nouveau_client') }}</span>
                                </a>
                            </div>
                            @if (Auth::user()->role_id == 1)
                                <div class="col-6 col-md-3">
                                    <a href="{{ route('products.index') }}"
                                        class="btn btn-outline-warning w-100 d-flex flex-column align-items-center py-3">
                                        <i class="tf-icons bx bx-box fs-2 mb-2"></i>
                                        <span>{{ __('dashboard.ajouter_stock') }}</span>
                                    </a>
                                </div>
                                <div class="col-6 col-md-3">
                                    <a href="{{ route('purchases.index') }}"
                                        class="btn btn-outline-info w-100 d-flex flex-column align-items-center py-3">
                                        <i class="tf-icons bx bx-cart-add fs-2 mb-2"></i>
                                        <span>{{ __('dashboard.nouvel_achat') }}</span>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Chart.js ===== -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const labels = @json($weeklySales->keys()->map(fn($w) => 'Semaine ' . $w));
            const dataVals = @json($weeklySales->values());
            const ctx = document.getElementById('salesChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventes ({{ company()?->devise }})',
                        data: dataVals,
                        backgroundColor: '#6EB8F1', // Sneat primary with alpha
                        borderColor: '#6EB8F4',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value + '{{ company()?->devise }}'
                            }
                        }
                    }
                }
            });
        })();
    </script>

    <script>
        window.addEventListener('load', function() {
            Livewire.on('updateChartData', function(data) {
                var chartInstance = document.getElementById('salesChart').getContext('2d');
                if (chartInstance.chart !== undefined && chartInstance.chart !== null) {
                    chartInstance.chart.destroy();
                }
            })
        })
    </script>

    <script>
        window.addEventListener('weeklySalesUpdated', function(event) {

            const labels = event.detail.labels;
            const dataVals = event.detail.data;;
            const ctx = document.getElementById('salesChart');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventes ({{ company()?->devise }})',
                        data: dataVals,
                        backgroundColor: '#6EB8F1', // Sneat primary with alpha
                        borderColor: '#6EB8F4',
                        borderWidth: 1,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => value + '{{ company()?->devise }}'
                            }
                        }
                    }
                }
            });

        });
    </script>

</div>
