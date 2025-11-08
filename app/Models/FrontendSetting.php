<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrontendSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'banner',
        'primary_color',
        'secondary_color',
        'text_color',
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'footer_logo',
        'footer_description',
        'footer_facebook_url',
        'footer_instagram_url',
        'footer_twitter_url',
        'footer_skype_url',
        'footer_google_play_url',
        'footer_app_store_url',
        'footer_copyright_text',
        'about_us_content',
        'services_section_title',
        'services_section_content',
        'why_haxo_section_title',
        'why_haxo_section_content',
        'pricing_section_title',
        'pricing_section_content',
        'stats_section_content',
        'gdpr_cookie_enabled',
        'gdpr_cookie_message',
        'gdpr_cookie_button_text',
        'gdpr_cookie_decline_text',
        'gdpr_cookie_settings_text',
        'gdpr_cookie_position',
        'gdpr_cookie_bg_color',
        'gdpr_cookie_text_color',
        'gdpr_cookie_button_color',
        'gdpr_cookie_expiry_days',
        'contact_email',
        'contact_phone',
        'contact_address',
    ];

    /**
     * Get the current frontend settings or create default
     */
    public static function getSettings()
    {
        $settings = self::first();
        if (!$settings) {
            $settings = self::create([
                'primary_color' => '#FF750F',
                'secondary_color' => '#ff8c3a',
                'text_color' => '#1b1b18',
                'hero_title' => 'Hassle Free Fastest Delivery',
                'hero_subtitle' => 'We Committed to delivery - Make easy Efficient and quality delivery.',
                'hero_button_text' => 'Track Now',
                'footer_description' => 'Fastest platform with all courier service features. Help you start, run and grow your courier service.',
                'footer_copyright_text' => 'Copyright © All rights reserved. Development by Hexoship',
            ]);
        }
        return $settings;
    }
}
