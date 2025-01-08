<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: ProviderInterface.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Services;

interface ProviderInterface
{
    public function getLoginLink(): string;

    public function performCallBackAction(): array;

    public function getAccessToken(): string;

    public function setAccessToken(string $token): self;

    public function getProviderId(): string;

    public function getProviderName(): string;

    public function getCode(): string;

    public function setCode(string $code): self;

    public function getAvatar(): ?string;

    public function getUserInfo(): object;
}
