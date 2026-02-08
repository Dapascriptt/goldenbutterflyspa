<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TherapistCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'therapist_name',
        'extra_time',
        'extra_charge',
        'traditional',
        'fullbody',
        'butterfly',
        'shockwave',
        'discount_percent',
        'discount_nominal',
        'room_charge',
        'total_charge',
        'room',
    ];

    protected $casts = [
        'date' => 'date',
        'shockwave' => 'boolean',
    ];
}
