<?php

use App\Models\Dotation;
use App\Models\Store;
use function Livewire\Volt\{state, computed};

state([
    'selected_store_id' => fn() => Auth::user()->store_id,
    'stores' => fn() => Auth::user()->hasRoleString('Boss') ? Store::all() : [],
]);

$dotations = computed(function () {
    $storeId = Auth::user()->hasRoleString('Boss') ? $this->selected_store_id : Auth::user()->store_id;

    return Dotation::query()
        ->when($storeId, fn($q) => $q->where('store_id', $storeId))
        ->with('store')
        ->latest()
        ->get();
});

?>

<div>
    <div class="d-flex align-items-center justify-content-between">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Supervision /</span> Dotations Reçues
        </h4>
        <a href="{{ route('finance.manager.dotations.create') }}" class="btn btn-primary mb-4">
            <i class="bx bx-plus me-1"></i> Enregistrer Dotation
        </a>
    </div>

    @if(Auth::user()->hasRoleString('Boss'))
        <div class="card mb-4">
            <div class="card-body text-nowrap">
                <label class="form-label">Filtrer par Succursale (Vue Boss)</label>
                <select class="form-select w-auto" wire:model.live="selected_store_id">
                    <option value="">Toutes les succursales</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Historique des fonds</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Succursale</th>
                        <th>Montant</th>
                        <th>Référence</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->dotations as $dotation)
                        <tr>
                            <td>{{ $dotation->date_dotation }}</td>
                            <td><small>{{ $dotation->store->name }}</small></td>
                            <td>
                                <span class="fw-bold text-success">
                                    {{ number_format($dotation->amount, 2) }} {{ $dotation->currency }}
                                </span>
                            </td>
                            <td><small>{{ $dotation->reference ?? '-' }}</small></td>
                            <td>{{ $dotation->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">Aucune dotation trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
