<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Decimal Places
    |--------------------------------------------------------------------------
    |
    | How many decimal places money/numeric values keep across the whole app:
    | database storage, form inputs, PHP display helpers and the JS totals.
    |
    | Changing this value updates display and input handling immediately.
    | To change how many decimals the *database* stores you must also run a
    | migration (see database/migrations/*_set_decimal_scale_on_money_columns).
    |
    */

    'decimals' => (int) env('NUMBER_DECIMALS', 3),

    /*
    |--------------------------------------------------------------------------
    | Total Precision
    |--------------------------------------------------------------------------
    |
    | Total number of significant digits stored in decimal columns, including
    | the decimals above. 15 leaves 12 digits for the integer part.
    |
    */

    'precision' => (int) env('NUMBER_PRECISION', 15),

    /*
    |--------------------------------------------------------------------------
    | Trim Trailing Zeros
    |--------------------------------------------------------------------------
    |
    | When true, 1.500 is displayed as "1.5" and 1.000 as "1" instead of
    | padding every value out to the full decimal count.
    |
    */

    'trim_trailing_zeros' => true,

];
