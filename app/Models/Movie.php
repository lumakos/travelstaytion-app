<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    public function getUserVoteAttribute()
    {
        $userId = Auth::id();

        $cacheKey = "user_vote_{$userId}_{$this->id}";

        if (Cache::has($cacheKey)) {
            //dd($cacheKey, Cache::get($cacheKey));
            return Cache::get($cacheKey);
        }

        $vote = $this->votes()
            ->where('user_id', $userId)
            ->select('vote')
            ->first()
            ->vote ?? null;

        Cache::put($cacheKey, $vote, now()->addDay());

        return $vote;
    }
}
