<?php

use App\Models\CompanySetting;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use function Livewire\Volt\{state, rules, mount, uses};

uses([WithFileUploads::class]);

state([
    'name' => '',
    'address' => '',
    'email' => '',
    'phone' => '',
    'rccm' => '',
    'id_nat' => '',
    'logo' => null,
    'currentLogo' => '',
]);

rules([
    'name' => 'required|min:3',
    'address' => 'required',
    'email' => 'nullable|email',
    'phone' => 'required',
    'logo' => 'nullable|image|max:1024',
]);

mount(function () {
    $settings = CompanySetting::first();
    if ($settings) {
        $this->name = $settings->name;
        $this->address = $settings->address;
        $this->email = $settings->email;
        $this->phone = $settings->phone;
        $this->rccm = $settings->rccm;
        $this->id_nat = $settings->id_nat;
        $this->currentLogo = $settings->logo;
    }
});

$save = function () {
    $this->validate();

    $data = [
        'name' => $this->name,
        'address' => $this->address,
        'email' => $this->email,
        'phone' => $this->phone,
        'rccm' => $this->rccm,
        'id_nat' => $this->id_nat,
    ];

    if ($this->logo) {
        $data['logo'] = $this->logo->store('logos', 'public');
    }

    $settings = CompanySetting::first();
    if ($settings) {
        $settings->update($data);
    } else {
        CompanySetting::create($data);
    }

    notyf()->success(__('Paramètres enregistrés.'));
};

$removeLogo = function () {
    $settings = CompanySetting::first();
    if ($settings && $settings->logo) {
        Storage::disk('public')->delete($settings->logo);
        $settings->update(['logo' => null]);
        $this->currentLogo = null;
        notyf()->success(__('Logo supprimé.'));
    }
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Paramètres /</span> Informations de l'Entreprise
    </h4>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <h5 class="card-header">Détails de l'entreprise</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de l'entreprise</label>
                                <input type="text" class="form-control" wire:model="name" />
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email" />
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse physique</label>
                            <input type="text" class="form-control" wire:model="address" />
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="text" class="form-control" wire:model="phone" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo</label>
                                <input type="file" class="form-control" wire:model="logo" />
                                @if($currentLogo)
                                    <div class="mt-2 d-flex align-items-center">
                                        <img src="{{ asset('storage/' . $currentLogo) }}" alt="Logo" class="img-thumbnail me-2" style="max-height: 50px;">
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeLogo" wire:confirm="Supprimer le logo actuel ?">
                                            <i class="bx bx-trash"></i> Retirer
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Enregistrer les paramètres</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
