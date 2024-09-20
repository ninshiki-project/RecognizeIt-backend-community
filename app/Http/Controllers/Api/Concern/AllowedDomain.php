<?php
/*
 * Copyright (c) 2024.
 *
 * Filename: AllowedDomain.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Support\Str;

trait AllowedDomain
{
    /**
     * @param  $email
     * @return bool
     */
    protected function isWhitelistedDomain($email): bool
    {
        $tldDomain = config('ninshiki.allowed_email_domain');

        if (is_null($tldDomain)) {
            return true;
        }

        if (! is_array($tldDomain)) {
            if (Str::of($tldDomain)->contains(',')) {
                $domains = Str::of($tldDomain)->explode(',')->toArray();

                return in_array($email, $domains);
            }

            return Str::of($email)->endsWith($tldDomain);
        }

        return in_array($email, $tldDomain);

    }
}
