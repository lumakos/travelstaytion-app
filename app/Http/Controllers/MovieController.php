<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MovieController extends Controller
{
//    public function index(Request $request)
//    {
//        $sort = $request->query('sort', 'latest');
//        $page = $request->query('page', 1);
//
//        // Init cache keys
//        $moviesPerPageKey = "movies_{$sort}_page_{$page}";
//        $totalMoviesKey = "movies_total_count";
//
//        // Checks if listing and total num of movies are cached
//        if (Cache::has($moviesPerPageKey) && Cache::has($totalMoviesKey)) {
//            $movies = Cache::get($moviesPerPageKey);
//            $totalMovies = Cache::get($totalMoviesKey);
//
//            return view('movies.index', compact('movies', 'sort', 'totalMovies'));
//        }
//
//        $sortOptions = [
//            'latest' => ['created_at', 'desc'],
//            'likes' => ['likes', 'desc'],
//            'hates' => ['hates', 'desc'],
//        ];
//
//        [$column, $direction] = $sortOptions[$sort] ?? $sortOptions['latest'];
//
//        // Counts total num of movies
//        $totalMovies = Movie::count();
//
//        // Gets first 20 movies
//        $movies = Movie::with('user')
//            ->withCount([
//                'votes as likes' => function ($query) {
//                    $query->where('vote', 'like');
//                },
//                'votes as hates' => function ($query) {
//                    $query->where('vote', 'hate');
//                },
//            ])
//            ->orderBy($column, $direction)
//            ->paginate(20);
//
//        // Store current movies
//        Cache::put($moviesPerPageKey, $movies, now()->addMinutes(10));
//        // Store total number of movies
//        Cache::put($totalMoviesKey, $totalMovies, now()->addMinutes(10));
//
//        // Store cache key in a list of unique keys
//        Redis::sadd('movies_cache_keys', $moviesPerPageKey);
//
//        return view('movies.index', compact('movies', 'sort', 'totalMovies'));
//    }
//
//    public function create()
//    {
//        return view('movies.create');
//    }
//
//    public function store(Request $request)
//    {
//        $request->validate([
//            'title' => 'required|string|max:255',
//            'description' => 'required|string',
//        ]);
//
//        Movie::create([
//            'user_id' => auth()->id(),
//            'title' => $request->title,
//            'description' => $request->description,
//        ]);
//
//        // Get all cache keys about movie list
//        $cacheKeys = Redis::smembers('movies_cache_keys');
//        // Delete all of them
//        foreach ($cacheKeys as $key) {
//            Cache::forget($key);
//        }
//        // Delete the list of cache keys
//        Redis::del('movies_cache_keys');
//
//        return redirect()->route('movies.index');
//    }
//
//    public function userMovies(Request $request, $userId)
//    {
//        $sort = $request->query('sort', 'latest');
//        $page = $request->query('page', 1);
//
//        // Init cache keys
//        $moviesPerUserIdKey = "movies_{$sort}_page_{$page}_userid_{$userId}";
//        $totalMoviesPerUserKey = "movies_total_count_userid_{$userId}";
//
//        // Checks if listing and total num of movies are cached
//        if (Cache::has($moviesPerUserIdKey) && Cache::has($totalMoviesPerUserKey)) {
//            $movies = Cache::get($moviesPerUserIdKey);
//            $totalMovies = Cache::get($totalMoviesPerUserKey);
//
//            return view('movies.index', compact('movies', 'sort', 'totalMovies'));
//        }
//
//        $sortOptions = [
//            'latest' => ['created_at', 'desc'],
//            'likes' => ['likes', 'desc'],
//            'hates' => ['hates', 'desc'],
//        ];
//
//        [$column, $direction] = $sortOptions[$sort] ?? $sortOptions['latest'];
//
//        $totalMovies = Movie::where('user_id', $userId)->count();
//
//        $movies = Movie::with('user')
//            ->where('user_id', $userId)
//            ->withCount([
//                'votes as likes' => function ($query) {
//                    $query->where('vote', 'like');
//                },
//                'votes as hates' => function ($query) {
//                    $query->where('vote', 'hate');
//                },
//            ])
//            ->orderBy($column, $direction)
//            ->paginate(20);
//
//        // Store current movies
//        Cache::put($moviesPerUserIdKey, $movies, now()->addMinutes(10));
//        // Store total number of movies
//        Cache::put($totalMoviesPerUserKey, $totalMovies, now()->addMinutes(10));
//
//        // Store cache key in a list of unique keys
//        Redis::sadd('movies_cache_keys', $moviesPerUserIdKey);
//
//        return view('movies.index', compact('movies', 'sort', 'totalMovies'));
//    }

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

        // Store data to cache
        Cache::put($cacheKeyPrefix, $movies, now()->addMinutes(10));
        Cache::put($totalMoviesKey, $totalMovies, now()->addMinutes(10));

        // Store cache key of each movie
        foreach ($movies as $movie) {
            Redis::sadd("movie_cache_keys_{$movie->id}", $cacheKeyPrefix);
        }
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
