<?php

use App\Models\Store;
use function Livewire\Volt\{state, rules, mount};

state([
    'store' => null,
    'name' => '',
    'location' => '',
    'phone' => '',
    'email' => '',
]);

rules([
    'name' => 'required|min:3',
    'location' => 'nullable',
    'phone' => 'nullable',
    'email' => 'nullable|email',
]);

mount(function (Store $store) {
    $this->store = $store;
    $this->name = $store->name;
    $this->location = $store->location;
    $this->phone = $store->phone;
    $this->email = $store->email;
});

$update = function () {
    $this->validate();

    $this->store->update([
        'name' => $this->name,
        'location' => $this->location,
        'phone' => $this->phone,
        'email' => $this->email,
    ]);

    notyf()->success(__('Succursale mise à jour.'));

    return redirect()->route('finance.boss.stores.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Paramètres /</span> Modifier la Succursale
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Informations de la succursale</h5>
                <div class="card-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom de la succursale</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="location">Emplacement / Ville</label>
                            <input type="text" class="form-control" id="location" wire:model="location" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">Téléphone</label>
                            <input type="text" class="form-control" id="phone" wire:model="phone" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email de contact</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Sauvegarder les modifications</button>
                            <a href="{{ route('finance.boss.stores.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
