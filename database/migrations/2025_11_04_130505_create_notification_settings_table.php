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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'user_login', 'role_assigned', 'order_updated'
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->boolean('show_popup')->default(true);
            $table->boolean('show_dropdown')->default(true);
            $table->integer('polling_interval')->default(30); // seconds
            $table->json('additional_settings')->nullable();
            $table->timestamps();
            
            $table->index('key');
            $table->index('enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
