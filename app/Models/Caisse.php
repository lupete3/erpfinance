<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_operation',
        'montant',
        'motif',
        'solde_apres_operation',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
