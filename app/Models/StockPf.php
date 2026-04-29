<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockPf extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'prix',
        'solde',
    ];

    public function productions(): HasMany
    {
        return $this->hasMany(Production::class);
    }

    public function stockBoulangerie(): HasMany
    {
        return $this->hasMany(StockBoulangerie::class);
    }
}
