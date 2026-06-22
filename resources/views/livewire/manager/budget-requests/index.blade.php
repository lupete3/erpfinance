<?php

use App\Models\BudgetRequest;
use Livewire\WithPagination;
use function Livewire\Volt\{state, uses, computed};

uses([WithPagination::class]);

state([
    'expandedId' => null,
]);

$requests = computed(function () {
    return BudgetRequest::where('store_id', Auth::user()->store_id)
        ->with('items')
        ->latest()
        ->paginate(10);
});

$toggleDetails = function ($id) {
    $this->expandedId = ($this->expandedId == $id) ? null : $id;
};

$delete = function ($id) {
    $req = BudgetRequest::where('store_id', Auth::user()->store_id)->findOrFail($id);
    if ($req->status !== 'en_attente') {
        notyf()->error(__('Vous ne pouvez pas supprimer une demande déjà traitée.'));
        return;
    }
    
    $req->items()->delete();
    $req->delete();
    
    notyf()->success(__('Demande supprimée avec succès.'));
};

?>

<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold py-3 mb-0">
            <span class="text-muted fw-light">Ma Succursale /</span> Mes États de Besoin
        </h4>
        <a href="{{ route('finance.manager.budget-requests.create') }}" class="btn btn-primary">
            <span class="tf-icons bx bx-plus"></span>&nbsp; Nouvelle demande
        </a>
    </div>

    <div class="card">
        <h5 class="card-header">Historique des demandes</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Objet</th>
                        <th>Montant Demandé</th>
                        <th>Montant Approuvé</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->requests as $req)
                        <tr class="{{ $expandedId == $req->id ? 'table-primary' : '' }}">
                            <td>{{ $req->created_at->format('d/m/Y') }}</td>
                            <td>
                                <strong class="text-primary">{{ $req->title }}</strong>
                                <button wire:click="toggleDetails({{ $req->id }})" class="btn btn-xs btn-outline-secondary ms-2">
                                    <i class="bx {{ $expandedId == $req->id ? 'bx-chevron-up' : 'bx-chevron-down' }}"></i> Détails
                                </button>
                            </td>
                            <td>{{ number_format($req->requested_amount, 2) }} {{ $req->currency }}</td>
                            <td>
                                @if($req->approved_amount)
                                    <span class="fw-bold text-success">{{ number_format($req->approved_amount, 2) }} {{ $req->currency }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($req->status == 'en_attente')
                                    <span class="badge bg-label-warning">En attente</span>
                                @elseif($req->status == 'approuvé')
                                    <span class="badge bg-label-success">Approuvé</span>
                                @elseif($req->status == 'rejeté')
                                    <span class="badge bg-label-danger">Rejeté</span>
                                @elseif($req->status == 'ajusté')
                                    <span class="badge bg-label-info">Ajusté</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 align-items-center">
                                    @if($req->status == 'en_attente')
                                        <a href="{{ route('finance.manager.budget-requests.edit', $req->id) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Modifier">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer" wire:click="delete({{ $req->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer cette demande ?">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    @endif
                                    @if($req->boss_note)
                                        <button class="btn btn-sm btn-icon btn-label-warning" title="{{ $req->boss_note }}">
                                            <i class="bx bx-comment-dots"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @if($expandedId == $req->id)
                            <tr class="table-light">
                                <td colspan="6">
                                    <div class="p-3">
                                        <table class="table table-sm table-bordered bg-white">
                                            <thead>
                                                <tr>
                                                    <th>Article / Description</th>
                                                    <th class="text-center">Qté</th>
                                                    <th class="text-end">Prix Unit.</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($req->items as $item)
                                                    <tr>
                                                        <td>{{ $item->description }}</td>
                                                        <td class="text-center">{{ number_format($item->quantity, 1) }}</td>
                                                        <td class="text-end">{{ number_format($item->unit_amount, 2) }}</td>
                                                        <td class="text-end">{{ number_format($item->total_amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Aucune demande soumise.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->requests->links() }}
        </div>
    </div>
</div>
