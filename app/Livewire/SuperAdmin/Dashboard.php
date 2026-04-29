<?php

namespace App\Livewire\SuperAdmin;


use App\Models\Tenant;
use App\Models\User;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\Plan;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'tenants'       => Tenant::count(),
            'stores'        => Store::count(),
            'users'         => User::count(),
            'revenues'      => Subscription::sum('amount'), // assure-toi d’avoir `amount` dans subscriptions
            'subscriptions' => Subscription::where('is_active', true)->count(),
            'expired'       => Subscription::where('end_date', '<', now())->orWhere('is_active', false)->count(),
        ];
    }

    public function render()
    {
        // Graphique revenus (12 derniers mois)
        $revenuesByMonth = Subscription::select(
                DB::raw("DATE_FORMAT(start_date, '%Y-%m') as month"),
                DB::raw("SUM(amount) as total")
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->take(12)
            ->pluck('total', 'month')
            ->toArray();

        // Répartition des plans
        $planDistribution = Plan::withCount('subscriptions')->get();

        $latestTenants = Tenant::with('subscriptions', 'stores')
            ->latest()
            ->take(5)
            ->get();

        $latestSubscriptions = Subscription::with('tenant', 'plan')
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.super-admin.dashboard', [
            'revenuesByMonth'   => $revenuesByMonth,
            'planDistribution'  => $planDistribution,
            'latestSubscriptions'  => $latestSubscriptions,
            'latestTenants'  => $latestTenants,
        ]);
    }
}

