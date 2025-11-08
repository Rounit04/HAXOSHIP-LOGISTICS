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
        Schema::create('booking_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['wallet', 'ledger', 'support'])->default('wallet');
            $table->boolean('requires_awb')->default(false);
            $table->enum('status', ['Active', 'In-active'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_categories');
    }
};
