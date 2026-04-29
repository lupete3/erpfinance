<?php

use App\Models\User;
use App\Models\Role;
use function Livewire\Volt\{state, on};

state(['managers' => function () {
    $gerantRole = Role::where('name', 'Gérant')->first();
    return User::where('tenant_id', Auth::user()->tenant_id)
               ->where('role_id', $gerantRole->id)
               ->with('store')
               ->get();
}]);

$deleteManager = function (User $manager) {
    $manager->delete();
    $this->managers = User::where('tenant_id', Auth::user()->tenant_id)
                           ->where('role_id', Role::where('name', 'Gérant')->first()->id)
                           ->get();
    notyf()->success(__('Gérant supprimé avec succès.'));
};

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Gestion /</span> Gérants des Succursales
        </h4>
        <a href="{{ route('finance.boss.managers.create') }}" class="btn btn-primary">
            <span class="tf-icons bx bx-user-plus"></span>&nbsp; Nouveau Gérant
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Liste des Gérants</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Succursale</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($managers as $manager)
                        <tr>
                            <td><strong>{{ $manager->name }}</strong></td>
                            <td>{{ $manager->email }}</td>
                            <td>
                                @if($manager->store)
                                    <span class="badge bg-label-info">{{ $manager->store->name }}</span>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                @if($manager->is_active)
                                    <span class="badge bg-label-success">Actif</span>
                                @else
                                    <span class="badge bg-label-danger">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('finance.boss.managers.edit', $manager->id) }}">
                                            <i class="bx bx-edit-alt me-1"></i> Modifier
                                        </a>
                                        <button class="dropdown-item text-danger" wire:click="deleteManager({{ $manager->id }})" wire:confirm="Supprimer ce gérant ?">
                                            <i class="bx bx-trash me-1"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucun gérant trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
