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
        Schema::create('shipping_charges', function (Blueprint $table) {
            $table->id();
            $table->string('origin', 100);
            $table->string('origin_zone');
            $table->string('destination', 100);
            $table->string('destination_zone');
            $table->enum('shipment_type', ['Dox', 'Non-Dox', 'Medicine'])->default('Dox');
            $table->decimal('min_weight', 10, 2);
            $table->decimal('max_weight', 10, 2);
            $table->string('network', 100);
            $table->string('service', 100);
            $table->decimal('rate', 10, 2);
            $table->text('remark')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('origin');
            $table->index('destination');
            $table->index('network');
            $table->index('service');
            $table->index('shipment_type');
            // Composite index for common queries (limited string lengths to avoid key length limit)
            $table->index(['origin', 'destination', 'network', 'service']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_charges');
    }
};
