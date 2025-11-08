<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSendSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'send_on_booking',
        'send_on_delivery',
        'send_on_pickup',
        'send_on_status_update',
        'booking_template',
        'delivery_template',
        'pickup_template',
        'status_update_template',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'send_on_booking' => false,
            'send_on_delivery' => false,
            'send_on_pickup' => false,
            'send_on_status_update' => false,
        ]);
    }
}

