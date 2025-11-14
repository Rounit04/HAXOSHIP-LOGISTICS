<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    protected $fillable = [
        'pincode',
        'country',
        'zone',
        'network',
        'service',
        'status',
        'remark',
    ];
}
