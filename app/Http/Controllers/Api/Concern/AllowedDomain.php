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
     * @param  string  $email
     * @return bool
     */
    protected function isWhitelistedDomain(string $email): bool
    {
        $tldDomain = config('ninshiki.allowed_email_domain');
        $emailDomain = Str::of($email)->lower()->afterLast('@');

        if (is_null($tldDomain) || Str::of($tldDomain)->isEmpty()) {
            return true;
        }

        // check if the domain variable is an array and if array then validate
        if (is_array($tldDomain)) {
            return in_array($emailDomain, $tldDomain);
        }
        // if not array then possible a string? with comma-separated? or a single string
        if (Str::of($tldDomain)->contains(',')) {
            // The filter domain is in comma-separated
            $domains = Str::of($tldDomain)->explode(',')->toArray();

            return in_array($emailDomain, $domains);
        }

        // the tld domain filter is a single string
        return Str::of($tldDomain)->contains($emailDomain, ignoreCase: true);

    }
}
