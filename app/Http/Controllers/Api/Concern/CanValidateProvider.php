<?php

/*
 * Copyright (c) 2024.
 *
 * Filename: CanValidateProvider.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Concern;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait CanValidateProvider
{
    /**
     * @param  string  $provider
     * @return void
     *
     * @throw UnprocessableEntityHttpException
     */
    protected function validateProvider(string $provider): void
    {
        if ($provider !== 'zoho') {
            throw new UnprocessableEntityHttpException('Please login using credentials, or zoho');
        }
    }
}
