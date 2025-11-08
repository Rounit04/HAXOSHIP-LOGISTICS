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
            $table->string('footer_logo')->nullable()->after('hero_button_text');
            $table->text('footer_description')->nullable()->after('footer_logo');
            $table->string('footer_facebook_url')->nullable()->after('footer_description');
            $table->string('footer_instagram_url')->nullable()->after('footer_facebook_url');
            $table->string('footer_twitter_url')->nullable()->after('footer_instagram_url');
            $table->string('footer_skype_url')->nullable()->after('footer_twitter_url');
            $table->string('footer_google_play_url')->nullable()->after('footer_skype_url');
            $table->string('footer_app_store_url')->nullable()->after('footer_google_play_url');
            $table->text('footer_copyright_text')->nullable()->after('footer_app_store_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_logo',
                'footer_description',
                'footer_facebook_url',
                'footer_instagram_url',
                'footer_twitter_url',
                'footer_skype_url',
                'footer_google_play_url',
                'footer_app_store_url',
                'footer_copyright_text',
            ]);
        });
    }
};
