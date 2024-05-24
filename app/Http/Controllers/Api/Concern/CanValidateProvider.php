<?php

namespace App\Http\Controllers\Api\Concern;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

trait CanValidateProvider
{
    protected function validateProvider($provider)
    {
        if ($provider != 'zoho') {
            throw new UnprocessableEntityHttpException('Please login using credentials, or zoho');
        }
    }
}
