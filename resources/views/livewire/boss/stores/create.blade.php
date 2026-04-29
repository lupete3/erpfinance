<?php

use App\Models\Store;
use function Livewire\Volt\{state, rules};

state([
    'name' => '',
    'location' => '',
    'phone' => '',
    'email' => '',
]);

rules([
    'name' => 'required|min:3',
    'location' => 'required',
    'phone' => 'nullable',
    'email' => 'nullable|email',
]);

$save = function () {
    $this->validate();

    Store::create([
        'name' => $this->name,
        'location' => $this->location,
        'phone' => $this->phone,
        'email' => $this->email,
    ]);

    notyf()->success(__('Succursale créée avec succès.'));

    return redirect()->route('finance.boss.stores.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Succursales /</span> Ajouter une nouvelle
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Informations de la succursale</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom de la succursale</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Ex: Succursale Kinshasa Gombe" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="location">Emplacement (Ville / Quartier)</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" wire:model="location" placeholder="Ex: Kinshasa, Gombe" />
                            @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="phone">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" wire:model="phone" placeholder="+243 ..." />
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email de contact</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="contact@succursale.com" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer</button>
                            <a href="{{ route('finance.boss.stores.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
