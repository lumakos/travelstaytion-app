<?php

namespace Tests\Unit\Controllers;

use App\Events\UserVoted;
use App\Models\Movie;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class VoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_a_movie(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::LIKE_VOTE])
            ->assertRedirect();

        Event::assertDispatched(UserVoted::class, function ($event) use ($user, $movie) {
            return $event->userId === $user->id && $event->movie->id === $movie->id && $event->vote === Vote::LIKE_VOTE;
        });
    }

    public function test_user_can_hate_a_movie(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::HATE_VOTE])
            ->assertRedirect();

        Event::assertDispatched(UserVoted::class, function ($event) use ($user, $movie) {
            return $event->userId === $user->id && $event->movie->id === $movie->id && $event->vote === Vote::HATE_VOTE;
        });
    }

    public function test_user_can_change_vote(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        Vote::create(['user_id' => $user->id, 'movie_id' => $movie->id, 'vote' => Vote::LIKE_VOTE]);

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::HATE_VOTE])
            ->assertRedirect();

        Event::assertDispatched(UserVoted::class);
    }

    public function test_user_can_remove_vote(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $movie = Movie::factory()->create();
        Vote::create(['user_id' => $user->id, 'movie_id' => $movie->id, 'vote' => Vote::LIKE_VOTE]);

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::LIKE_VOTE])
            ->assertRedirect();

        Event::assertDispatched(UserVoted::class);
    }

    public function test_invalid_vote_is_rejected(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => 'invalid_vote'])
            ->assertSessionHasErrors('vote');
    }

    public function test_cache_is_cleared_on_vote(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $movie = Movie::factory()->create();

        Cache::put("user_{$user->id}_movie_{$movie->id}_vote", Vote::LIKE_VOTE, 3600);

        $this->actingAs($user)
            ->post(route('movies.vote', $movie), ['vote' => Vote::HATE_VOTE])
            ->assertRedirect();

        Event::assertDispatched(UserVoted::class);

        $latestVote = Vote::where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->value('vote');

        $this->assertNotEquals(Cache::get("user_{$user->id}_movie_{$movie->id}_vote"), $latestVote);
    }
}
