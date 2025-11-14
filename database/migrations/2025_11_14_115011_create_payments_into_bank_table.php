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
        Schema::create('payments_into_bank', function (Blueprint $table) {
            $table->id();
            $table->string('bank_account'); // Format: "Bank Name - Account Number"
            $table->enum('mode_of_payment', ['UPI', 'Cash', 'Netf'])->default('Netf');
            $table->enum('type', ['Credit', 'Debit']);
            $table->enum('category_bank', ['Salary', 'Expense', 'Revenue'])->default('Expense');
            $table->string('transaction_no');
            $table->decimal('amount', 15, 2);
            $table->text('remark')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('bank_account');
            $table->index('transaction_no');
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_into_bank');
    }
};
