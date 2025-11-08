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
            $table->string('services_section_title')->nullable()->after('about_us_content');
            $table->text('services_section_content')->nullable()->after('services_section_title');
            $table->string('why_haxo_section_title')->nullable()->after('services_section_content');
            $table->text('why_haxo_section_content')->nullable()->after('why_haxo_section_title');
            $table->string('pricing_section_title')->nullable()->after('why_haxo_section_content');
            $table->text('pricing_section_content')->nullable()->after('pricing_section_title');
            $table->text('stats_section_content')->nullable()->after('pricing_section_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_settings', function (Blueprint $table) {
            $table->dropColumn([
                'services_section_title',
                'services_section_content',
                'why_haxo_section_title',
                'why_haxo_section_content',
                'pricing_section_title',
                'pricing_section_content',
                'stats_section_content',
            ]);
        });
    }
};
