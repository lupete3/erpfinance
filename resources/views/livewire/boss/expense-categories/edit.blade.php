<?php

use App\Models\ExpenseCategory;
use function Livewire\Volt\{state, rules, mount};

state([
    'category' => null,
    'name' => '',
    'description' => '',
]);

rules([
    'name' => 'required|min:3',
    'description' => 'nullable',
]);

mount(function (ExpenseCategory $category) {
    $this->category = $category;
    $this->name = $category->name;
    $this->description = $category->description;
});

$update = function () {
    $this->validate();

    $this->category->update([
        'name' => $this->name,
        'description' => $this->description,
    ]);

    notyf()->success(__('Catégorie mise à jour.'));

    return redirect()->route('finance.boss.expense-categories.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Configuration /</span> Modifier la Catégorie
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Détails de la catégorie</h5>
                <div class="card-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="3"></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Mettre à jour</button>
                            <a href="{{ route('finance.boss.expense-categories.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
