<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'enabled',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'mailer' => 'smtp',
            'enabled' => false,
        ]);
    }
}

