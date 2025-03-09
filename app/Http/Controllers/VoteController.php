<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class VoteController extends Controller
{
    public function vote(Request $request, Movie $movie)
    {
        $request->validate([
            'vote' => 'required|in:like,hate',
        ]);

        $existingVote = Vote::where('user_id', auth()->id())
            ->where('movie_id', $movie->id)
            ->first();

        //dd($existingVote, $movie->id, Cache::get('user_vote_' . auth()->id() . '_' . $movie->id));


        if ($existingVote) {
            if ($existingVote->vote == $request->vote) {
                Cache::forget('user_vote_' . auth()->id() . '_' . $existingVote->id);
                $existingVote->delete();
            } else {
                $existingVote->update(['vote' => $request->vote]);
            }
        } else {
            //dd(1);
            Cache::forget('user_vote_' . auth()->id() . '_' . $movie->id);
            Vote::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'vote' => $request->vote,
            ]);
        }





        // Clears movie cache
        MovieController::clearMovieCache($movie->id);

        $userId = auth()->id();
        $cacheKey = "user_vote_{$userId}_{$movie->id}";
        Cache::put($cacheKey, $request->vote, now()->addDay());

        return back();
    }
}
