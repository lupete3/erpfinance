<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BakeryTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_site_id',
        'to_site_id',
        'user_id',
        'transfer_date',
        'status',
        'notes',
    ];

    public function fromSite()
    {
        return $this->belongsTo(Site::class, 'from_site_id');
    }

    public function toSite()
    {
        return $this->belongsTo(Site::class, 'to_site_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(BakeryTransferItem::class);
    }
}
