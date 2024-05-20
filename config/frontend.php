<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Front End Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    |
    */
    'url' => env('FRONTEND_URL', 'http://localhost:3000/'),

    /**
     * ---------------------------------------------
     * Invitation URL
     * ---------------------------------------------
     */
    'invitation' => [
        'decline_url' => env('FRONTEND_DECLINE_URL', 'http://localhost:3000/invitation/decline/'),
        'accept_url' => env('FRONTEND_ACCEPT_URL', 'http://localhost:3000/invitation/accept/'),
    ],

    /**
     * ---------------------------------------------
     * Password Reset
     * ---------------------------------------------
     */
    'reset_password' => env('FRONTEND_RESET_PASSWORD_URL', 'http://localhost:3000/reset-password/'),
];
