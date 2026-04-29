<?php

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Dotation;
use function Livewire\Volt\{state, rules, computed};

state([
    'expense_category_id' => '',
    'amount' => '',
    'currency' => 'USD',
    'expense_date' => date('Y-m-d'),
    'beneficiary' => '',
    'description' => '',
    'reference' => '',
    'categories' => fn() => ExpenseCategory::all(),
]);

$balance = computed(function () {
    $totalDotations = Dotation::where('store_id', Auth::user()->store_id)
        ->where('currency', $this->currency)
        ->sum('amount');
        
    $totalExpenses = Expense::where('store_id', Auth::user()->store_id)
        ->where('currency', $this->currency)
        ->sum('amount');
        
    return $totalDotations - $totalExpenses;
});

rules([
    'expense_category_id' => 'required|exists:expense_categories,id',
    'amount' => 'required|numeric|min:0.01',
    'currency' => 'required|in:USD,CDF',
    'expense_date' => 'required|date',
    'beneficiary' => 'required|min:3',
    'description' => 'nullable',
    'reference' => 'nullable',
]);

$save = function () {
    $this->validate();

    if ($this->amount > $this->balance) {
        notyf()->error(__('Solde insuffisant ! Vous ne pouvez pas dépenser plus que ce que vous avez en caisse (Disponible : ' . number_format($this->balance, 2) . ' ' . $this->currency . ').'));
        return;
    }

    Expense::create([
        'tenant_id' => Auth::user()->tenant_id,
        'store_id' => Auth::user()->store_id,
        'user_id' => Auth::id(),
        'expense_category_id' => $this->expense_category_id,
        'amount' => $this->amount,
        'currency' => $this->currency,
        'expense_date' => $this->expense_date,
        'beneficiary' => $this->beneficiary,
        'description' => $this->description,
        'reference' => $this->reference,
        'status' => 'validé',
    ]);

    notyf()->success(__('Dépense enregistrée avec succès.'));

    return redirect()->route('finance.manager.expenses.index');
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Dépenses /</span> Nouvelle saisie
    </h4>

    <div class="row">
        <div class="col-md-7">
            <div class="card mb-4">
                <h5 class="card-header">Détails de la dépense</h5>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="mb-3">
                            <label class="form-label" for="currency">Devise de la dépense</label>
                            <select class="form-select" id="currency" wire:model.live="currency">
                                <option value="USD">USD - Dollars Américains</option>
                                <option value="CDF">CDF - Francs Congolais</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="expense_category_id">Catégorie de dépense</label>
                            <select class="form-select @error('expense_category_id') is-invalid @enderror" id="expense_category_id" wire:model="expense_category_id">
                                <option value="">Sélectionnez une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('expense_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="beneficiary">Bénéficiaire (Payé à qui ?)</label>
                            <input type="text" class="form-control @error('beneficiary') is-invalid @enderror" id="beneficiary" wire:model="beneficiary" placeholder="Ex: Fournisseur X, Nom de l'employé..." />
                            @error('beneficiary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="amount">Montant</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" wire:model="amount" placeholder="0.00" />
                                <span class="input-group-text">{{ $currency }}</span>
                            </div>
                            @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="expense_date">Date de dépense</label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" wire:model="expense_date" />
                            @error('expense_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="reference">Référence / N° Pièce (Optionnel)</label>
                            <input type="text" class="form-control" id="reference" wire:model="reference" placeholder="Laissé vide pour génération auto" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="description">Motif détaillé</label>
                            <textarea class="form-control" id="description" wire:model="description" rows="2" placeholder="Ex: Achat de petit matériel, transport..."></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer la dépense</button>
                            <a href="{{ route('finance.manager.expenses.index') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card bg-label-info mb-4">
                <div class="card-body text-center">
                    <h5 class="card-title text-info mb-1">Solde disponible en {{ $currency }}</h5>
                    <h3 class="fw-bold text-info mb-1">{{ number_format($this->balance, 2) }} {{ $currency }}</h3>
                    <p class="mb-0 small text-muted">Ce montant est spécifique à la devise sélectionnée.</p>
                </div>
            </div>

            <div class="alert alert-warning">
                <i class="bx bx-error-circle me-1"></i>
                Le solde est recalculé automatiquement dès que vous changez de devise.
            </div>
        </div>
    </div>
</div>
