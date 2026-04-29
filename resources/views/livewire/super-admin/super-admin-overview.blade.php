<div>
  {{-- 🔹 Statistiques globales selon l’onglet --}}
  <div class="row mb-3">
    <div class="col-md-3 mb-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Valeur</h5>
          <p class="fs-4 text-success">{{ number_format($stats['totalValue'],0,',','.') }}</p>
        </div>
      </div>
    </div>

    <div class="col-md-3 mb-2">
      <div class="card text-center shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Total {{ $stats['title'] }}</h5>
          <p class="fs-4">{{ $stats['totalCount'] }}</p>
        </div>
      </div>
    </div>

    @if($filter === 'subscriptions')
      <div class="col-md-2 mb-2">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Expirant (7j)</h5>
            <p class="fs-4 text-warning">{{ $stats['expiringSoon'] }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-2 mb-2">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Expirés</h5>
            <p class="fs-4 text-danger">{{ $stats['expired'] }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-2 mb-2">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Plan Populaire</h5>
            <p class="fs-6">{{ $stats['topPlan']?->name ?? 'N/A' }}</p>
          </div>
        </div>
      </div>
    @endif
  </div>

  {{-- 🔹 Meilleurs Tenants --}}
  @if($filter === 'subscriptions' && count($stats['topTenants']))
    <div class="card mb-4 shadow-sm">
      <div class="card-header">🏆 Top 5 Tenants</div>
      <ul class="list-group list-group-flush">
        @foreach($stats['topTenants'] as $tenant)
          <li class="list-group-item d-flex justify-content-between">
            <span>{{ $tenant->name }}</span>
            <span class="fw-bold text-success">{{ number_format($tenant->total_amount,0,',','.') }} $</span>
          </li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- 🔹 Boutons de filtre --}}
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <button class="btn btn-sm {{ $filter==='tenants' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="setFilter('tenants')">Tenants</button>
        <button class="btn btn-sm {{ $filter==='plans' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="setFilter('plans')">Plans</button>
        <button class="btn btn-sm {{ $filter==='subscriptions' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="setFilter('subscriptions')">Souscriptions</button>
        <button class="btn btn-sm {{ $filter==='users' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="setFilter('users')">Utilisateurs</button>
      </div>

      <input type="text" class="form-control w-25" placeholder="Rechercher..." wire:model.live="search">
    </div>

    <div class="card-body table-responsive">
      {{-- Tables dynamiques --}}
      @if($filter === 'tenants')
        <table class="table table-bordered">
          <thead><tr><th>Nom</th><th>Email</th></tr></thead>
          <tbody>
            @foreach($items as $tenant)
              <tr><td>{{ $tenant->name }}</td><td>{{ $tenant->email }}</td></tr>
            @endforeach
          </tbody>
        </table>
      @elseif($filter === 'plans')
        <table class="table table-bordered">
          <thead><tr><th>Nom</th><th>Prix</th><th>Durée</th></tr></thead>
          <tbody>
            @foreach($items as $plan)
              <tr><td>{{ $plan->name }}</td><td>{{ $plan->price }} $</td><td>{{ $plan->duration >= 10000 ? 'Illimité' : $plan->duration }} jrs</td></tr>
            @endforeach
          </tbody>
        </table>
      @elseif($filter === 'subscriptions')
        <table class="table table-bordered">
          <thead><tr><th>Tenant</th><th>Plan</th><th>Montant</th><th>Début</th><th>Fin</th></tr></thead>
          <tbody>
            @foreach($items as $sub)
              <tr>
                <td>{{ $sub->tenant->name }}</td>
                <td>{{ $sub->plan->name }}</td>
                <td>{{ $sub->amount }} $</td>
                <td>{{ $sub->start_date }}</td>
                <td>{{ $sub->end_date }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @elseif($filter === 'users')
        <table class="table table-bordered">
          <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Tenant</th></tr></thead>
          <tbody>
            @foreach($items as $user)
              <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role?->name }}</td>
                <td>{{ $user->tenant?->name ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif

      <div class="mt-3">
        {{ $items->links() }}
      </div>
    </div>
  </div>
</div>