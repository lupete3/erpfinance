<?php

namespace App\Livewire\Bakery;

use App\Models\Client as ClientModel;
use Livewire\Component;
use Livewire\WithPagination;

class Client extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $nom, $telephone, $adresse;
    public $editingClientId;
    public $isEditMode = false;

    protected $rules = [
        'nom' => 'required|string|max:255',
        'telephone' => 'nullable|string|max:20',
        'adresse' => 'nullable|string|max:500',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function resetFields()
    {
        $this->nom = '';
        $this->telephone = '';
        $this->adresse = '';
        $this->editingClientId = null;
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetFields();
        $this->dispatch('openModal', ['id' => 'clientModal']);
    }

    public function store()
    {
        $this->validate();

        ClientModel::create([
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
        ]);

        session()->flash('success', 'Client ajouté avec succès.');
        $this->dispatch('closeModal', ['id' => 'clientModal']);
        $this->resetFields();
    }

    public function edit($id)
    {
        $this->isEditMode = true;
        $this->editingClientId = $id;
        $client = ClientModel::findOrFail($id);

        $this->nom = $client->nom;
        $this->telephone = $client->telephone;
        $this->adresse = $client->adresse;

        $this->dispatch('openModal', ['id' => 'clientModal']);
    }

    public function update()
    {
        $this->validate();

        $client = ClientModel::findOrFail($this->editingClientId);
        $client->update([
            'nom' => $this->nom,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
        ]);

        session()->flash('success', 'Client mis à jour avec succès.');
        $this->dispatch('closeModal', ['id' => 'clientModal']);
        $this->resetFields();
    }

    public function delete($id)
    {
        $client = ClientModel::findOrFail($id);
        // On pourrait vérifier si le client a des ventes avant de supprimer
        $client->delete();
        session()->flash('success', 'Client supprimé avec succès.');
    }

    public function render()
    {
        $clients = ClientModel::where(function ($query) {
            $query->where('nom', 'like', '%' . $this->search . '%')
                ->orWhere('telephone', 'like', '%' . $this->search . '%');
        })
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return view('livewire.bakery.client', [
            'clients' => $clients,
        ])->layout('components.layouts.app', ['title' => 'Gestion des Clients']);
    }
}
