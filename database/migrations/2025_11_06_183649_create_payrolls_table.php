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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('user_type', ['merchant', 'deliveryman', 'user'])->default('user');
            $table->decimal('salary_amount', 10, 2);
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('generation_type', ['manual', 'calendar'])->default('manual');
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'period_start', 'period_end']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
