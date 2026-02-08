<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OmsetDailySummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'total_amount',
        'total_entries',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
