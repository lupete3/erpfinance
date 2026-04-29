<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockUsine extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_stock_maisons',
        'solde'
    ];

    public function mouvementsSorties(): HasMany
    {
        return $this->hasMany(MouvementStockMp::class);
    }

    public function stockMaison(): BelongsTo
    {
        return $this->belongsTo(StockMaison::class, 'id_stock_maisons', 'id');
    }
}
