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
            if (Str::of($tldDomain)->contains(',')) {
                $domains = Str::of($tldDomain)->explode(',');

                return in_array($email, $domains);
            }

            return Str::of($email)->endsWith($tldDomain);
        }

        return in_array($email, $tldDomain);

    }
}
