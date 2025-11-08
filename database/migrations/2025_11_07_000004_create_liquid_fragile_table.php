<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liquid_fragile', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Liquid', 'Fragile', 'Both'])->default('Both');
            $table->decimal('additional_charge', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liquid_fragile');
    }
};

