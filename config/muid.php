<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Muid length
     |--------------------------------------------------------------------------
     |
     | Here you can change the MUID length.
     | remember that length include [prefix, timestamp(6)chars] and the rest will be random bits
     | recommended to have minimum of 16 chars
     |
     */
    'tiny_muid_length' => 16,
    'small_muid_length' => 24,
    'muid_length' => 36,

    /*
     |--------------------------------------------------------------------------
     | Random string strings
     |--------------------------------------------------------------------------
     |
     | Recommended not to change
     |
     */
    'alfa_small' => 'abcdefghilkmnopqrstuvwxyz',
    'alfa_capital' => 'ABCDEFGHILKMNOPQRSTUVWXYZ',
    'digits' => '0123456789',

    /*
     |--------------------------------------------------------------------------
     | Capital Char options
     |--------------------------------------------------------------------------
     |
     | set it to FALSE if you wish not use capital letters in the generated MUID
     |
     */
    'allow_capital' => true,

];
