<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite doesn't support ALTER TABLE for enum columns, so we need to recreate the table
        // First, save existing data if any
        $existingData = DB::table('booking_categories')->get();
        
        // Drop the old table
        Schema::dropIfExists('booking_categories');
        
        // Recreate the table with new enum values
        Schema::create('booking_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['wallet', 'ledger', 'support'])->default('wallet');
            $table->boolean('requires_awb')->default(false);
            $table->enum('status', ['Active', 'In-active'])->default('Active');
            $table->timestamps();
        });
        
        // Restore existing data, converting old type values to new ones
        foreach ($existingData as $row) {
            $newType = 'wallet'; // Default to wallet for old data
            if ($row->type === 'Single') {
                $newType = 'wallet';
            } elseif ($row->type === 'Bulk') {
                $newType = 'wallet'; // Or you can map to ledger if needed
            }
            
            DB::table('booking_categories')->insert([
                'id' => $row->id,
                'name' => $row->name,
                'type' => $newType,
                'requires_awb' => $row->requires_awb,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Save existing data
        $existingData = DB::table('booking_categories')->get();
        
        // Drop the table
        Schema::dropIfExists('booking_categories');
        
        // Recreate with old enum values
        Schema::create('booking_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['Single', 'Bulk'])->default('Single');
            $table->boolean('requires_awb')->default(false);
            $table->enum('status', ['Active', 'In-active'])->default('Active');
            $table->timestamps();
        });
        
        // Restore data, converting new types back to old ones
        foreach ($existingData as $row) {
            $oldType = 'Single'; // Default
            if ($row->type === 'wallet') {
                $oldType = 'Single';
            } elseif ($row->type === 'ledger' || $row->type === 'support') {
                $oldType = 'Bulk';
            }
            
            DB::table('booking_categories')->insert([
                'id' => $row->id,
                'name' => $row->name,
                'type' => $oldType,
                'requires_awb' => $row->requires_awb,
                'status' => $row->status,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ]);
        }
    }
};
