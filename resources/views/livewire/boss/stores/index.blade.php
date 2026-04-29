<?php

use App\Models\Store;
use function Livewire\Volt\{state, on};

state(['stores' => fn () => Store::all()]);

$deleteStore = function (Store $store) {
    $store->delete();
    $this->stores = Store::all();
    notyf()->success(__('Succursale supprimée avec succès.'));
};

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Finance /</span> Succursales
        </h4>
        <a href="{{ route('finance.boss.stores.create') }}" class="btn btn-primary">
            <span class="tf-icons bx bx-plus"></span>&nbsp; Nouvelle Succursale
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Toutes les succursales</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Emplacement</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($stores as $store)
                        <tr>
                            <td><strong>{{ $store->name }}</strong></td>
                            <td>{{ $store->location }}</td>
                            <td>{{ $store->phone }}</td>
                            <td>{{ $store->email }}</td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('finance.boss.stores.edit', $store->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <button class="dropdown-item text-danger" wire:click="deleteStore({{ $store->id }})" wire:confirm="Supprimer cette succursale ?">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucune succursale trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
