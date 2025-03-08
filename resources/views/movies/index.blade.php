@extends('layouts.app')

@section('content')
    <div
        class="sm:justify-center sm:max-w-2xl mt-6 px-6 py-6 bg-white shadow rounded-lg min-h-screen justify-center mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl font-bold"><a href="{{ route('movies.index') }}">Movie World</a></h1>

            <div class="authentication flex justify-between items-center">
                @guest
                    <p><a href="{{ route('login') }}" class="text-blue-500 font-semibold">Log in </a></p>
                    <p class="ml-1 mr-1">or</p>
                    <p><a href="{{ route('register') }}" class="px-4 py-2 border border-blue-500 text-white font-semibold rounded-xl outline outline-2 outline-blue-700 bg-blue-500 hover:bg-blue-600 transition">Sign Up</a></p>
                @else
                    Welcome Back <p class="ml-2 text-blue-600">{{ auth()->user()->name }}</p>
                @endguest
            </div>
        </div>

        <div class="font-semibold">Found {{$total_movies}} movies</div>

        <div class="flex">
            <div class="w-4/5">
                <div id="movie-list">
                    @include('movies.partials.movie_list', ['movies' => $movies, 'sort' => $sort])
                </div>
            </div>

            <div class="w-1/5 pl-4">
                @auth
                    <div class="text-center flex flex-col mt-4 rounded-xl outline outline-10 outline-black-700 shadow bg-green-400">
                        <a href="{{ route('movies.create') }}" class="p-2 rounded text-white">New Movie</a>
                    </div>
                @endauth
                <div class="px-2 py-2 text-center flex flex-col space-y-4 mt-4 rounded-xl outline outline-10 outline-black-700 shadow bg-blue-100">
                        <p class="font-bold border-b-4 border-blue-500 pb-2">Sort by:</p>
                        @foreach (['likes' => 'Likes', 'hates' => 'Hates', 'latest' => 'Dates'] as $sortKey => $sortLabel)
                            <a href="{{ request()->url() }}?{{ http_build_query(array_merge(request()->query(), ['sort' => $sortKey])) }}"
                               class="flex items-center justify-center gap-2 text-blue-600 px-2 pb-2 {{ in_array($sortKey, ['likes', 'hates']) ? 'border-b-4 border-blue-500' : '' }}">

                                <span class="flex items-center justify-center">{{ $sortLabel }}</span>
                                <div class="w-4 h-4 border-2 border-blue-500 flex items-center justify-center rounded-sm">
                                    @if(request('sort', 'latest') == $sortKey)
                                        <div class="w-2 h-2 bg-green-500 rounded-sm"></div>
                                    @endif
                                </div>

                            </a>
                        @endforeach

                </div>
                <div class="px-2 py-2 text-center flex flex-col space-y-4 mt-4 rounded-xl outline outline-10 outline-black-700 shadow">
                    @auth
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $movies->appends(['sort' => request('sort')])->links() }}
        </div>
    </div>
@endsection
