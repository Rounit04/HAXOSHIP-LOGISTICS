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
            $table->string('site_name')->nullable()->after('id');
            $table->string('site_email')->nullable()->after('site_name');
            $table->text('site_description')->nullable()->after('site_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('frontend_settings', function (Blueprint $table) {
            $table->dropColumn(['site_name', 'site_email', 'site_description']);
        });
    }
};
