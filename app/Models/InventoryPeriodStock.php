<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryPeriodStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'period_year',
        'period_month',
        'stock_awal',
        'stock_akhir',
    ];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
