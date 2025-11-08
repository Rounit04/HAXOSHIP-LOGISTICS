<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('googlemap_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->nullable();
            $table->boolean('enabled')->default(false);
            $table->string('map_type')->default('roadmap');
            $table->integer('zoom_level')->default(10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('googlemap_settings');
    }
};

