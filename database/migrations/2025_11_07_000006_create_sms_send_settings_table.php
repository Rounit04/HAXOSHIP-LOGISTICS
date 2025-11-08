<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_send_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('send_on_booking')->default(false);
            $table->boolean('send_on_delivery')->default(false);
            $table->boolean('send_on_pickup')->default(false);
            $table->boolean('send_on_status_update')->default(false);
            $table->text('booking_template')->nullable();
            $table->text('delivery_template')->nullable();
            $table->text('pickup_template')->nullable();
            $table->text('status_update_template')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_send_settings');
    }
};

