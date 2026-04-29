<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashWithdrawal extends Model
{
    protected $fillable = [
        'cash_register_id', 'user_id', 'amount', 'reason'
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
