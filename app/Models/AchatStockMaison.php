<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AchatStockMaison extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'prix_achat',
        'quantite',
        'montant_paye',
        'id_fournisseur',
        'id_stock_maisons'
    ];

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class, 'id_fournisseur', 'id');
    }

    public function stockMaison(): BelongsTo
    {
        return $this->belongsTo(StockMaison::class, 'id_stock_maisons', 'id');
    }
}
