<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cloture extends Model
{
    use HasFactory;

    protected $fillable = [
        'qnte_entree',
        'qnte_sortie',
        'avarie',
        'consommation',
        'prix',
        'solde',
        'stock_pf_id',
        'site_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockProduitFinis(): BelongsTo
    {
        return $this->belongsTo(StockBoulangerie::class, 'stock_pf_id', 'id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
