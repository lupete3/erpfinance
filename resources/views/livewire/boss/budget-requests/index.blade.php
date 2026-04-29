<?php

use App\Models\BudgetRequest;
use Livewire\WithPagination;
use function Livewire\Volt\{state, uses, computed};

uses([WithPagination::class]);

state([
    'expandedId' => null,
]);

$requests = computed(function () {
    return BudgetRequest::with(['store', 'user', 'items'])
        ->latest()
        ->paginate(15);
});

$toggleDetails = function ($id) {
    $this->expandedId = ($this->expandedId == $id) ? null : $id;
};

$approve = function ($id) {
    $req = BudgetRequest::findOrFail($id);
    $req->update([
        'status' => 'approuvé',
        'approved_amount' => $req->requested_amount
    ]);
    notyf()->success(__('Demande approuvée.'));
};

$reject = function ($id, $note = '') {
    $req = BudgetRequest::findOrFail($id);
    $req->update([
        'status' => 'rejeté',
        'boss_note' => $note
    ]);
    notyf()->error(__('Demande rejetée.'));
};

$adjust = function ($id, $newAmount, $note = '') {
    $req = BudgetRequest::findOrFail($id);
    $req->update([
        'status' => 'ajusté',
        'approved_amount' => $newAmount,
        'boss_note' => $note
    ]);
    notyf()->info(__('Demande ajustée.'));
};

?>

<div>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Validation /</span> États de Besoin Détaillés
    </h4>

    <div class="card">
        <h5 class="card-header">Toutes les demandes</h5>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Succursale</th>
                        <th>Objet global</th>
                        <th>Montant Demandé</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($this->requests as $req)
                        <tr class="{{ $expandedId == $req->id ? 'table-primary' : '' }}">
                            <td>{{ $req->created_at->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-label-info">{{ $req->store->name }}</span>
                                <small class="d-block text-muted">{{ $req->user->name }}</small>
                            </td>
                            <td>
                                <strong class="text-primary">{{ $req->title }}</strong>
                                <button wire:click="toggleDetails({{ $req->id }})" class="btn btn-xs btn-outline-secondary ms-2">
                                    <i class="bx {{ $expandedId == $req->id ? 'bx-chevron-up' : 'bx-chevron-down' }}"></i> Détails ({{ $req->items->count() }})
                                </button>
                            </td>
                            <td>
                                <span class="fw-bold fs-5">{{ number_format($req->requested_amount, 2) }} {{ $req->currency }}</span>
                                @if($req->approved_amount && $req->status != 'approuvé')
                                    <div class="text-success small fw-bold">Accordé : {{ number_format($req->approved_amount, 2) }}</div>
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
                                @if($req->status == 'en_attente')
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-success" title="Approuver" wire:click="approve({{ $req->id }})" wire:confirm="Approuver cette demande ?">
                                            <i class="bx bx-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-info" title="Ajuster" onclick="promptAdjust({{ $req->id }}, {{ $req->requested_amount }})">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" title="Rejeter" onclick="promptReject({{ $req->id }})">
                                            <i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                @else
                                    <small class="text-muted">Traité</small>
                                @endif
                            </td>
                        </tr>
                        @if($expandedId == $req->id)
                            <tr class="table-light">
                                <td colspan="6">
                                    <div class="p-3">
                                        <h6 class="fw-bold mb-3">Détail des lignes demandées :</h6>
                                        <table class="table table-sm table-bordered bg-white">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="text-center">Qté</th>
                                                    <th class="text-end">Prix Unit.</th>
                                                    <th class="text-end">Sous-total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($req->items as $item)
                                                    <tr>
                                                        <td>{{ $item->description }}</td>
                                                        <td class="text-center">{{ number_format($item->quantity, 1) }}</td>
                                                        <td class="text-end">{{ number_format($item->unit_amount, 2) }}</td>
                                                        <td class="text-end fw-bold">{{ number_format($item->total_amount, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-secondary fw-bold">
                                                    <td colspan="3" class="text-end">TOTAL CALCULÉ :</td>
                                                    <td class="text-end text-primary">{{ number_format($req->requested_amount, 2) }} {{ $req->currency }}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                        @if($req->description)
                                            <div class="mt-3">
                                                <strong>Note du gérant :</strong>
                                                <p class="mb-0 italic">{{ $req->description }}</p>
                                            </div>
                                        @endif
                                        @if($req->boss_note)
                                            <div class="mt-2 alert alert-warning py-2 mb-0">
                                                <strong>Votre note :</strong> {{ $req->boss_note }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="6" class="text-center py-4">Aucune demande à traiter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $this->requests->links() }}
        </div>
    </div>

    <script>
        function promptAdjust(id, current) {
            let amount = prompt("Entrez le montant ajusté à accorder :", current);
            if (amount != null && amount != "") {
                let note = prompt("Note / Justification de l'ajustement (Optionnel) :");
                @this.adjust(id, amount, note);
            }
        }

        function promptReject(id) {
            let note = prompt("Motif du rejet (Optionnel) :");
            if (note != null) {
                @this.reject(id, note);
            }
        }
    </script>
</div>
