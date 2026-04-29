<?php

namespace App\Livewire\Bakery;

use App\Models\Caisse;
use App\Models\Client;
use App\Models\CommandeClient;
use App\Models\PaiementClient;
use App\Models\StockBoulangerie;
use App\Models\Vente as VenteModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Point de Vente (POS)')]
class Pos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $searchProduct = '';
    public $searchClient = '';

    // POS Mode: 'catalogue' or 'inventaire'
    public $posMode = 'catalogue';

    // POS State
    public $cart = []; // Array of ['id' => x, 'name' => y, 'price' => z, 'quantity' => w, 'stock_pf_id' => v]
    public $remains = []; // Array of [stock_id => quantity_remaining]
    public $selectedClientId;
    public $selectedSiteId;
    public $montantRecu = 0;
    public $observation = '';

    // Modals & Views
    public $isConfirming = false;

    protected $rules = [
        'selectedClientId' => 'required|exists:clients,id',
        'montantRecu' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $this->selectedSiteId = Auth::user()->site_id ?? 1;
    }

    public function addToCart($id)
    {
        $product = StockBoulangerie::with('stockProduitFinis')->find($id);

        if (!$product || $product->solde <= 0) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Produit non disponible.']);
            return;
        }

        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] >= $product->solde) {
                $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Stock maximum atteint.']);
                return;
            }
            $this->cart[$id]['quantity']++;
        } else {
            $this->cart[$id] = [
                'id' => $id,
                'stock_pf_id' => $product->stock_pf_id,
                'name' => $product->stockProduitFinis->designation,
                'price' => $product->stockProduitFinis->prix,
                'quantity' => 1,
            ];
        }
    }

    public function removeFromCart($id)
    {
        unset($this->cart[$id]);
    }

    public function increaseQty($id)
    {
        $product = StockBoulangerie::find($id);
        if ($this->cart[$id]['quantity'] < $product->solde) {
            $this->cart[$id]['quantity']++;
        }
    }

    public function decreaseQty($id)
    {
        if ($this->cart[$id]['quantity'] > 1) {
            $this->cart[$id]['quantity']--;
        } else {
            $this->removeFromCart($id);
        }
    }

    public function updateQty($id, $qty)
    {
        $qty = (float) $qty;
        if ($qty <= 0) {
            $this->removeFromCart($id);
            return;
        }

        $product = StockBoulangerie::find($id);
        if (!$product)
            return;

        if ($qty > $product->solde) {
            $this->dispatch('showAlert', ['type' => 'warning', 'message' => 'Stock insuffisant. Max disponible: ' . $product->solde]);
            $this->cart[$id]['quantity'] = $product->solde;
        } else {
            $this->cart[$id]['quantity'] = $qty;
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->remains = [];
    }

    /**
     * Handle inventory entry: quantite_vendue = stock_actuel - quantite_restante
     */
    public function updatedRemains($value, $id)
    {
        $id = (int) $id;
        $product = StockBoulangerie::with('stockProduitFinis')->find($id);

        if (!$product)
            return;

        $remaining = (float) $value;
        $sold = $product->solde - $remaining;

        if ($sold > 0) {
            $this->cart[$id] = [
                'id' => $id,
                'stock_pf_id' => $product->stock_pf_id,
                'name' => $product->stockProduitFinis->designation,
                'price' => $product->stockProduitFinis->prix,
                'quantity' => $sold,
            ];
        } else {
            unset($this->cart[$id]);
        }
    }

    public function switchMode($mode)
    {
        $this->posMode = $mode;
        // Optional: clear cart or remains when switching? 
        // User might want to combine modes, but usually it's one or the other.
        // For simplicity, we keep the cart.
    }

    public function getCartTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getResteProperty()
    {
        $montant = is_numeric($this->montantRecu) ? (float) $this->montantRecu : 0;
        $reste = $this->cartTotal - $montant;
        return $reste > 0 ? $reste : 0;
    }

    public function store()
    {
        $this->validate();

        if (empty($this->cart)) {
            $this->dispatch('showAlert', ['type' => 'error', 'message' => 'Le panier est vide.']);
            return;
        }

        $total = $this->cartTotal;
        $paye = is_numeric($this->montantRecu) ? (float) $this->montantRecu : 0;
        $reste = $this->reste;
        $site_id = $this->selectedSiteId;

        $commandeId = DB::transaction(function () use ($total, $paye, $reste, $site_id) {
            // 1. Create Commande
            $commande = CommandeClient::create([
                'montant' => $total,
                'paye' => $paye,
                'reste' => $reste,
                'ecart' => $total - ($paye + $reste),
                'client_id' => $this->selectedClientId,
                'observation' => $this->observation,
                'site_id' => $site_id,
            ]);

            // 2. Create Payment record if any amount paid
            if ($paye > 0) {
                PaiementClient::create([
                    'montant' => $paye,
                    'reste' => $reste,
                    'commande_client_id' => $commande->id,
                    'client_id' => $this->selectedClientId,
                ]);
            }

            // 3. Update Cash Register (Caisse)
            if ($paye > 0) {
                $dernierSolde = Caisse::latest()->value('solde_apres_operation') ?? 0;
                $client = Client::find($this->selectedClientId);
                $description = "Vente ({$commande->id}) client: " . ($client->nom ?? 'Anonyme');

                Caisse::create([
                    'type_operation' => 'entree',
                    'montant' => $paye,
                    'motif' => $description,
                    'solde_apres_operation' => $dernierSolde + $paye,
                    'user_id' => Auth::id(),
                ]);
            }

            // 4. Create Vente lines and update stock
            foreach ($this->cart as $id => $item) {
                $stockItem = StockBoulangerie::find($id);

                VenteModel::create([
                    'designation' => $item['name'],
                    'quantite' => $item['quantity'],
                    'prix' => $item['price'],
                    'reste' => $stockItem->solde - $item['quantity'],
                    'stock_pf_id' => $item['stock_pf_id'],
                    'commande_client_id' => $commande->id,
                ]);

                $stockItem->decrement('solde', $item['quantity']);
            }

            return $commande->id;
        });

        $this->dispatch('orderCreated', ['id' => $commandeId]);
        session()->flash('success', 'Vente enregistrée avec succès. Vous pouvez maintenant imprimer la facture.');
        $this->reset(['cart', 'remains', 'selectedClientId', 'montantRecu', 'observation']);
    }

    public function render()
    {
        $site_id = $this->selectedSiteId;

        $products = StockBoulangerie::with('stockProduitFinis')
            ->where('site_id', $site_id)
            ->whereHas('stockProduitFinis', function ($q) {
                $q->where('designation', 'like', '%' . $this->searchProduct . '%');
            })
            ->get();

        $clients = Client::where('nom', 'like', '%' . $this->searchClient . '%')
            ->orderBy('nom', 'ASC')
            ->get();

        $sites = \App\Models\Site::all();

        return view('livewire.bakery.pos', [
            'products' => $products,
            'clients' => $clients,
            'sites' => $sites,
        ]);
    }
}
