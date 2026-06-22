<?php

use App\Models\BudgetRequest;
use App\Models\BudgetRequestItem;
use function Livewire\Volt\{state, mount, rules, computed};

state([
    'budgetRequest' => null,
    'title' => '',
    'description' => '',
    'currency' => 'USD',
    'items' => [],
]);

mount(function (BudgetRequest $budgetRequest) {
    if ($budgetRequest->store_id !== Auth::user()->store_id) {
        abort(403);
    }
    if ($budgetRequest->status !== 'en_attente') {
        notyf()->error(__('Cette demande ne peut plus être modifiée car elle a déjà été traitée.'));
        return redirect()->route('finance.manager.budget-requests.index');
    }
    $this->budgetRequest = $budgetRequest;
    $this->title = $budgetRequest->title;
    $this->description = $budgetRequest->description;
    $this->currency = $budgetRequest->currency;
    $this->items = $budgetRequest->items->map(function ($item) {
        return [
            'id' => $item->id,
            'description' => $item->description,
            'quantity' => $item->quantity,
            'unit_amount' => $item->unit_amount,
        ];
    })->toArray();
});

$totalAmount = computed(function () {
    $total = 0;
    foreach ($this->items as $item) {
        $total += (float) ($item['quantity'] ?? 0) * (float) ($item['unit_amount'] ?? 0);
    }
    return $total;
});

$addItem = function () {
    $this->items[] = ['description' => '', 'quantity' => 1, 'unit_amount' => 0];
};

$removeItem = function ($index) {
    unset($this->items[$index]);
    $this->items = array_values($this->items);
};

$save = function () {
    if ($this->budgetRequest->status !== 'en_attente') {
        notyf()->error(__('Cette demande ne peut plus être modifiée car elle a déjà été traitée.'));
        return redirect()->route('finance.manager.budget-requests.index');
    }

    $this->validate([
        'title' => 'required|min:5',
        'currency' => 'required|in:USD,CDF',
        'items.*.description' => 'required|min:3',
        'items.*.quantity' => 'required|numeric|min:0.1',
        'items.*.unit_amount' => 'required|numeric|min:0',
    ]);

    if (count($this->items) === 0) {
        notyf()->error(__('Veuillez ajouter au moins une ligne de dépense.'));
        return;
    }

    $this->budgetRequest->update([
        'title' => $this->title,
        'description' => $this->description,
        'requested_amount' => $this->totalAmount,
        'currency' => $this->currency,
    ]);

    $this->budgetRequest->items()->delete();

    foreach ($this->items as $item) {
        BudgetRequestItem::create([
            'budget_request_id' => $this->budgetRequest->id,
            'description' => $item['description'],
            'quantity' => $item['quantity'],
            'unit_amount' => $item['unit_amount'],
            'total_amount' => (float)$item['quantity'] * (float)$item['unit_amount'],
        ]);
    }

    notyf()->success(__('État de besoin mis à jour.'));

    return redirect()->route('finance.manager.budget-requests.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">États de Besoin /</span> Modifier la demande
    </h4>

    <form wire:submit="save">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Objet de la demande globale</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title" placeholder="Ex: Besoins pour la semaine du 01/05" />
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Devise</label>
                                <select class="form-select" wire:model.live="currency">
                                    <option value="USD">USD - Dollars</option>
                                    <option value="CDF">CDF - Francs</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <h5 class="card-header d-flex justify-content-between align-items-center">
                        Détail des besoins (Lignes)
                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addItem">
                            <i class="bx bx-plus me-1"></i> Ajouter une ligne
                        </button>
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 50%;">Description / Article</th>
                                    <th>Quantité</th>
                                    <th>Prix Unitaire</th>
                                    <th class="text-end">Total</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $index => $item)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" wire:model="items.{{ $index }}.description" placeholder="Ex: Ramette de papier" />
                                            @error("items.$index.description") <small class="text-danger">{{ $message }}</small> @enderror
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" class="form-control form-control-sm" wire:model.live="items.{{ $index }}.quantity" />
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm" wire:model.live="items.{{ $index }}.unit_amount" />
                                        </td>
                                        <td class="text-end fw-bold">
                                            {{ number_format((float)($items[$index]['quantity'] ?? 0) * (float)($items[$index]['unit_amount'] ?? 0), 2) }}
                                        </td>
                                        <td>
                                            @if(count($items) > 1)
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-danger" wire:click="removeItem({{ $index }})">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold">MONTANT TOTAL ESTIMÉ :</td>
                                    <td class="text-end fw-bold fs-5 text-primary">
                                        {{ number_format($this->totalAmount, 2) }} {{ $currency }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <label class="form-label">Note complémentaire (Optionnel)</label>
                        <textarea class="form-control" wire:model="description" rows="2" placeholder="Informations supplémentaires..."></textarea>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Enregistrer les modifications</button>
                            <a href="{{ route('finance.manager.budget-requests.index') }}" class="btn btn-outline-secondary btn-lg">Annuler</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
