<?php

namespace App\Http\Controllers;

use App\Events\UserVoted;
use App\Http\Requests\Movie\VoteRequest;
use App\Models\Movie;

class VoteController extends Controller
{
    /**
     * @param VoteRequest $request
     * @param Movie $movie
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vote(VoteRequest $request, Movie $movie)
    {
        $userId = auth()->id();
        $vote = $request->validated()['vote'];

        event(new UserVoted($userId, $movie, $vote));

        return redirect()->back();
    }
}
