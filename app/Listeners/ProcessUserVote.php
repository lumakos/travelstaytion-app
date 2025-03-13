<?php

namespace App\Listeners;

use App\Events\UserVoted;
use App\Helpers\CacheHelper;
use App\Models\Vote;
use Illuminate\Support\Facades\Cache;

class ProcessUserVote
{
    /**
     * Handle the event.
     */
    public function handle(UserVoted $event): void
    {
        $userId = $event->userId;
        $movie = $event->movie;
        $voteValue = $event->vote;

        try {
            $existingVote = $movie->userVotes($userId);

            // Flush movie's cache key
            CacheHelper::clearMovieCache($movie->id);

            if ($existingVote) {
                if ($existingVote->vote == $voteValue) {
                    Cache::forget("user_{$userId}_movie_{$existingVote->movie_id}_vote");
                    $existingVote->delete();

                    return;
                } else {
                    $existingVote->update(['vote' => $voteValue]);
                }
            } else {
                Cache::forget("user_{$userId}_movie_{$movie->id}_vote");
                $vote = Vote::create([
                    'user_id' => auth()->id(),
                    'movie_id' => $movie->id,
                    'vote' => $voteValue,
                ]);
            }
            Cache::put("user_{$userId}_movie_{$movie->id}_vote", $voteValue, CacheHelper::CACHE_DURATION);

        } catch (\Exception $e) {
            \Log::error('Error processing vote: ' . $e->getMessage(), [
                'user_id' => $userId,
                'movie_id' => $movie->id,
                'vote' => $voteValue,
            ]);
        }
    }
}
