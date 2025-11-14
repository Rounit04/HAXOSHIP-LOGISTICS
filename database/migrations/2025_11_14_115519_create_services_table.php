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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('network');
            $table->string('transit_time')->nullable();
            $table->text('items_allowed')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remark')->nullable();
            $table->string('display_title')->nullable();
            $table->text('description')->nullable();
            $table->string('icon_type')->nullable();
            $table->boolean('is_highlighted')->default(false);
            $table->timestamps();
            
            // Indexes
            $table->index('network');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
