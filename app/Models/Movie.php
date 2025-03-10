<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function likes()
    {
        return $this->votes()->where('vote', 'like');
    }

    public function hates()
    {
        return $this->votes()->where('vote', 'hate');
    }

    public function userVotes($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::parse($this->created_at)->format('d/m/Y');
    }

    public function getUserVoteAttribute()
    {
        $userId = Auth::id();
        $cacheKey = "user_{$userId}_movie_{$this->id}_vote";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Static array to hold user's votes
        static $userVotes = null;

        if ($userVotes === null) {
            $userVotes = Vote::where('user_id', $userId)->pluck('vote', 'movie_id');
        }

        $vote = $userVotes[$this->id] ?? null;

        Cache::put($cacheKey, $vote, now()->addDay());

        return $vote;
    }
}
