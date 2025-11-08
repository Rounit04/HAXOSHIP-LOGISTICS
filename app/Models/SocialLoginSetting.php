<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialLoginSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'google_enabled',
        'google_client_id',
        'google_client_secret',
        'facebook_enabled',
        'facebook_app_id',
        'facebook_app_secret',
        'twitter_enabled',
        'twitter_client_id',
        'twitter_client_secret',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'google_enabled' => false,
            'facebook_enabled' => false,
            'twitter_enabled' => false,
        ]);
    }
}

