<?php

namespace Tests\Unit\Controllers;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class MovieControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_movies(): void
    {
        $response = $this->get(route('movies.index'));
        $response->assertStatus(200);
        $response->assertViewIs('movies.index');
    }

    public function test_authenticated_user_can_create_movie(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $movieData = [
            'title' => 'Test Movie',
            'description' => 'This is a test description.',
        ];

        $response = $this->post(route('movies.store'), $movieData);

        $response->assertRedirect(route('movies.index'));
        $this->assertDatabaseHas('movies', [
            'title' => 'Test Movie',
            'description' => 'This is a test description.',
            'user_id' => $user->id,
        ]);
    }

    public function test_movie_creation_requires_validation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('movies.store'), []);

        $response->assertSessionHasErrors(['title', 'description']);
    }

    public function test_movies_are_cached(): void
    {
        $user = User::factory()->create();
        $movie = Movie::factory()->create(['user_id' => $user->id]);

        $this->get(route('movies.index'));

        $cacheKey = "movies_latest_page_1";
        $this->assertTrue(Cache::has($cacheKey));
    }
}

