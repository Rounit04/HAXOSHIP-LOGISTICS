<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'opening_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
    ];
}
