<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->query('sort', 'latest');

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'likes' => ['likes', 'desc'],
            'hates' => ['hates', 'desc'],
        ];

        [$column, $direction] = $sortOptions[$sort] ?? $sortOptions['latest'];

        // Counts total num of movies
        $total_movies = Movie::all()->count();

        // Gets first 20 movies
        $movies = Movie::with('user')
            ->withCount([
                'votes as likes' => function ($query) {
                    $query->where('vote', 'like');
                },
                'votes as hates' => function ($query) {
                    $query->where('vote', 'hate');
                },
            ])
            ->orderBy($column, $direction)
            ->paginate(20);

        return view('movies.index', compact('movies', 'sort', 'total_movies'));
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

        return redirect()->route('movies.index');
    }

    public function userMovies(Request $request, $userId)
    {
        $sort = $request->query('sort', 'latest');

        $sortOptions = [
            'latest' => ['created_at', 'desc'],
            'likes' => ['likes', 'desc'],
            'hates' => ['hates', 'desc'],
        ];

        [$column, $direction] = $sortOptions[$sort] ?? $sortOptions['latest'];

        $total_movies = Movie::where('user_id', $userId)->count();

        $movies = Movie::with('user')
            ->where('user_id', $userId)
            ->withCount([
                'votes as likes' => function ($query) {
                    $query->where('vote', 'like');
                },
                'votes as hates' => function ($query) {
                    $query->where('vote', 'hate');
                },
            ])
            ->orderBy($column, $direction)
            ->paginate(20);

        return view('movies.index', compact('movies', 'sort', 'total_movies'));
    }

}
