<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Store;
use Illuminate\Support\Facades\Hash;
use function Livewire\Volt\{state, rules};

state([
    'name' => '',
    'email' => '',
    'password' => '',
    'store_id' => '',
    'stores' => fn() => Store::all(),
]);

rules([
    'name' => 'required|min:3',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6',
    'store_id' => 'required|exists:stores,id',
]);

$save = function () {
    $this->validate();

    $gerantRole = Role::where('name', 'Gérant')->first();

    User::create([
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
        'tenant_id' => Auth::user()->tenant_id,
        'role_id' => $gerantRole->id,
        'store_id' => $this->store_id,
        'is_active' => true,
    ]);

    notyf()->success(__('Gérant créé avec succès.'));

    return redirect()->route('finance.boss.managers.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Gérants /</span> Ajouter un gérant
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Informations du compte gérant</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nom Complet</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Ex: Jean Dupont" />
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email (Identifiant de connexion)</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="gerant@succursale.com" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Mot de passe temporaire</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password" placeholder="············" />
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="store_id">Succursale à gérer</label>
                            <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" wire:model="store_id">
                                <option value="">Sélectionnez une succursale</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }} - {{ $store->location }}</option>
                                @endforeach
                            </select>
                            @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Créer le compte</button>
                            <a href="{{ route('finance.boss.managers.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="alert alert-info">
                <h6 class="alert-heading fw-bold mb-1">Note :</h6>
                <p class="mb-0">Le gérant pourra se connecter avec son email et le mot de passe que vous avez défini. Il aura accès uniquement aux dépenses de la succursale assignée.</p>
            </div>
        </div>
    </div>
</div>
