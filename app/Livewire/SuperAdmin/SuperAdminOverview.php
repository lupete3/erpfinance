<?php

namespace App\Livewire\SuperAdmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SuperAdminOverview extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $filter = 'tenants';
    public $search = '';
    public $perPage = 10;
    public $period = 'month'; // jour, semaine, mois, année, intervalle
    public $startDate;
    public $endDate;

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function getStats()
    {
        if ($this->filter === 'tenants') {
            $total = Tenant::count();
            $withSubs = Tenant::has('subscriptions')->count();

            return [
                'title'        => 'Tenants',
                'totalValue'   => $withSubs,
                'totalCount'   => $total,
                'expiringSoon' => null,
                'expired'      => null,
                'topPlan'      => null,
                'topTenants'   => [],
            ];
        }

        if ($this->filter === 'plans') {
            $total = Plan::count();
            $topPlan = Plan::withCount('subscriptions')
                ->orderByDesc('subscriptions_count')
                ->first();

            return [
                'title'        => 'Plans',
                'totalValue'   => $total,
                'totalCount'   => $total,
                'expiringSoon' => null,
                'expired'      => null,
                'topPlan'      => $topPlan,
                'topTenants'   => [],
            ];
        }

        if ($this->filter === 'users') {
            $total = User::count();
            $withTenant = User::whereNotNull('tenant_id')->count();

            return [
                'title'        => 'Utilisateurs',
                'totalValue'   => $withTenant,
                'totalCount'   => $total,
                'expiringSoon' => null,
                'expired'      => null,
                'topPlan'      => null,
                'topTenants'   => [],
            ];
        }

        // 🔹 Cas par défaut = subscriptions
        $query = Subscription::with('plan');

        if ($this->period === 'day') {
            $query->whereDate('start_date', Carbon::today());
        } elseif ($this->period === 'week') {
            $query->whereBetween('start_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->period === 'month') {
            $query->whereMonth('start_date', Carbon::now()->month)
                  ->whereYear('start_date', Carbon::now()->year);
        } elseif ($this->period === 'year') {
            $query->whereYear('start_date', Carbon::now()->year);
        } elseif ($this->period === 'custom' && $this->startDate && $this->endDate) {
            $query->whereBetween('start_date', [$this->startDate, $this->endDate]);
        }

        $totalValue = (clone $query)->sum('amount');
        $totalCount = (clone $query)->count();

        $expiringSoon = Subscription::whereBetween('end_date', [Carbon::today(), Carbon::today()->addDays(7)])->count();
        $expired = Subscription::where('end_date', '<', Carbon::today())->count();

        $topPlan = Plan::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->first();

        $topTenants = Subscription::exists()
        ? Tenant::select('tenants.id', 'tenants.name', DB::raw('SUM(subscriptions.amount) as total_amount'))
            ->join('subscriptions', 'tenants.id', '=', 'subscriptions.tenant_id')
            ->groupBy('tenants.id', 'tenants.name')
            ->orderByDesc('total_amount')
            ->take(5)
            ->get()
        : collect();


        return [
            'title'        => 'Souscriptions',
            'totalValue'   => $totalValue,
            'totalCount'   => $totalCount,
            'expiringSoon' => $expiringSoon,
            'expired'      => $expired,
            'topPlan'      => $topPlan,
            'topTenants'   => $topTenants,
        ];
    }

    public function render()
    {
        $data = [];

        switch ($this->filter) {
            case 'tenants':
                $data = Tenant::where('name', 'like', "%{$this->search}%")->paginate($this->perPage);
                break;

            case 'plans':
                $data = Plan::where('name', 'like', "%{$this->search}%")->paginate($this->perPage);
                break;

            case 'subscriptions':
                $data = Subscription::with(['tenant', 'plan'])
                    ->where(function($q) {
                        $q->whereHas('tenant', fn($t) => $t->where('name', 'like', "%{$this->search}%"))
                          ->orWhereHas('plan', fn($p) => $p->where('name', 'like', "%{$this->search}%"));
                    })
                    ->paginate($this->perPage);
                break;

            case 'users':
                $data = User::with('tenant', 'role')
                    ->where(function($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                    })
                    ->paginate($this->perPage);
                break;
        }

        return view('livewire.super-admin.super-admin-overview', [
            'items' => $data,
            'stats' => $this->getStats()
        ]);
    }
}
