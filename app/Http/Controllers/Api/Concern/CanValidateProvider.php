<?php

namespace App\Http\Controllers\Api\Concern;

trait CanValidateProvider
{
    protected function validateProvider($provider)
    {
        if ($provider != 'zoho') {
            return response()->json([
                'success' => false,
                'message' => 'Please login using credentials, or zoho'], 422);
        }
    }
}
