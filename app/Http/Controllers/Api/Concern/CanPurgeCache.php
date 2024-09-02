<?php

namespace App\Http\Controllers\Api\Concern;

use Illuminate\Support\Facades\Cache;

trait CanPurgeCache
{
    protected static string $cacheKey;

    /**
     * @param  mixed|null  $key
     * @return void
     */
    public function purgeCache(mixed $key = null)
    {
        isset($key) ? Cache::forget($key) : Cache::forget(static::$cacheKey);
    }
}
