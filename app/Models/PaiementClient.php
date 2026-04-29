<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaiementClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'reste',
        'commande_client_id',
        'client_id',
        'site_id',
    ];

    public function commandeClient(): BelongsTo
    {
        return $this->belongsTo(CommandeClient::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
