<?php

use App\Models\Expense;
use App\Models\Store;
use Livewire\WithPagination;
use function Livewire\Volt\{state, computed, uses};

uses([WithPagination::class]);

state([
    'selected_store_id' => fn() => Auth::user()->store_id,
    'stores' => fn() => Auth::user()->hasRoleString('Boss') ? Store::all() : [],
]);

$expenses = computed(function () {
    $storeId = Auth::user()->hasRoleString('Boss') ? $this->selected_store_id : Auth::user()->store_id;

    return Expense::query()
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->with(['category', 'store']) // Eager loading
        ->latest()
        ->paginate(15);
});

$deleteExpense = function (Expense $expense) {
    if ($expense->created_at->diffInHours(now()) > 24 && !Auth::user()->hasRoleString('Boss')) {
        notyf()->error(__('Cette dépense est verrouillée.'));
        return;
    }
    
    $expense->delete();
    notyf()->success(__('Dépense supprimée.'));
};

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Supervision /</span> Liste des Dépenses
        </h4>
        @if(Auth::user()->hasRoleString('Gérant'))
            <a href="{{ route('finance.manager.expenses.create') }}" class="btn btn-primary">
                <span class="tf-icons bx bx-plus"></span>&nbsp; Saisir une dépense
            </a>
        @endif
    </div>

    @if(Auth::user()->hasRoleString('Boss'))
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <label class="form-label">Filtrer par Succursale</label>
                        <select class="form-select" wire:model.live="selected_store_id">
                            <option value="">Toutes les succursales</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Historique des dépenses</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Succursale</th>
                        <th>Catégorie</th>
                        <th>Bénéficiaire</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->expenses as $expense)
                        @php $isLocked = $expense->created_at->diffInHours(now()) > 24; @endphp
                        <tr>
                            <td>
                                {{ $expense->expense_date }}
                                @if($isLocked) <i class="bx bx-lock-alt text-muted"></i> @endif
                            </td>
                            <td><small>{{ $expense->store->name }}</small></td>
                            <td><span class="badge bg-label-primary">{{ $expense->category?->name }}</span></td>
                            <td>{{ $expense->beneficiary }}</td>
                            <td><span class="fw-bold text-danger">{{ number_format($expense->amount, 2) }} {{ $expense->currency }}</span></td>
                            <td>
                                @if(!$isLocked || Auth::user()->hasRoleString('Boss'))
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <button class="dropdown-item text-danger" wire:click="deleteExpense({{ $expense->id }})" wire:confirm="Supprimer ?">
                                                <i class="bx bx-trash me-1"></i> Supprimer
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Aucune donnée.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->expenses->links() }}
        </div>
    </div>
</div>
