<?php

use App\Helpers\CurrencyHelper;

if (!function_exists('currency')) {
    /**
     * Format a price with currency
     *
     * @param float $amount
     * @param mixed $currency
     * @return string
     */
    function currency($amount, $currency = null)
    {
        return CurrencyHelper::format($amount, $currency);
    }
}

if (!function_exists('currency_symbol')) {
    /**
     * Get currency symbol
     *
     * @param mixed $currency
     * @return string
     */
    function currency_symbol($currency = null)
    {
        return CurrencyHelper::symbol($currency);
    }
}

if (!function_exists('currency_code')) {
    /**
     * Get currency code
     *
     * @param mixed $currency
     * @return string
     */
    function currency_code($currency = null)
    {
        return CurrencyHelper::code($currency);
    }
}





