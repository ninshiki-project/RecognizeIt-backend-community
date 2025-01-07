<?php

namespace App\Http\Services\Zoho;

use App\Http\Services\Oauth;
use App\Http\Services\ProviderInterface;

class Zoho extends Oauth implements ProviderInterface
{
    /**
     * @return string This will return a URL
     */
    public function loginLink(): string
    {
        return $this->driver
            ->setScopes(['AaaServer.profile.Read', 'zohocontacts.contactapi.READ'])
            ->with([
                'prompt' => 'consent',
                'access_type' => 'offline',
            ])
            ->stateless()->redirect()->getTargetUrl();
    }

    public function getAvatar(): ?string
    {
        $user = $this->getUserInfo();

        // @phpstan-ignore-next-line
        return $user->user['ZUID'] ? 'https://contacts.zoho.com/file?t=user&ID='.$user->user['ZUID'] : null;
    }
}
