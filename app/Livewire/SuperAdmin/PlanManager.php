<?php

namespace App\Livewire\SuperAdmin;

use App\Models\Plan;
use Livewire\Component;
use Livewire\WithPagination;

class PlanManager extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $planId;
    public $name, $price, $duration_days, $max_users, $max_stores;
    public $isEditMode = false;

    protected $listeners = ['deleteConfirmed' => 'delete'];

    protected function rules()
    {
        return [
            'name'          => 'required|string|max:255',
            'price'         => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_users'     => 'nullable|integer|min:1',
            'max_stores'    => 'nullable|integer|min:1',
        ];
    }

    public function render()
    {
        $plans = Plan::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', '%' . $this->search . '%')
            )
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.super-admin.plan-manager', [
            'plans' => $plans,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEditMode = false;
    }

    public function edit($id)
    {
        $plan = Plan::findOrFail($id);
        $this->planId        = $id;
        $this->name          = $plan->name;
        $this->price         = $plan->price;
        $this->duration_days = $plan->duration_days;
        $this->max_users     = $plan->max_users;
        $this->max_stores    = $plan->max_stores;
        $this->isEditMode    = true;
    }

    public function save()
    {
        $this->validate();

        Plan::updateOrCreate(
            ['id' => $this->planId],
            [
                'name'          => $this->name,
                'price'         => $this->price,
                'duration_days' => $this->duration_days,
                'max_users'     => $this->max_users,
                'max_stores'    => $this->max_stores,
            ]
        );

        notyf()->success(__($this->isEditMode ? 'Plan mis à jour.' : 'Plan créé.'));

        $this->dispatch('close-modal');
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $this->planId = $id;
        $this->dispatch('show-delete-confirmation');
    }

    public function delete()
    {
        Plan::find($this->planId)?->delete();
        notyf()->success(__('Plan supprimé.'));
        $this->dispatch('close-delete-confirmation');
    }

    private function resetInputFields()
    {
        $this->planId        = null;
        $this->name          = '';
        $this->price         = '';
        $this->duration_days = '';
        $this->max_users     = null;
        $this->max_stores    = null;
    }
}
