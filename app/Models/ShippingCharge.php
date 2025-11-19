<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCharge extends Model
{
    protected $fillable = [
        'origin',
        'origin_zone',
        'destination',
        'destination_zone',
        'shipment_type',
        'min_weight',
        'max_weight',
        'network',
        'service',
        'rate',
        'remark',
    ];

    protected $casts = [
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'rate' => 'decimal:2',
    ];
}

