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
        Schema::create('awb_uploads', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('hub')->nullable();
            $table->string('branch')->nullable();
            $table->string('awb_no')->unique();
            $table->string('type')->nullable(); // domestic/international
            $table->string('origin')->nullable();
            $table->string('origin_zone')->nullable();
            $table->string('origin_zone_pincode')->nullable();
            $table->string('destination')->nullable();
            $table->string('destination_zone')->nullable();
            $table->string('destination_zone_pincode')->nullable();
            $table->string('reference_no')->nullable();
            
            // Date Information
            $table->date('date_of_sale')->nullable();
            $table->date('invoice_date')->nullable();
            
            // Consignor & Consignee
            $table->string('non_commercial')->nullable();
            $table->string('consignor')->nullable();
            $table->string('consignor_attn')->nullable();
            $table->string('consignee')->nullable();
            $table->string('consignee_attn')->nullable();
            
            // Goods Information
            $table->string('goods_type')->nullable(); // Dox/NDox
            $table->integer('pk')->default(1);
            $table->decimal('actual_weight', 10, 2)->nullable();
            $table->decimal('volumetric_weight', 10, 2)->nullable();
            $table->decimal('chargeable_weight', 10, 2)->nullable();
            
            // Network & Service (fetched from existing tables)
            $table->string('network_name')->nullable();
            $table->string('service_name')->nullable();
            
            // Additional Information
            $table->decimal('amour', 10, 2)->nullable();
            $table->string('medical_shipment')->nullable();
            $table->decimal('invoice_value', 10, 2)->nullable();
            $table->boolean('is_coc')->default(false);
            $table->decimal('cod_amount', 10, 2)->default(0);
            
            // Status & Clearance
            $table->string('clearance_required')->nullable(); // Yes/No
            $table->text('remark')->nullable();
            $table->string('status')->default('publish');
            $table->string('payment_deduct')->nullable();
            $table->string('location')->nullable();
            
            // Forwarding Information
            $table->string('forwarding_service')->nullable();
            $table->string('forwarding_number')->nullable();
            $table->string('transfer')->nullable();
            $table->string('transfer_on')->nullable();
            $table->text('remark_1')->nullable();
            $table->text('remark_2')->nullable();
            
            // Additional fields from form
            $table->string('booking_type')->nullable();
            $table->string('shipment_type')->nullable();
            $table->string('display_service_name')->nullable();
            $table->text('operation_remark')->nullable();
            $table->text('remark_3')->nullable();
            $table->text('remark_4')->nullable();
            $table->text('remark_5')->nullable();
            $table->text('remark_6')->nullable();
            $table->text('remark_7')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('awb_uploads');
    }
};
