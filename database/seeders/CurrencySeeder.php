<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if INR currency already exists
        $inrCurrency = Currency::where('code', 'INR')->first();

        if (!$inrCurrency) {
            // Remove default flag from any existing currencies
            Currency::where('is_default', true)->update(['is_default' => false]);

            // Create INR currency as default
            Currency::create([
                'name' => 'Indian Rupee',
                'code' => 'INR',
                'symbol' => '₹',
                'exchange_rate' => 1.0000,
                'is_default' => true,
                'status' => 'Active',
            ]);
        } else {
            // Update existing INR currency to be default if it's not already
            if (!$inrCurrency->is_default) {
                Currency::where('is_default', true)->update(['is_default' => false]);
                $inrCurrency->update([
                    'is_default' => true,
                    'status' => 'Active',
                    'symbol' => '₹',
                    'exchange_rate' => 1.0000,
                ]);
            }
        }
    }
}




