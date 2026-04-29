<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function mouvementStockPfs(): HasMany
    {
        return $this->hasMany(MouvementStockPf::class);
    }

    public function stockBoulangeries(): HasMany
    {
        return $this->hasMany(StockBoulangerie::class);
    }

    public function commandeClients(): HasMany
    {
        return $this->hasMany(CommandeClient::class);
    }

    public function inventaires(): HasMany
    {
        return $this->hasMany(Cloture::class);
    }

    public function syntheses(): HasMany
    {
        return $this->hasMany(Synthese::class);
    }
}
