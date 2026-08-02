<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Number of decimal places configured for the app.
     */
    public static function decimals(): int
    {
        return (int) config('numbers.decimals', 3);
    }

    /**
     * Value for the `step` attribute of a numeric input.
     *
     * "any" lets the browser accept as many decimals as the user types,
     * instead of rejecting anything finer than a fixed step.
     */
    public static function step(): string
    {
        return 'any';
    }

    /**
     * Round a value to the configured precision, without thousand separators.
     * Use this for values written back into form inputs.
     */
    public static function round($number, ?int $decimals = null)
    {
        return round((float) ($number ?? 0), $decimals ?? static::decimals());
    }

    /**
     * Format a number for display, with thousand separators and without
     * trailing zeros (1.500 => "1.5", 1.000 => "1", 1234.25 => "1,234.25").
     */
    public static function format($number, ?int $decimals = null)
    {
        $decimals = $decimals ?? static::decimals();

        $formatted = number_format((float) ($number ?? 0), max($decimals, 0));

        if (config('numbers.trim_trailing_zeros', true) && str_contains($formatted, '.')) {
            $formatted = rtrim(rtrim($formatted, '0'), '.');
        }

        return $formatted;
    }
}
