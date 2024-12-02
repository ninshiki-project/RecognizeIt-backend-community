<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: ninshiki.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

return [

    /**
     *  =============================================
     *  ---------- User Domain Filter ---------------
     *
     * This is to make sure that there is no other Zoho user can log in and access
     * the application that is not under allowed domain.
     *
     *  This param accepts array or string
     *  Ex.
     * ['mydomain.com','myotherdomain']
     *
     * If the ALLOWED_EMAIL_DOMAIN is null, then all the TLD domains will be passed in the filter
     * ==============================================
     */
    'allowed_email_domain' => env('ALLOWED_EMAIL_DOMAIN'),

    /**
     *  =======================================================
     *  Spend Wallet Founds
     *
     * This will use once the Spend Wallet will be reset and the fund will be reset to the default every month based on
     * the user type
     */
    'fund' => [
        'manager' => 100,
        'normal' => 30,
    ],

];
