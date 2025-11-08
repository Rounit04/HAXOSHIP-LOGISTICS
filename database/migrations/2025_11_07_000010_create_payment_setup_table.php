<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_setup', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_name');
            $table->string('gateway_type')->default('online');
            $table->string('api_key')->nullable();
            $table->string('api_secret')->nullable();
            $table->string('merchant_id')->nullable();
            $table->boolean('enabled')->default(false);
            $table->boolean('test_mode')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_setup');
    }
};

