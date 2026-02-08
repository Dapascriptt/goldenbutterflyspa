<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Omset extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'amount',
        'created_by',
        'description',
        'code',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
