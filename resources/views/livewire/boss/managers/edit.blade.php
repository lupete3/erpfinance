<?php

use App\Models\User;
use App\Models\Store;
use function Livewire\Volt\{state, rules, mount};

state([
    'manager' => null,
    'name' => '',
    'email' => '',
    'store_id' => '',
    'password' => '',
    'stores' => fn() => Store::all(),
]);

mount(function (User $manager) {
    $this->manager = $manager;
    $this->name = $manager->name;
    $this->email = $manager->email;
    $this->store_id = $manager->store_id;
});

$update = function () {
    $this->validate([
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email,' . $this->manager->id,
        'store_id' => 'required|exists:stores,id',
    ]);

    $data = [
        'name' => $this->name,
        'email' => $this->email,
        'store_id' => $this->store_id,
    ];

    if (!empty($this->password)) {
        $data['password'] = Hash::make($this->password);
    }

    $this->manager->update($data);

    notyf()->success(__('Compte gérant mis à jour.'));

    return redirect()->route('finance.boss.managers.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gérants /</span> Modifier le compte
    </h4>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <h5 class="card-header">Détails du gérant</h5>
                <div class="card-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom Complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email (Identifiant)</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="store_id">Succursale affectée</label>
                            <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" wire:model="store_id">
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Nouveau mot de passe (Laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="password" wire:model="password" />
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer les changements</button>
                            <a href="{{ route('finance.boss.managers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
