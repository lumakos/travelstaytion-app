<?php

namespace Tests\Unit\Controllers;

use App\Helpers\CacheHelper;
use App\Models\Movie;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class VoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_movie(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::LIKE_VOTE])
            ->assertRedirect();

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'vote' => Vote::LIKE_VOTE,
        ]);
    }

    public function test_user_can_hate_a_movie(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::HATE_VOTE])
            ->assertRedirect();

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'vote' => Vote::HATE_VOTE,
        ]);
    }

    public function test_user_can_change_vote(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $vote = Vote::create(['user_id' => $user->id, 'movie_id' => $movie->id, 'vote' => Vote::LIKE_VOTE]);

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::HATE_VOTE])
            ->assertRedirect();

        $this->assertDatabaseHas('votes', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
            'vote' => Vote::HATE_VOTE,
        ]);
    }

    public function test_user_can_remove_vote(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        $vote = Vote::create(['user_id' => $user->id, 'movie_id' => $movie->id, 'vote' => Vote::LIKE_VOTE]);

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::LIKE_VOTE])
            ->assertRedirect();

        $this->assertDatabaseMissing('votes', [
            'user_id' => $user->id,
            'movie_id' => $movie->id,
        ]);
    }

    public function test_cache_is_cleared_on_vote(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        Cache::shouldReceive('forget')->once();
        Cache::shouldReceive('put')->once();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::LIKE_VOTE])
            ->assertRedirect();
    }


    public function test_invalid_vote_is_rejected(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => 'invalid_vote'])
            ->assertSessionHasErrors('vote');
    }
}
