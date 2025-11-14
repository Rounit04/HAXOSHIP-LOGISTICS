<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentIntoBank extends Model
{
    protected $table = 'payments_into_bank';

    protected $fillable = [
        'bank_account',
        'mode_of_payment',
        'type',
        'category_bank',
        'transaction_no',
        'amount',
        'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];
}

