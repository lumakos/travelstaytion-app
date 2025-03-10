<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        return view('movies.index', $this->getMovies($request));
    }

    public function userMovies(Request $request, $userId)
    {
        return view('movies.index', $this->getMovies($request, $userId));
    }

    public function create()
    {
        return view('movies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Movie::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        $this->clearCache();

        return redirect()->route('movies.index');
    }

    /**
     * @param Request $request
     * @param int|null $userId
     * @return array
     */
    private function getMovies(Request $request, ?int $userId = null): array
    {
        $sort = $request->query('sort', 'latest');
        $page = $request->query('page', 1);

        $cacheKeyPrefix = $userId ? "movies_{$sort}_page_{$page}_userid_{$userId}" : "movies_{$sort}_page_{$page}";
        $totalMoviesKey = $userId ? "movies_total_count_userid_{$userId}" : "movies_total_count";

        if (Cache::has($cacheKeyPrefix) && Cache::has($totalMoviesKey)) {
            return [
                'movies' => Cache::get($cacheKeyPrefix),
                'totalMovies' => Cache::get($totalMoviesKey),
                'sort' => $sort
            ];
        }

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'likes' => ['likes', 'desc'],
            'hates' => ['hates', 'desc'],
        ];

        [$column, $direction] = $sortOptions[$sort] ?? $sortOptions['latest'];

        $query = Movie::with('user')
            ->withCount([
                'votes as likes' => fn($q) => $q->where('vote', 'like'),
                'votes as hates' => fn($q) => $q->where('vote', 'hate'),
            ])->orderBy($column, $direction);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $movies = $query->paginate(20);
        $totalMovies = $userId ? Movie::where('user_id', $userId)->count() : Movie::count();

        // Cache sorting list of movies per page
        Cache::put($cacheKeyPrefix, $movies, now()->addMinutes(10));
        // Cache total num of movies
        Cache::put($totalMoviesKey, $totalMovies, now()->addMinutes(10));

        // Store cache key of each movie
        foreach ($movies as $movie) {
            Redis::sadd("movie_cache_keys_{$movie->id}", $cacheKeyPrefix);
        }
        //
        Redis::sadd('movies_cache_keys', $cacheKeyPrefix);

        return compact('movies', 'totalMovies', 'sort');
    }

    /**
     * Clears cache of a specific movie
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

    /**
     * Clears all cache keys of a specific listname
     *
     * @return void
     */
    private function clearCache(): void
    {
        // Get an array of all cache keys about 'movies_cache_keys' list
        $cacheKeys = Redis::smembers('movies_cache_keys');

        // Delete each cache key
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Delete the 'movies_cache_keys' list of cache keys
        Redis::del('movies_cache_keys');
    }
}
