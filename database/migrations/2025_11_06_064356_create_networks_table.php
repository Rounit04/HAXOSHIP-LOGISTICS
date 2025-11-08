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
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type'); // Domestic or International
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->string('status')->default('Active'); // Active or Inactive
            $table->text('bank_details')->nullable();
            $table->string('upi_scanner')->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
