<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vente extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'quantite',
        'prix',
        'reste',
        'stock_pf_id',
        'commande_client_id',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(StockPf::class, 'stock_pf_id');
    }

    public function commandeClient(): BelongsTo
    {
        return $this->belongsTo(CommandeClient::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
