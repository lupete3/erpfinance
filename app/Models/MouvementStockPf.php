<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStockPf extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'stock_pf_id',
        'quantite',
        'reste_stock_pf',
        'reste_boulangerie',
        'site_id'
    ];

    public function stockPf(): BelongsTo
    {
        return $this->belongsTo(StockPf::class, 'stock_pf_id', 'id');
    }

    public function stockBoulangerie(): BelongsTo
    {
        return $this->belongsTo(StockBoulangerie::class, 'stock_pf_id', 'id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
