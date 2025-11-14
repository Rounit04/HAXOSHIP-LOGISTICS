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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('pincode');
            $table->string('country');
            $table->string('zone');
            $table->string('network');
            $table->string('service');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remark')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('pincode');
            $table->index('country');
            $table->index('network');
            $table->index('service');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
