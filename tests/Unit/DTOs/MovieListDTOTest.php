<?php

namespace Tests\Unit\DTOs;

use App\DTOs\MovieListDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MovieListDTOTest extends TestCase
{
    public function test_creates_a_movie_list_dto_with_proper_properties()
    {
        $moviesCollection = new Collection([
            (object) ['id' => 1, 'title' => 'Inception'],
            (object) ['id' => 2, 'title' => 'Interstellar'],
        ]);

        $currentPage = 1;
        $perPage = 10;
        $total = 2;
        $moviesPaginator = new LengthAwarePaginator(
            $moviesCollection, $total, $perPage, $currentPage
        );

        $totalMovies = 2;
        $sort = 'title_asc';

        $movieListDTO = new MovieListDTO($moviesPaginator, $totalMovies, $sort);

        $this->assertInstanceOf(LengthAwarePaginator::class, $movieListDTO->movies);
        $this->assertEquals($totalMovies, $movieListDTO->totalMovies);
        $this->assertEquals($sort, $movieListDTO->sort);
    }
}
