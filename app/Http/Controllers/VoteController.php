<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function vote(Request $request, Movie $movie)
    {
        if ($movie->user_id === auth()->id()) {
            return back()->withErrors('You cannot vote for your own movie.');
        }

        $request->validate([
            'vote' => 'required|in:like,hate',
        ]);

        $existingVote = Vote::where('user_id', auth()->id())
            ->where('movie_id', $movie->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote == $request->vote) {
                $existingVote->delete();
            } else {
                $existingVote->update(['vote' => $request->vote]);
            }
        } else {
            Vote::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'vote' => $request->vote,
            ]);
        }

        return back();
    }
}
