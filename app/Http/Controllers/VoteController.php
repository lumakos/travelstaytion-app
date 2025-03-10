<?php

namespace App\Http\Controllers;

use App\Helpers\CacheHelper;
use App\Http\Requests\Movie\VoteRequest;
use App\Models\Movie;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;

class VoteController extends Controller
{
    public function vote(VoteRequest $request, Movie $movie)
    {
        $userId = auth()->id();
        $requestData = $request->validated();

        $existingVote = $movie->userVotes($userId);

        // Flush movie's cache key
        CacheHelper::clearMovieCache($movie->id);

        if ($existingVote) {
            if ($existingVote->vote == $requestData['vote']) {
                Cache::forget("user_{$userId}_movie_{$existingVote->movie_id}_vote");
                $existingVote->delete();

                return back();
            } else {
                $existingVote->update(['vote' => $requestData['vote']]);
            }
        } else {
            Cache::forget("user_{$userId}_movie_{$movie->id}_vote");
            Vote::create([
                'user_id' => auth()->id(),
                'movie_id' => $movie->id,
                'vote' => $requestData['vote'],
            ]);
        }

        Cache::put("user_{$userId}_movie_{$movie->id}_vote", $requestData['vote'], now()->addDay());

        return back();
    }
}
