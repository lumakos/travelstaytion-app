<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Helpers\SortOptionsHelper;
use App\Http\Requests\Movie\CreateMovieRequest;
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

    public function store(CreateMovieRequest $request)
    {
        $data = $request->validated();

        try {
            Movie::create([
                'user_id' => auth()->id(),
                'title' => $data['title'],
                'description' => $data['description'],
            ]);

            $this->clearCache();

            return redirect()->route('movies.index');
        } catch (\Exception $e) {
            \Log::error('Error storing movie: ' . $e->getMessage());

            return redirect()->route('movies.create')->with('error', 'There was an issue saving the movie.');
        }
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

        $listMoviesKey = $this->generateMoviesCacheKey($userId, $sort, $page);
        $totalMoviesKey = $this->generateTotalMoviesCacheKey($userId);

        if ($cachedData = $this->getMoviesFromCache($listMoviesKey, $totalMoviesKey)) {
            return $cachedData;
        }

        try {
            // Get Sort Options
            $sortOptions = $this->getSortOptions($sort);
            // Get movies
            $movies = $this->fetchMovies($userId, $sortOptions);

            // Get total movies
            $totalMovies = $userId ? Movie::where('user_id', $userId)->count() : Movie::count();
            // Cache movies
            $this->cacheMovies($listMoviesKey, $totalMoviesKey, $movies, $totalMovies);

            return compact('movies', 'totalMovies', 'sort');
        } catch (\Exception $e) {
            \Log::error('Error fetching movies: ' . $e->getMessage());
            return compact([], [], $sort);
        }
    }

    /**
     * @param string $listMoviesKey
     * @param string $totalMoviesKey
     * @param $movies
     * @param int $totalMovies
     * @return void
     */
    private function cacheMovies(string $listMoviesKey, string $totalMoviesKey, $movies, int $totalMovies): void
    {
        // Cache sorting list of movies per page
        Cache::put($listMoviesKey, $movies, CacheHelper::CACHE_DURATION);
        // Cache total num of movies
        Cache::put($totalMoviesKey, $totalMovies, CacheHelper::CACHE_DURATION);

        // Store cache key of each movie
        foreach ($movies as $movie) {
            Redis::sadd("movie_cache_keys_{$movie->id}", $listMoviesKey);
        }

        Redis::sadd(CacheHelper::MOVIE_CACHE_KEY, $listMoviesKey);
    }

    /**
     * @param int|null $userId
     * @param array $sortOptions
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function fetchMovies(?int $userId, array $sortOptions)
    {
        $query = Movie::with('user')
            ->withCount([
                'votes as likes' => fn($q) => $q->where('vote', Vote::LIKE_VOTE),
                'votes as hates' => fn($q) => $q->where('vote', Vote::HATE_VOTE),
            ])
            ->orderBy($sortOptions[0], $sortOptions[1]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->paginate(Movie::PER_PAGE);
    }

    /**
     * @param string $sort
     * @return array
     */
    private function getSortOptions(string $sort): array
    {
        $sortOptions = [
            SortOptionsHelper::LATEST => [SortOptionsHelper::CREATED_AT, 'desc'],
            SortOptionsHelper::LIKES => [SortOptionsHelper::LIKES, 'desc'],
            SortOptionsHelper::HATES => [SortOptionsHelper::HATES, 'desc'],
        ];

        return $sortOptions[$sort] ?? $sortOptions[SortOptionsHelper::LATEST];
    }

    /**
     * @param string $listMoviesKey
     * @param string $totalMoviesKey
     * @return array|null
     */
    private function getMoviesFromCache(string $listMoviesKey, string $totalMoviesKey): ?array
    {
        if (Cache::has($listMoviesKey) && Cache::has($totalMoviesKey)) {
            return [
                'movies' => Cache::get($listMoviesKey),
                'totalMovies' => Cache::get($totalMoviesKey),
                'sort' => request()->query('sort', 'latest')
            ];
        }

        return null;
    }

    /**
     * @param int|null $userId
     * @param string $sort
     * @param int $page
     * @return string
     */
    private function generateMoviesCacheKey(?int $userId, string $sort, int $page): string
    {
        return $userId ? "movies_{$sort}_page_{$page}_userid_{$userId}" : "movies_{$sort}_page_{$page}";
    }

    /**
     * @param int|null $userId
     * @return string
     */
    private function generateTotalMoviesCacheKey(?int $userId): string
    {
        return $userId ? "movies_total_count_userid_{$userId}" : "movies_total_count";
    }

    /**
     * Flush all cache keys of a specific listname
     *
     * @return void
     */
    private function clearCache(): void
    {
        // Get an array of all cache keys about 'movies_cache_keys' list
        $cacheKeys = Redis::smembers(CacheHelper::MOVIE_CACHE_KEY);

        // Delete each cache key
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Delete the 'movies_cache_keys' list of cache keys
        Redis::del(CacheHelper::MOVIE_CACHE_KEY);
    }
}
