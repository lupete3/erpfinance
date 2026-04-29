<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // 🔗 Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }


    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)
            ->where('is_active', true)
            ->whereDate('end_date', '>=', now());
    }
}
