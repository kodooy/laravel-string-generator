<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Character Set
    |--------------------------------------------------------------------------
    |
    | This is the default character set used for string generation.
    | You can customize this to include or exclude specific characters.
    |
    */
    'default_charset' => 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789',

    /*
    |--------------------------------------------------------------------------
    | Character Sets
    |--------------------------------------------------------------------------
    |
    | Predefined character sets for different use cases.
    |
    */
    'charsets' => [
        'alphanumeric' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        'alphanumeric_safe' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789', // Excludes i, l, I, o, O, 0
        'alpha' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'alpha_safe' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ', // Excludes i, l, I, o, O
        'numeric' => '0123456789',
        'lowercase' => 'abcdefghijklmnopqrstuvwxyz',
        'uppercase' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'symbols' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Length
    |--------------------------------------------------------------------------
    |
    | The default length for generated strings when not specified.
    |
    */
    'default_length' => 8,

    /*
    |--------------------------------------------------------------------------
    | Maximum Attempts
    |--------------------------------------------------------------------------
    |
    | Maximum number of attempts when generating unique collections.
    | This prevents infinite loops when requesting too many unique strings.
    |
    */
    'max_attempts_multiplier' => 5,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Enable caching for generated strings (useful for testing or debugging).
    |
    */
    'cache' => [
        'enabled' => false,
        'ttl' => 3600, // 1 hour
        'prefix' => 'string_generator:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    |
    | Enable events for string generation.
    |
    */
    'events' => [
        'enabled' => true,
    ],
];
