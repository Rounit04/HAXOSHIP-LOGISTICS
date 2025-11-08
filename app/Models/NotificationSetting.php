<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'enabled',
        'show_popup',
        'show_dropdown',
        'polling_interval',
        'additional_settings',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'show_popup' => 'boolean',
        'show_dropdown' => 'boolean',
        'polling_interval' => 'integer',
        'additional_settings' => 'array',
    ];

    /**
     * Get or create default settings
     */
    public static function getSettings()
    {
        $settings = self::all()->keyBy('key');
        
        // If no settings exist, create defaults
        if ($settings->isEmpty()) {
            self::createDefaults();
            $settings = self::all()->keyBy('key');
        }
        
        return $settings;
    }

    /**
     * Get setting by key
     */
    public static function getSetting($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->enabled : ($default ?? true);
    }

    /**
     * Check if notification type is enabled
     */
    public static function isEnabled($key)
    {
        return self::getSetting($key, true);
    }

    /**
     * Create default notification settings
     */
    public static function createDefaults()
    {
        $defaults = [
            [
                'key' => 'user_login',
                'title' => 'User Login Notifications',
                'description' => 'Receive notifications when users log in to the system',
                'enabled' => true,
                'show_popup' => true,
                'show_dropdown' => true,
                'polling_interval' => 30,
            ],
            [
                'key' => 'role_assigned',
                'title' => 'Role Assignment Notifications',
                'description' => 'Receive notifications when roles are assigned or updated',
                'enabled' => true,
                'show_popup' => true,
                'show_dropdown' => true,
                'polling_interval' => 30,
            ],
            [
                'key' => 'order_updated',
                'title' => 'Order/Booking Update Notifications',
                'description' => 'Receive notifications when orders or bookings are updated',
                'enabled' => true,
                'show_popup' => true,
                'show_dropdown' => true,
                'polling_interval' => 30,
            ],
        ];

        foreach ($defaults as $default) {
            self::create($default);
        }
    }

    /**
     * Get global polling interval
     */
    public static function getPollingInterval()
    {
        $setting = self::where('key', 'user_login')->first();
        return $setting ? $setting->polling_interval : 30;
    }
}
