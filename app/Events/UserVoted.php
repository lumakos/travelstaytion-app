<?php

namespace App\Events;

use App\Models\Movie;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserVoted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public int $userId, public Movie $movie, public string $vote)
    {
    }
}
