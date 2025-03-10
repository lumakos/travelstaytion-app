<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheHelper
{
    /**
     * Flush cache of a specific movie
     *
     * @param int $movieId
     * @return void
     */
    public static function clearMovieCache(int $movieId): void
    {
        // Get all cache keys of the 'movie_cache_keys_{$movieId}' list
        $cacheKeys = Redis::smembers("movie_cache_keys_{$movieId}");

        // Delete only keys of 'movie_cache_keys_{$movieId}' list
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
            Redis::srem("movie_cache_keys_{$movieId}", $key);
        }

        // Delete the whole list of cache keys of a specific movie
        Redis::del("movie_cache_keys_{$movieId}");
    }
}
