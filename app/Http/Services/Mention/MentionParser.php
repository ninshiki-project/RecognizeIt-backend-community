<?php

/*
 * Copyright (c) 2025.
 *
 * Filename: MentionParser.php
 * Project Name: ninshiki-backend
 * Project Repository: https://github.com/ninshiki-project/Ninshiki-backend
 *  License: MIT
 *  GitHub: https://github.com/MarJose123
 *  Written By: Marjose123
 */

namespace App\Http\Services\Mention;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MentionParser
{
    protected ?Collection $mentions = null;

    protected string $content;

    public function parse(\Closure|string $content = ''): static
    {
        $content = is_callable($content) ? $content() : $content;
        $this->mentions = Str::of($content)->trim()->matchAll('/\B@\w+/');

        return $this;
    }

    /**
     * Return the mentioned users that has been parsed in the
     * content
     *
     * @return array|Collection|null
     */
    public function mentions(): array|Collection|null
    {
        return $this->mentions;
    }

    /**
     * Return mention as Collection of Eloquent
     *
     * @return Collection
     */
    public function toCollection(): Collection
    {
        $collection = collect();
        $this->mentions?->each(function ($mention) use ($collection) {
            $collection->push(
                User::where('username', Str::of($mention)->after('@'))->firstOrFail()
            );
        });

        return $collection;
    }
}
