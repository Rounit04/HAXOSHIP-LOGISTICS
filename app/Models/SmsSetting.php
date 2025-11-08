<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'api_key',
        'api_secret',
        'sender_id',
        'enabled',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'provider' => 'twilio',
            'enabled' => false,
        ]);
    }
}

