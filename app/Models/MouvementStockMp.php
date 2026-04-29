<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStockMp extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_stock_mp',
        'quantite',
        'reste_maison',
        'reste_usine',
        'statut'
    ];

    public function stockMaison(): BelongsTo
    {
        return $this->belongsTo(StockMaison::class, 'id_stock_mp', 'id');
    }

    public function stockUsine(): BelongsTo
    {
        return $this->belongsTo(StockUsine::class, 'id_stock_mp', 'id');
    }
}
