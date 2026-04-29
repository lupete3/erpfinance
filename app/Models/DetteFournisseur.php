<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetteFournisseur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_fournisseur',
        'id_achat',
        'montant_dette',
        'reste_a_payer',
        'est_soldee',
    ];

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class, 'id_fournisseur');
    }

    public function achat()
    {
        return $this->belongsTo(AchatStockMaison::class, 'id_achat');
    }

}
