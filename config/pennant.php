<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Pennant Store
    |--------------------------------------------------------------------------
    |
    | Here you will specify the default store that Pennant should use when
    | storing and resolving feature flag values. Pennant ships with the
    | ability to store flag values in an in-memory array or database.
    |
    | Supported: "array", "database"
    |
    */

    'default' => env('PENNANT_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Pennant Stores
    |--------------------------------------------------------------------------
    |
    | Here you may configure each of the stores that should be available to
    | Pennant. These stores shall be used to store resolved feature flag
    | values - you may configure as many as your application requires.
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'connection' => null,
            'table' => 'features',
        ],

    ],

    /*
        * Column names and data source that can be used to activate or deactivate for a segment of users.
        * This columns must exist on the users table and the data source must be a model.
        * COLUMN: The column name as defined on the default scope model config.
        * MODEL: The eloquent model of the source table.
        * VALUE: The column to be used as value.
        * KEY: The column to be used as key.
        */
    'segments' => [
        [
            'column' => 'email',
            'source' => [
                'model' => App\Models\User::class,
                'value' => 'email',
                'key' => 'email',
            ],
        ],
    ],
];
