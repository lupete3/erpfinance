<?php

namespace App\Livewire\Bakery;

use App\Models\Fournisseur as FournisseurModel;
use Livewire\Component;
use Livewire\WithPagination;

class Fournisseur extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $nom, $telephone, $email;
    public $editingFournisseurId;
    public $isEditMode = false;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->nom = '';
        $this->telephone = '';
        $this->email = '';
        $this->editingFournisseurId = null;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetFields();
        $this->dispatch('openModal', ['id' => 'fournisseurModal']);
    }

    public function store()
    {
        $this->validate();

        FournisseurModel::create([
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Fournisseur ajouté avec succès.');
        $this->dispatch('closeModal', ['id' => 'fournisseurModal']);
        $this->resetFields();
    }

    public function edit($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->isEditMode = true;
        $this->editingFournisseurId = $id;
        $fournisseur = FournisseurModel::findOrFail($id);

        $this->nom = $fournisseur->nom;
        $this->telephone = $fournisseur->telephone;
        $this->email = $fournisseur->email;

        $this->dispatch('openModal', ['id' => 'fournisseurModal']);
    }

    public function update()
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $this->validate();

        $fournisseur = FournisseurModel::findOrFail($this->editingFournisseurId);
        $fournisseur->update([
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'email' => $this->email,
        ]);

        session()->flash('success', 'Fournisseur mis à jour avec succès.');
        $this->dispatch('closeModal', ['id' => 'fournisseurModal']);
        $this->resetFields();
    }

    public function delete($id)
    {
        if (in_array(auth()->user()->role, ['geran_depot_usine', 'geran_depot_magasin', 'geran_depot_boulangerie'])) {
            session()->flash('error', 'Action non autorisée pour votre rôle.');
            return;
        }

        $fournisseur = FournisseurModel::findOrFail($id);
        $fournisseur->delete();
        session()->flash('success', 'Fournisseur supprimé avec succès.');
    }

    public function render()
    {
        $fournisseurs = FournisseurModel::where('nom', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('livewire.bakery.fournisseur', [
            'fournisseurs' => $fournisseurs,
        ])->layout('components.layouts.app', ['title' => 'Gestion des Fournisseurs']);
    }
}
