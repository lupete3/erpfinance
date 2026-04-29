<?php

use App\Models\Dotation;
use App\Models\Store;
use function Livewire\Volt\{state, rules};

state([
    'store_id' => '',
    'amount' => '',
    'currency' => 'USD',
    'date_dotation' => date('Y-m-d'),
    'description' => '',
    'reference' => '',
    'stores' => fn() => Store::all(),
]);

rules([
    'store_id' => 'required|exists:stores,id',
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
        'store_id' => $this->store_id,
        'user_id' => Auth::id(),
        'amount' => $this->amount,
        'currency' => $this->currency,
        'date_dotation' => $this->date_dotation,
        'description' => $this->description,
        'reference' => $this->reference,
    ]);

    notyf()->success(__('Dotation envoyée avec succès.'));

    return redirect()->route('finance.boss.dotations.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dotations /</span> Envoyer des fonds
    </h4>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <h5 class="card-header">Formulaire de dotation</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label" for="store_id">Succursale bénéficiaire</label>
                            <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" wire:model="store_id">
                                <option value="">Sélectionnez une succursale</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            @error('store_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label" for="amount">Montant</label>
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
                            <label class="form-label" for="date_dotation">Date de l'envoi</label>
                            <input type="date" class="form-control @error('date_dotation') is-invalid @enderror" id="date_dotation" wire:model="date_dotation" />
                            @error('date_dotation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="reference">Référence / N° Bordereau (Optionnel)</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" id="reference" wire:model="reference" placeholder="Laissé vide pour génération auto" />
                            @error('reference') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Note / Motif</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="3" placeholder="Motif de l'envoi..."></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Confirmer l'envoi</button>
                            <a href="{{ route('finance.boss.dotations.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-label-primary mb-4">
                <div class="card-body">
                    <h5 class="card-title text-primary">Info Dotation</h5>
                    <p class="card-text">
                        Une dotation représente un flux d'argent liquide ou bancaire que vous envoyez à une succursale pour couvrir ses dépenses opérationnelles.
                    </p>
                    <hr>
                    <p class="mb-0">
                        Cette transaction sera visible dans les rapports de la succursale comme une entrée de fonds.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
