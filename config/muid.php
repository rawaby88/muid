<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Generation Strategy
    |--------------------------------------------------------------------------
    |
    | This option controls the default ID generation strategy used by the
    | package. Available strategies: 'incremental', 'padded', 'ordered'
    |
    | - 'incremental': Simple incrementing IDs (usr_1, usr_2, usr_3)
    | - 'padded': Zero-padded incrementing IDs (usr_0000001, usr_0000002)
    | - 'ordered': Time-sortable IDs similar to ULID (recommended)
    |
    */

    'default_strategy' => 'ordered',

    /*
    |--------------------------------------------------------------------------
    | Column Lengths
    |--------------------------------------------------------------------------
    |
    | These values define the maximum column lengths for each MUID size.
    | The length includes the prefix and separator.
    |
    */

    'lengths' => [
        'tiny' => 16,      // e.g., usr_1234567890 (12 chars for body)
        'small' => 24,     // e.g., usr_12345678901234567890 (20 chars for body)
        'standard' => 36,  // e.g., usr_12345678901234567890123456789012 (32 chars for body)
    ],

    /*
    |--------------------------------------------------------------------------
    | Incremental / Padded Settings
    |--------------------------------------------------------------------------
    |
    | Configuration options for the incremental and padded generators.
    |
    */

    'incremental' => [
        'padding_length' => 7,        // usr_0000001
        'padding_character' => '0',
        'per_prefix' => true,         // Each prefix has its own sequence
    ],

    /*
    |--------------------------------------------------------------------------
    | Signature Settings
    |--------------------------------------------------------------------------
    |
    | Optional signature validation for MUIDs. When enabled, a checksum is
    | appended to validate the authenticity of generated IDs.
    |
    */

    'signature' => [
        'enabled' => false,           // Disabled by default for simplicity
        'length' => 4,                // chars for signature when enabled
        'algorithm' => 'xxh64',       // fast non-cryptographic hash
    ],

    /*
    |--------------------------------------------------------------------------
    | Character Encoding
    |--------------------------------------------------------------------------
    |
    | The encoding type used for the MUID body.
    |
    | - 'base62': a-zA-Z0-9 (62 chars, compact, case-sensitive)
    | - 'base36': a-z0-9 (36 chars, case-insensitive, slightly longer)
    |
    */

    'encoding' => [
        'type' => 'base62',
    ],

    /*
    |--------------------------------------------------------------------------
    | Prefix Validation Rules
    |--------------------------------------------------------------------------
    |
    | Rules for validating MUID prefixes.
    |
    */

    'prefix' => [
        'min_length' => 2,
        'max_length' => 8,
        'pattern' => '/^[a-z][a-z0-9]*$/i', // starts with letter
    ],
];
