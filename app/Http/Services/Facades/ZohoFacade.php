<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: ZohoFacade.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Services\Facades;

use Illuminate\Support\Facades\Facade;

class ZohoFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Zoho';
    }
}
