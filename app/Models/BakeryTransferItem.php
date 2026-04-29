<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BakeryTransferItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'bakery_transfer_id',
        'stock_pf_id',
        'quantity',
    ];

    public function transfer()
    {
        return $this->belongsTo(BakeryTransfer::class, 'bakery_transfer_id');
    }

    public function product()
    {
        return $this->belongsTo(StockPf::class, 'stock_pf_id');
    }
}
