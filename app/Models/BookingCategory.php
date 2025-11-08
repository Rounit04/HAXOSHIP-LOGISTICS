<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'requires_awb',
        'status',
    ];

    protected $casts = [
        'requires_awb' => 'boolean',
    ];
}
