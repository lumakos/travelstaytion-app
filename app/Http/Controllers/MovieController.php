<?php

namespace App\Http\Controllers;

use App\DTOs\MovieDTO;
use App\DTOs\MovieListDTO;
use App\Http\Requests\Movie\CreateMovieRequest;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $moviesDTO = $this->getMovies($request);

        return view('movies.index', [
            'movies' => $moviesDTO->movies,
            'totalMovies' => $moviesDTO->totalMovies,
            'sort' => $moviesDTO->sort,
        ]);
    }

    public function userMovies(Request $request, $userId)
    {
        $moviesDTO = $this->getMovies($request, $userId);

        return view('movies.index', [
            'movies' => $moviesDTO->movies,
            'totalMovies' => $moviesDTO->totalMovies,
            'sort' => $moviesDTO->sort,
        ]);
    }

    public function create()
    {
        return view('movies.create');
    }

    public function store(CreateMovieRequest $request)
    {
        try {
            $movieDTO = new MovieDTO(auth()->id(), $request->title, $request->description);

            Movie::create([
                'user_id' => $movieDTO->userId,
                'title' => $movieDTO->title,
                'description' => $movieDTO->description,
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
    private function getMovies(Request $request, ?int $userId = null): MovieListDTO
    {
        $sort = $request->query('sort', 'latest');
        $page = $request->query('page', 1);

        $listMoviesKey = $userId ? "movies_{$sort}_page_{$page}_userid_{$userId}" : "movies_{$sort}_page_{$page}";
        $totalMoviesKey = $userId ? "movies_total_count_userid_{$userId}" : "movies_total_count";

        try {
            if (Cache::has($listMoviesKey) && Cache::has($totalMoviesKey)) {
                return new MovieListDTO(
                    Cache::get($listMoviesKey),
                    Cache::get($totalMoviesKey),
                    $sort
                );
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


            // Get total movies
            $totalMovies = $userId ? Movie::where('user_id', $userId)->count() : Movie::count();

            // Cache sorting list of movies per page
            Cache::put($listMoviesKey, $movies, now()->addMinutes(10));
            // Cache total num of movies
            Cache::put($totalMoviesKey, $totalMovies, now()->addMinutes(10));

            // Store cache key of each movie
            foreach ($movies as $movie) {
                Redis::sadd("movie_cache_keys_{$movie->id}", $listMoviesKey);
            }
            //
            Redis::sadd('movies_cache_keys', $listMoviesKey);

            return new MovieListDTO($movies, $totalMovies, $sort);
        } catch (\Exception $e) {
            \Log::error('Error fetching movies: ' . $e->getMessage());
            return new MovieListDTO([], 0, $sort);
        }
    }

    /**
     * Flush all cache keys of a specific listname
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
