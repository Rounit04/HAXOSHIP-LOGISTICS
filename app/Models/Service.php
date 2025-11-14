<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'network',
        'transit_time',
        'items_allowed',
        'status',
        'remark',
        'display_title',
        'description',
        'icon_type',
        'is_highlighted',
    ];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];
}
