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
        Schema::create('frontend_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('primary_color')->default('#FF750F');
            $table->string('secondary_color')->default('#ff8c3a');
            $table->string('text_color')->default('#1b1b18');
            $table->text('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_button_text')->default('Track Now');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('frontend_settings');
    }
};
