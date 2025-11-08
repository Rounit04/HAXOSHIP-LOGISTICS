<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GooglemapSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_key',
        'enabled',
        'map_type',
        'zoom_level',
    ];

    public static function getSettings()
    {
        return static::first() ?? static::create([
            'enabled' => false,
            'map_type' => 'roadmap',
            'zoom_level' => 10,
        ]);
    }
}

