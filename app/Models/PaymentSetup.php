<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetup extends Model
{
    use HasFactory;

    protected $table = 'payment_setup';

    protected $fillable = [
        'gateway_name',
        'gateway_type',
        'api_key',
        'api_secret',
        'merchant_id',
        'enabled',
        'test_mode',
        'description',
    ];
}

