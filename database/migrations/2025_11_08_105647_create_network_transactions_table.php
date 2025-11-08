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
        Schema::create('network_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('network_id')->constrained('networks')->onDelete('cascade');
            $table->string('booking_id')->nullable(); // Booking ID from session (stored as string)
            $table->string('awb_no')->nullable(); // AWB number for reference
            $table->enum('type', ['credit', 'debit'])->index();
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->string('transaction_type')->default('booking'); // booking, price_change, network_change
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['network_id', 'type']);
            $table->index(['booking_id', 'transaction_type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('network_transactions');
    }
};
