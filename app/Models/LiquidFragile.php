<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiquidFragile extends Model
{
    use HasFactory;

    protected $table = 'liquid_fragile';

    protected $fillable = [
        'name',
        'type',
        'additional_charge',
        'description',
        'status',
    ];
}

