<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: Oauth.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Services;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User;

trait Oauth
{
    protected string $code;

    protected string $accessToken;

    protected mixed $driver;

    public function __construct()
    {
        $this->driver = Socialite::driver($this->getProviderId());
    }

    public function performCallBackAction(): array
    {
        return $this->driver->stateless()->getAccessTokenResponse($this->getCode());

    }

    public function getUserInfo(): User
    {
        return $this->driver->stateless()->userFromToken($this->getAccessToken());
    }

    public function setAccessToken(string $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
