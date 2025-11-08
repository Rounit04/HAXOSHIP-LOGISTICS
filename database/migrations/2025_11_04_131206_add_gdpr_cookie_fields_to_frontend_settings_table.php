<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('frontend_settings', function (Blueprint $table) {
            $table->boolean('gdpr_cookie_enabled')->default(false)->after('text_color');
            $table->text('gdpr_cookie_message')->nullable()->after('gdpr_cookie_enabled');
            $table->string('gdpr_cookie_button_text')->default('Accept All')->after('gdpr_cookie_message');
            $table->string('gdpr_cookie_decline_text')->default('Decline')->after('gdpr_cookie_button_text');
            $table->string('gdpr_cookie_settings_text')->default('Settings')->after('gdpr_cookie_decline_text');
            $table->string('gdpr_cookie_position')->default('bottom')->after('gdpr_cookie_settings_text'); // bottom, top
            $table->string('gdpr_cookie_bg_color')->default('#ffffff')->after('gdpr_cookie_position');
            $table->string('gdpr_cookie_text_color')->default('#1b1b18')->after('gdpr_cookie_bg_color');
            $table->string('gdpr_cookie_button_color')->default('#FF750F')->after('gdpr_cookie_text_color');
            $table->integer('gdpr_cookie_expiry_days')->default(365)->after('gdpr_cookie_button_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_settings', function (Blueprint $table) {
            $table->dropColumn([
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
            ]);
        });
    }
};
