<?php

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
            return Str::of($email)->endsWith($tldDomain);
        }

        return in_array($email, $tldDomain);

    }
}
