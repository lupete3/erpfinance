<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommandeClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'paye',
        'reste',
        'ecart',
        'client_id',
        'observation',
        'site_id'
    ];

    public function ventes(): HasMany
    {
        return $this->hasMany(Vente::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function paiements(): HasMany
    {
        return $this->hasMany(PaiementClient::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }
}
