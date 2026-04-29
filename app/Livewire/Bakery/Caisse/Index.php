<?php

namespace App\Livewire\Bakery\Caisse;

use App\Models\Caisse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $type_operation = 'entree';
    public $montant;
    public $motif;

    public $isEditMode = false;
    public $viewingDetailsId;

    protected $rules = [
        'type_operation' => 'required|in:entree,sortie',
        'montant' => 'required|numeric|min:0',
        'motif' => 'required|string|min:3',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function store()
    {
        $this->validate();

        DB::transaction(function () {
            // Get last balance
            $dernierSolde = Caisse::latest('id')->value('solde_apres_operation') ?? 0;
            
            $nouveauSolde = $this->type_operation === 'entree' 
                ? (float)$dernierSolde + (float)$this->montant 
                : (float)$dernierSolde - (float)$this->montant;

            Caisse::create([
                'type_operation' => $this->type_operation,
                'montant' => $this->montant,
                'motif' => $this->motif,
                'solde_apres_operation' => $nouveauSolde,
                'user_id' => Auth::id(),
            ]);
        });

        session()->flash('success', 'Opération de caisse enregistrée avec succès.');
        $this->dispatch('closeModal', ['id' => 'caisseModal']);
        $this->resetFields();
    }

    public function deleteOperation($id)
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        DB::transaction(function () use ($id) {
            Caisse::find($id)?->delete();
            $this->recalculerSoldes();
        });

        session()->flash('success', 'Opération supprimée et soldes recalculés.');
    }

    public function recalculerSoldes()
    {
        $solde = 0;
        $operations = Caisse::orderBy('id', 'asc')->get();

        foreach ($operations as $op) {
            if ($op->type_operation === 'entree') {
                $solde += $op->montant;
            } else {
                $solde -= $op->montant;
            }
            $op->update(['solde_apres_operation' => $solde]);
        }
    }

    public function resetFields()
    {
        $this->type_operation = 'entree';
        $this->montant = null;
        $this->motif = null;
        $this->isEditMode = false;
    }

    public function render()
    {
        $caisses = Caisse::with('user')
            ->where('motif', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(15);

        $currentSolde = Caisse::latest('id')->value('solde_apres_operation') ?? 0;

        return view('livewire.bakery.caisse.index', [
            'caisses' => $caisses,
            'currentSolde' => $currentSolde
        ])->layout('components.layouts.app', ['title' => 'Gestion de la Caisse']);
    }
}
