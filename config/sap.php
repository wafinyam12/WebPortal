<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection SAP
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */
    'sapb1' => [
        'url' => env('SAP_B1_URL'),
        'username' => env('SAP_B1_USERNAME'),
        'password' => env('SAP_B1_PASSWORD'),
        // 'database' => env('SAP_B1_DATABASE'),
    ],

];
