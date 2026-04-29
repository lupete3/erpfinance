<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Production extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'quantite',
        'charge_personnel',
        'autres_charges',
        'stock_pf_id',
    ];

    public function produitFinis(): BelongsTo
    {
        return $this->belongsTo(StockPf::class, 'stock_pf_id', 'id');
    }

    public function compositions(): HasMany
    {
        return $this->hasMany(Composition::class);
    }
}
