@extends('layouts.app')

@section('content')
    <div
        class="sm:justify-center sm:max-w-2xl mt-6 px-6 py-6 bg-white shadow rounded-lg min-h-screen justify-center mx-auto">
        <div class="flex justify-between items-center mb-4">
            @include('movies.partials.head')
        </div>

        <div>
            @include('movies.partials.total_movies', ['totalMovies' => $totalMovies])
        </div>

        <div class="flex">
            <div class="w-4/5">
                @include('movies.partials.movie_list', ['movies' => $movies, 'sort' => $sort])
            </div>

            <div class="w-1/5 pl-4">
                @auth
                    @include('movies.partials.btn_create_movie')
                @endauth
                @include('movies.partials.sort_list')
                @include('movies.partials.logout')
            </div>
        </div>
        @include('movies.partials.pagination', ['movies' => $movies])
    </div>
@endsection
