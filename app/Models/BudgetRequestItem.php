<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_request_id',
        'description',
        'quantity',
        'unit_amount',
        'total_amount',
    ];

    public function budgetRequest(): BelongsTo
    {
        return $this->belongsTo(BudgetRequest::class);
    }
}
