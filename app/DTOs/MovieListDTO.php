<?php

namespace App\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

class MovieListDTO
{
    public LengthAwarePaginator $movies;
    public int $totalMovies;
    public string $sort;

    public function __construct(LengthAwarePaginator $movies, int $totalMovies, string $sort)
    {
        $this->movies = $movies;
        $this->totalMovies = $totalMovies;
        $this->sort = $sort;
    }
}
