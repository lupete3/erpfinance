<?php

namespace App\Livewire\SuperAdmin;


use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\Plan;
use Livewire\Component;
use Livewire\WithPagination;

class SubscriptionManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $subscriptionId;
    public $tenant_id;
    public $plan_id;
    public $amount = 0;
    public $start_date;
    public $end_date;
    public $is_active = true;
    public $isEditMode = false;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    public function render()
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->when($this->search, fn($q) =>
                $q->whereHas('tenant', fn($qt) =>
                    $qt->where('name', 'like', "%{$this->search}%")
                )
            )
            ->latest()
            ->paginate(10);

        return view('livewire.super-admin.subscription-manager', [
            'subscriptions' => $subscriptions,
            'tenants' => Tenant::orderBy('name')->get(),
            'plans' => Plan::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $sub = Subscription::findOrFail($id);
        $this->subscriptionId = $id;
        $this->tenant_id = $sub->tenant_id;
        $this->plan_id = $sub->plan_id;
        $this->amount = $sub->amount;
        $this->start_date = $sub->start_date;
        $this->end_date = $sub->end_date;
        $this->is_active = (bool) $sub->is_active;
        $this->isEditMode = true;
    }

    public function save()
    {
        $this->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'plan_id' => 'required|exists:plans,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $plan = Plan::find($this->plan_id);

        Subscription::updateOrCreate(
            ['id' => $this->subscriptionId],
            [
                'tenant_id' => $this->tenant_id,
                'plan_id' => $this->plan_id,
                'amount' => $this->amount,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ]
        );

        notyf()->success(__($this->isEditMode ? 'Souscription mise à jour.' : 'Souscription créée.'));

        $this->dispatch('close-modal');
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->subscriptionId = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Subscription::find($this->subscriptionId)?->delete();
        notyf()->success(__('Souscription supprimée.'));
        $this->dispatch('close-delete-confirmation');
    }

    private function resetInputFields()
    {
        $this->subscriptionId = null;
        $this->tenant_id = '';
        $this->plan_id = '';
        $this->amount = 0;
        $this->start_date = '';
        $this->end_date = '';
        $this->is_active = true;
    }
}

