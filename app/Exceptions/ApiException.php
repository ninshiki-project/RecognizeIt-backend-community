<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: ApiException.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Exceptions;

use App\Traits\ApiExceptionTrait;
use Exception;

class ApiException extends Exception
{
    use ApiExceptionTrait;
}
