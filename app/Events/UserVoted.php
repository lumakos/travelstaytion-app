<?php

namespace App\Events;

use App\Models\Movie;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserVoted
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public Movie $movie;
    public string $vote;

    /**
     * Create a new event instance.
     */
    public function __construct(int $userId, Movie $movie, string $vote)
    {
        $this->userId = $userId;
        $this->movie = $movie;
        $this->vote = $vote;
    }
}
