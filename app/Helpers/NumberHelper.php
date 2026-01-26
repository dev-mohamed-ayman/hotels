<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format number without trailing zeros
     */
    public static function format($number)
    {
        return preg_replace('/\.0+$|\.00+$/', '', number_format($number, 2));
    }
}
