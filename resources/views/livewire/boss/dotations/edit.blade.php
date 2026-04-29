<?php

use App\Models\Dotation;
use App\Models\Store;
use function Livewire\Volt\{state, rules, mount};

state([
    'dotation' => null,
    'store_id' => '',
    'amount' => '',
    'currency' => 'USD',
    'date_dotation' => '',
    'description' => '',
    'reference' => '',
    'stores' => fn() => Store::all(),
]);

mount(function (Dotation $dotation) {
    $this->dotation = $dotation;
    $this->store_id = $dotation->store_id;
    $this->amount = $dotation->amount;
    $this->currency = $dotation->currency;
    $this->date_dotation = $dotation->date_dotation;
    $this->description = $dotation->description;
    $this->reference = $dotation->reference;
});

$update = function () {
    $this->validate([
        'store_id' => 'required|exists:stores,id',
        'amount' => 'required|numeric|min:1',
        'currency' => 'required|in:USD,CDF',
        'date_dotation' => 'required|date',
        'description' => 'nullable',
        'reference' => 'nullable',
    ]);

    $this->dotation->update([
        'store_id' => $this->store_id,
        'amount' => $this->amount,
        'currency' => $this->currency,
        'date_dotation' => $this->date_dotation,
        'description' => $this->description,
        'reference' => $this->reference,
    ]);

    notyf()->success(__('Dotation mise à jour. Les soldes ont été recalculés.'));

    return redirect()->route('finance.boss.dotations.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dotations /</span> Modifier l'opération
    </h4>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <h5 class="card-header">Détails de la dotation</h5>
                <div class="card-body">
                    <form wire:submit="update">
                        <div class="mb-3">
                            <label class="form-label" for="store_id">Succursale bénéficiaire</label>
                            <select class="form-select @error('store_id') is-invalid @enderror" id="store_id" wire:model="store_id">
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
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" wire:model="amount" />
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
                            <label class="form-label" for="reference">Référence / N° Bordereau</label>
                            <input type="text" class="form-control" id="reference" wire:model="reference" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Note / Observation</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="2"></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Mettre à jour la dotation</button>
                            <a href="{{ route('finance.boss.dotations.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
