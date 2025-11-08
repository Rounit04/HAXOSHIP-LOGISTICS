<?php

namespace App\Helpers;

use App\Models\Currency;

class CurrencyHelper
{
    /**
     * Get the default currency
     */
    public static function getDefault()
    {
        return Currency::where('is_default', true)
            ->where('status', 'Active')
            ->first() ?? Currency::where('status', 'Active')->first();
    }

    /**
     * Format a price with currency
     */
    public static function format($amount, $currency = null)
    {
        if (!$currency) {
            $currency = self::getDefault();
        }

        if (!$currency) {
            return number_format($amount, 2);
        }

        // Convert amount based on exchange rate
        $convertedAmount = $amount * $currency->exchange_rate;

        // Format with currency symbol
        return $currency->symbol . number_format($convertedAmount, 2);
    }

    /**
     * Format a price with currency code
     */
    public static function formatWithCode($amount, $currency = null)
    {
        if (!$currency) {
            $currency = self::getDefault();
        }

        if (!$currency) {
            return number_format($amount, 2);
        }

        // Convert amount based on exchange rate
        $convertedAmount = $amount * $currency->exchange_rate;

        // Format with currency code
        return number_format($convertedAmount, 2) . ' ' . $currency->code;
    }

    /**
     * Get currency symbol
     */
    public static function symbol($currency = null)
    {
        if (!$currency) {
            $currency = self::getDefault();
        }

        return $currency ? $currency->symbol : '₹';
    }

    /**
     * Get currency code
     */
    public static function code($currency = null)
    {
        if (!$currency) {
            $currency = self::getDefault();
        }

        return $currency ? $currency->code : 'INR';
    }

    /**
     * Convert amount to another currency
     */
    public static function convert($amount, $fromCurrency = null, $toCurrency = null)
    {
        if (!$fromCurrency) {
            $fromCurrency = self::getDefault();
        }

        if (!$toCurrency) {
            $toCurrency = self::getDefault();
        }

        if (!$fromCurrency || !$toCurrency) {
            return $amount;
        }

        // Convert to base currency first, then to target currency
        $baseAmount = $amount / $fromCurrency->exchange_rate;
        return $baseAmount * $toCurrency->exchange_rate;
    }
}

