<?php

namespace Tests\Unit\DTOs;

use App\DTOs\MovieDTO;
use Tests\TestCase;

class MovieDTOTest extends TestCase
{
    public function test_creates_a_movie_dto_with_proper_properties()
    {
        $userId = 1;
        $title = 'Inception';
        $description = 'A mind-bending thriller by Christopher Nolan';

        $movieDTO = new MovieDTO($userId, $title, $description);

        $this->assertEquals($userId, $movieDTO->userId);
        $this->assertEquals($title, $movieDTO->title);
        $this->assertEquals($description, $movieDTO->description);
    }
}
