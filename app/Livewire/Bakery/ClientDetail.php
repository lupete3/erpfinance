<?php

namespace App\Livewire\Bakery;

use App\Models\Client;
use App\Models\CommandeClient;
use App\Models\PaiementClient;
use Livewire\Component;
use Livewire\WithPagination;

class ClientDetail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Client $client;
    public $start_date, $end_date;
    
    // Summary data
    public $total_achats = 0;
    public $total_paye = 0;
    public $total_dette = 0;

    public function mount(Client $client)
    {
        $this->client = $client;
        $this->updateSummary();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['start_date', 'end_date'])) {
            $this->resetPage('commandsPage');
            $this->resetPage('paymentsPage');
            $this->updateSummary();
        }
    }

    public function updateSummary()
    {
        $query = CommandeClient::where('client_id', '=', $this->client->id);

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        $this->total_achats = $query->sum('montant');
        $this->total_paye = $query->sum('paye');
        $this->total_dette = $query->sum('reste');
    }

    public function render()
    {
        $commandsQuery = CommandeClient::with(['ventes.product'])
            ->where('client_id', '=', $this->client->id);

        $paymentsQuery = PaiementClient::with(['commandeClient'])
            ->where('client_id', '=', $this->client->id);

        if ($this->start_date) {
            $commandsQuery->whereDate('created_at', '>=', $this->start_date);
            $paymentsQuery->whereDate('created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $commandsQuery->whereDate('created_at', '<=', $this->end_date);
            $paymentsQuery->whereDate('created_at', '<=', $this->end_date);
        }

        return view('livewire.bakery.client-detail', [
            'commands' => $commandsQuery->orderBy('created_at', 'DESC')->paginate(10, ['*'], 'commandsPage'),
            'payments' => $paymentsQuery->orderBy('created_at', 'DESC')->paginate(10, ['*'], 'paymentsPage'),
        ])->layout('components.layouts.app', ['title' => 'Fiche Client : ' . $this->client->nom]);
    }
}
