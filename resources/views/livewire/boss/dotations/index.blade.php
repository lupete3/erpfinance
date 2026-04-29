<?php

use App\Models\Dotation;
use Livewire\WithPagination;
use function Livewire\Volt\{state, uses, computed};

uses([WithPagination::class]);

$dotations = computed(function () {
    return Dotation::with('store')->latest()->paginate(10);
});

$deleteDotation = function (Dotation $dotation) {
    if (!Auth::user()->hasRoleString('Boss')) {
        notyf()->error(__('Action non autorisée.'));
        return;
    }

    if ($dotation->created_at->diffInHours(now()) > 24) {
        notyf()->warning(__('Cette dotation est ancienne.'));
    }

    $dotation->delete();
    notyf()->success(__('Dotation supprimée.'));
};

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Finance /</span> Dotations (Envois de fonds)
        </h4>
        <a href="{{ route('finance.boss.dotations.create') }}" class="btn btn-primary">
            <span class="tf-icons bx bx-send"></span>&nbsp; Nouvelle Dotation
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Historique des dotations</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Succursale</th>
                        <th>Montant</th>
                        <th>Référence</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->dotations as $dotation)
                        @php $isOld = $dotation->created_at->diffInHours(now()) > 24; @endphp
                        <tr>
                            <td>
                                {{ $dotation->date_dotation }}
                                @if($isOld) <i class="bx bx-history text-muted"></i> @endif
                            </td>
                            <td><span class="badge bg-label-info">{{ $dotation->store->name }}</span></td>
                            <td><span class="fw-bold text-success">{{ number_format($dotation->amount, 2) }} {{ $dotation->currency }}</span></td>
                            <td>{{ $dotation->reference ?? '-' }}</td>
                            <td>
                                <a href="{{ route('finance.boss.dotations.edit', $dotation->id) }}" class="btn btn-sm btn-label-warning">
                                    <i class="bx bx-edit-alt"></i>
                                </a>
                                <button class="btn btn-sm btn-label-danger" wire:click="deleteDotation({{ $dotation->id }})" wire:confirm="Supprimer ?">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4">Aucune dotation.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->dotations->links() }}
        </div>
    </div>
</div>
