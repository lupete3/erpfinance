<?php

namespace App\Livewire\Bakery\Stock;

use App\Models\BakeryTransfer;
use App\Models\BakeryTransferItem;
use App\Models\Site;
use App\Models\StockBoulangerie;
use App\Models\StockPf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Transfert extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Form fields
    public $from_site_id, $to_site_id, $transfer_date, $notes;
    public $items = []; // Each item: ['stock_pf_id' => '', 'quantity' => '']
    
    // Filters for Report
    public $filter_from_site, $filter_to_site, $filter_start_date, $filter_end_date;

    public $isCreateMode = false;

    public function mount()
    {
        $this->transfer_date = now()->format('Y-m-d\TH:i');
        $this->addItem(); // Start with one empty item
    }

    public function addItem()
    {
        $this->items[] = ['stock_pf_id' => '', 'quantity' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function submitTransfer()
    {
        if (Auth::user()->role !== 'admin') {
            session()->flash('error', 'Action non autorisée.');
            return;
        }

        $this->validate([
            'from_site_id' => 'required|exists:sites,id',
            'to_site_id' => 'required|exists:sites,id|different:from_site_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.stock_pf_id' => 'required|exists:stock_pfs,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ], [
            'to_site_id.different' => 'Le site de destination doit être différent du site de départ.',
            'items.*.stock_pf_id.required' => 'Le produit est requis.',
            'items.*.quantity.required' => 'La quantité est requise.',
            'items.*.quantity.min' => 'La quantité doit être supérieure à 0.',
        ]);

        try {
            DB::beginTransaction();

            $transfer = BakeryTransfer::create([
                'from_site_id' => $this->from_site_id,
                'to_site_id' => $this->to_site_id,
                'user_id' => Auth::id(),
                'transfer_date' => $this->transfer_date,
                'notes' => $this->notes,
                'status' => 'completed',
            ]);

            foreach ($this->items as $item) {
                // 1. Save Transfer Item
                BakeryTransferItem::create([
                    'bakery_transfer_id' => $transfer->id,
                    'stock_pf_id' => $item['stock_pf_id'],
                    'quantity' => $item['quantity'],
                ]);

                // 2. Decrease Stock at Source
                $sourceStock = StockBoulangerie::where('site_id', '=', $this->from_site_id)
                    ->where('stock_pf_id', '=', $item['stock_pf_id'])
                    ->first(['*']);

                if ($sourceStock) {
                    $sourceStock->decrement('solde', $item['quantity']);
                } else {
                    // This case shouldn't happen if UI filters available products correctly, 
                    // but for safety we can create it with negative balance if allowed or throw error.
                    // Let's assume it should exist.
                    StockBoulangerie::create([
                        'site_id' => $this->from_site_id,
                        'stock_pf_id' => $item['stock_pf_id'],
                        'solde' => -$item['quantity'],
                    ]);
                }

                // 3. Increase Stock at Destination
                $destStock = StockBoulangerie::where('site_id', '=', $this->to_site_id)
                    ->where('stock_pf_id', '=', $item['stock_pf_id'])
                    ->first(['*']);

                if ($destStock) {
                    $destStock->increment('solde', $item['quantity']);
                } else {
                    StockBoulangerie::create([
                        'site_id' => $this->to_site_id,
                        'stock_pf_id' => $item['stock_pf_id'],
                        'solde' => $item['quantity'],
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', 'Transfert effectué avec succès.');
            $this->reset(['from_site_id', 'to_site_id', 'notes', 'items', 'isCreateMode']);
            $this->addItem();
            $this->transfer_date = now()->format('Y-m-d\TH:i');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors du transfert : ' . $e->getMessage());
        }
    }

    public function toggleCreateMode()
    {
        $this->isCreateMode = !$this->isCreateMode;
    }

    public function render()
    {
        $query = BakeryTransfer::with(['fromSite', 'toSite', 'user', 'items.product']);

        if ($this->filter_from_site) {
            $query->where('from_site_id', $this->filter_from_site);
        }
        if ($this->filter_to_site) {
            $query->where('to_site_id', $this->filter_to_site);
        }
        if ($this->filter_start_date) {
            $query->whereDate('transfer_date', '>=', $this->filter_start_date);
        }
        if ($this->filter_end_date) {
            $query->whereDate('transfer_date', '<=', $this->filter_end_date);
        }

        $transfers = $query->orderBy('transfer_date', 'DESC')->paginate(10);

        return view('livewire.bakery.stock.transfert', [
            'transfers' => $transfers,
            'sites' => Site::orderBy('nom', 'ASC')->get(['*']),
            'products' => StockPf::orderBy('designation', 'ASC')->get(['*']),
        ])->layout('components.layouts.app', ['title' => 'Transferts Inter-Sites']);
    }
}
