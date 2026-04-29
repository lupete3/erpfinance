<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Synthese extends Model
{
    use HasFactory;

    protected $table = 'syntheses'; // Explicit table name if model was lowercase in DB

    protected $fillable = [
        'vente',
        'avarie',
        'depense',
        'consommation',
        'dette',
        'change',
        'total',
        'espece',
        'manquant',
        'site_id',
        'user_id',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
