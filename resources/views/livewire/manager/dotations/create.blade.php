<?php

use App\Models\Dotation;
use function Livewire\Volt\{state, rules};

state([
    'amount' => '',
    'currency' => 'USD',
    'date_dotation' => date('Y-m-d'),
    'description' => '',
    'reference' => '',
]);

rules([
    'amount' => 'required|numeric|min:1',
    'currency' => 'required|in:USD,CDF',
    'date_dotation' => 'required|date',
    'description' => 'nullable|string',
    'reference' => 'nullable|string|unique:dotations,reference',
]);

$save = function () {
    $this->validate();

    Dotation::create([
        'tenant_id' => Auth::user()->tenant_id,
        'store_id' => Auth::user()->store_id,
        'user_id' => Auth::id(),
        'amount' => $this->amount,
        'currency' => $this->currency,
        'date_dotation' => $this->date_dotation,
        'description' => $this->description,
        'reference' => $this->reference,
    ]);

    notyf()->success(__('Dotation enregistrée avec succès.'));

    return redirect()->route('finance.manager.dotations.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dotations /</span> Enregistrer des fonds reçus
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Formulaire de réception de fonds</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label" for="amount">Montant Reçu</label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" wire:model="amount" placeholder="0.00" />
                                    @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label" for="currency">Devise</label>
                                    <select class="form-select" id="currency" wire:model="currency">
                                        <option value="USD">USD</option>
                                        <option value="CDF">CDF</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="date_dotation">Date de réception</label>
                            <input type="date" class="form-control @error('date_dotation') is-invalid @enderror" id="date_dotation" wire:model="date_dotation" />
                            @error('date_dotation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="reference">Référence / N° Bordereau (Optionnel)</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" wire:model="reference" placeholder="Ex: Bordereau #1234" />
                            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Note / Motif</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="3" placeholder="Ex: Argent envoyé par le boss pour le loyer..."></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer la réception</button>
                            <a href="{{ route('finance.manager.dotations.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-label-info mb-4">
                <div class="card-body">
                    <h5 class="card-title text-info">Auto-enregistrement</h5>
                    <p class="card-text">
                        En tant que gérant, vous pouvez maintenant enregistrer vous-même les fonds que vous recevez du Boss sans attendre que ce dernier ne le fasse dans le système.
                    </p>
                    <hr>
                    <p class="mb-0">
                        Cette opération augmentera les fonds disponibles de votre succursale dans les rapports financiers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
