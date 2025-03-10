<?php

namespace App\DTOs;

class MovieDTO
{
    public int $userId;
    public string $title;
    public string $description;

    public function __construct(int $userId, string $title, string $description)
    {
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
    }
}
