<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'telephone',
        'email',
    ];

    public function achatStockMaisons(): HasMany
    {
        return $this->hasMany(AchatStockMaison::class);
    }

    public function dettes()
    {
        return $this->hasMany(DetteFournisseur::class, 'id_fournisseur');
    }
}
