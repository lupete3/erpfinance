<div>
    <!-- Statistiques principales -->
    <div class="row mb-4 g-3">
        @php
            $cards = [
                ['title'=>'Clients','value'=>$stats['tenants'],'color'=>'primary','icon'=>'bx-building'],
                ['title'=>'Magasins','value'=>$stats['stores'],'color'=>'success','icon'=>'bx-store'],
                ['title'=>'Utilisateurs','value'=>($stats['users'] - 1),'color'=>'info','icon'=>'bx-user'],
                ['title'=>'Revenus','value'=>"$ ".number_format($stats['revenues'],2),'color'=>'warning','icon'=>'bx-dollar'],
                ['title'=>'Souscriptions','value'=>$stats['subscriptions'],'color'=>'secondary','icon'=>'bx-credit-card'],
                ['title'=>'Expirées','value'=>$stats['expired'],'color'=>'danger','icon'=>'bx-calendar-x']
            ];
        @endphp

        @foreach($cards as $c)
        <div class="col-md-2">
            <div class="card shadow-sm border-{{ $c['color'] }}" >
                <div class="card-body text-center">
                    <i class="bx {{ $c['icon'] }} fs-2 text-{{ $c['color'] }}"></i>
                    <h5 class="card-title ">{{ $c['title'] }}</h5>
                    <h4 class="fw-bold">{{ $c['value'] }}</h4>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Graphiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Revenus par mois</div>
                <div class="card-body">
                    <canvas id="revenuesChart" wire:ignore style="max-height: 260px"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Répartition des plans</div>
                <div class="card-body">
                    <canvas id="plansChart" wire:ignore style="max-height: 260px"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers tenants et souscriptions -->
    <div class="row g-3">
        <!-- Derniers Tenants -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Derniers Tenants</div>
                <ul class="list-group list-group-flush">
                    @foreach($latestTenants as $tenant)
                        @php
                            $latestSubscription = $tenant->subscriptions->sortByDesc('created_at')->first();
                        @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $tenant->name }}</strong><br>
                                <small class="text-muted">
                                    Contact: {{ $tenant->contact_name }}<br>
                                    Email: {{ $tenant->email }}<br>
                                    Magasins: {{ $tenant->stores->count() }}
                                </small>
                            </div>
                            <span class="badge bg-{{ $latestSubscription ? 'primary' : 'secondary' }}">
                                {{ $latestSubscription?->plan?->name ?? 'Aucun plan' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <!-- Dernières Souscriptions -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Dernières Souscriptions</div>
                <ul class="list-group list-group-flush">
                    @foreach($latestSubscriptions as $sub)
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $sub->tenant?->name }}</strong> → 
                                <span class="badge bg-secondary">{{ $sub->plan?->name }}</span>
                                <div class="small text-muted mt-1">
                                    Montant: {{ number_format($sub->amount, 2) }} $ | 
                                    Durée: {{ $sub->plan?->duration_days >= 10000 ? 'Illimité' : $sub->plan?->duration_days.' jours' }} | 
                                    Début: {{ $sub->start_date }} | 
                                    Fin: {{ $sub->plan?->duration_days >= 10000 ? 'Illimité' : $sub->end_date }} |
                                    Users max: {{ $sub->plan?->max_users ?? 'Illimité' }} |
                                    Stores max: {{ $sub->plan?->max_stores ?? 'Illimité' }}
                                </div>
                            </div>
                            <span class="badge bg-{{ $sub->end_date >= now() ? 'success' : 'danger' }}">
                                {{ $sub->end_date >= now() ? 'Actif' : 'Expiré' }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // IIFE pour le graphique des Revenus (Revenues Chart)
    (function() {
        let revenuesChartInstance = null;

        /**
         * Affiche ou met à jour le graphique des revenus.
         * @param {Array<string>} labels Les étiquettes pour l'axe X (mois).
         * @param {Array<number>} data Les données de revenus.
         */
        function renderRevenuesChart(labels, data) {
            const ctx = document.getElementById('revenuesChart').getContext('2d');

            // Détruit l'instance existante si elle y en a une
            if (revenuesChartInstance) {
                revenuesChartInstance.destroy();
            }

            // Crée une nouvelle instance de graphique en ligne
            revenuesChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenus ($)',
                        data: data,
                        borderColor: '#6EB8F1',
                        backgroundColor: 'rgba(110, 184, 241, 0.3)',
                        fill: true,
                        tension: 0.3
                    }]
                }
            });
        }

        // Rendu initial avec les données Blade
        const initialRevenuesLabels = @json(array_keys($revenuesByMonth));
        const initialRevenuesData = @json(array_values($revenuesByMonth));
        renderRevenuesChart(initialRevenuesLabels, initialRevenuesData);

        // Écouteur Livewire pour la mise à jour des revenus
        window.addEventListener('load', function() {
            Livewire.on('revenuesUpdated', function(data) {
                // 'data' devrait contenir 'labels' et 'data'
                renderRevenuesChart(data.labels, data.data);
            });
        });

    })();
</script>

<script>
    // IIFE pour le graphique des Plans (Plans Chart)
    (function() {
        let plansChartInstance = null;

        /**
         * Affiche ou met à jour le graphique de répartition des plans.
         * @param {Array<string>} labels Les noms des plans.
         * @param {Array<number>} data Le nombre d'abonnements par plan.
         */
        function renderPlansChart(labels, data) {
            const ctx = document.getElementById('plansChart').getContext('2d');

            // Détruit l'instance existante si elle y en a une
            if (plansChartInstance) {
                plansChartInstance.destroy();
            }

            // Crée une nouvelle instance de graphique en donut (doughnut)
            plansChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#696CFF','#FFAB00','#FF3E1D','#71DD37','#03C3EC']
                    }]
                }
            });
        }

        // Rendu initial avec les données Blade
        const initialPlansLabels = @json($planDistribution->pluck('name'));
        const initialPlansData = @json($planDistribution->pluck('subscriptions_count'));
        renderPlansChart(initialPlansLabels, initialPlansData);

        // Écouteur Livewire pour la mise à jour des plans
        window.addEventListener('load', function() {
            Livewire.on('plansUpdated', function(data) {
                 // 'data' devrait contenir 'labels' et 'data'
                renderPlansChart(data.labels, data.data);
            });
        });

    })();
</script>


