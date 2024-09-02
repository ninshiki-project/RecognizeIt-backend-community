<?php

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Support\Facades\Cache;

trait CanPurgeCache
{
    /**
     * @param  string|null  $key
     * @return void
     */
    public function purgeCache(?string $key = null)
    {

        isset($key) ? static::setCacheKey($key) : static::setCacheKey(static::getCacheKey());

        Cache::forget(static::getCacheKey());

    }

    protected static function getCacheKey(): string
    {
        $reflection = new \ReflectionClass(static::class);
        if (! $reflection->hasProperty('cacheKey')) {
            throw new \RuntimeException('Cache key property does not exist');
        }

        return static::$cacheKey;
    }

    protected static function setCacheKey(string $key)
    {
        static::$cacheKey = $key;
    }
}
