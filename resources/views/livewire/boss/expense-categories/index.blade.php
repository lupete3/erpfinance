<?php

use App\Models\ExpenseCategory;
use function Livewire\Volt\{state, rules};

state([
    'categories' => fn() => ExpenseCategory::all(),
    'name' => '',
    'description' => '',
]);

rules([
    'name' => 'required|min:3',
    'description' => 'nullable',
]);

$save = function () {
    $this->validate();

    ExpenseCategory::create([
        'tenant_id' => Auth::user()->tenant_id,
        'name' => $this->name,
        'description' => $this->description,
    ]);

    $this->name = '';
    $this->description = '';
    $this->categories = ExpenseCategory::all();

    notyf()->success(__('Catégorie ajoutée.'));
};

$delete = function (ExpenseCategory $category) {
    if ($category->expenses()->count() > 0) {
        notyf()->error(__('Impossible de supprimer : cette catégorie contient des dépenses.'));
        return;
    }
    
    $category->delete();
    $this->categories = ExpenseCategory::all();
    notyf()->success(__('Catégorie supprimée.'));
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Configuration /</span> Catégories de Dépenses
    </h4>

    <div class="row">
        <!-- Formulaire -->
        <div class="col-md-4">
            <div class="card mb-4">
                <h5 class="card-header">Nouvelle Catégorie</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Ex: Loyer, Carburant..." />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" wire:model="description" placeholder="Optionnel..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste -->
        <div class="col-md-8">
            <div class="card">
                <h5 class="card-header">Liste des catégories</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @forelse($categories as $category)
                                <tr>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>{{ $category->description ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('finance.boss.expense-categories.edit', $category->id) }}" class="btn btn-sm btn-label-warning">
                                            <i class="bx bx-edit-alt me-1"></i>
                                        </a>
                                        <button class="btn btn-sm btn-label-danger" wire:click="delete({{ $category->id }})" wire:confirm="Supprimer cette catégorie ?">
                                            <i class="bx bx-trash me-1"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucune catégorie définie.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
