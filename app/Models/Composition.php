<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Composition extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'unite',
        'quantite',
        'prix',
        'stock_usine_id',
        'production_id'
    ];

    public function production(): BelongsTo
    {
        return $this->belongsTo(Production::class);
    }
}
