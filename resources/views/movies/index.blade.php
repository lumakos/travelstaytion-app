@extends('layouts.app')

@section('content')
    <div
        class="sm:justify-center sm:max-w-2xl mt-6 px-6 py-6 bg-white shadow rounded-lg min-h-screen justify-center mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-4xl font-bold">Movie World</h1>

            <div class="authentication">
                @guest
                    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Log in</a> or
                    <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Sign Up</a>
                @else
                    Welcome Back {{ auth()->user()->name }}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-red-500">Logout</button>
                    </form>
                @endguest
            </div>
        </div>
        <div>
            Found 200 movies
        </div>

        <div class="flex">
            <div class="w-4/5">
                <ul class="mt-4">
                    @foreach ($movies as $movie)
                        <li class="p-4 bg-gray-100 rounded shadow">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-semibold">{{ $movie->title }}</h2>
                                <p class="text-gray-600 text-sm">Posted {{ \Carbon\Carbon::parse($movie->created_at)->format('d/m/Y') }}</p>
                            </div>

                            <p class="text-gray-600">{{ $movie->description }}</p>

                            <div class="mt-4 flex justify-between items-center">
                                <p>{{ $movie->likes }} likes | {{ $movie->hates }} hates</p>
                                @auth
                                    @if ($movie->user_id !== auth()->id())
                                        <form action="{{ route('movies.vote', $movie->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="vote" value="like">
                                            <button type="submit" class="px-2 py-1 rounded">Like</button>
                                        </form>
                                        |
                                        <form action="{{ route('movies.vote', $movie->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="vote" value="hate">
                                            <button type="submit" class="px-2 py-1 rounded">Hate</button>
                                        </form>
                                    @endif
                                @endauth
                                <p class="text-sm text-gray-500">
                                    Posted by
                                    <a href="#" class="text-blue-600">
                                        @if(auth()->check() && $movie->user->email == auth()->user()->email)
                                            You
                                        @else
                                            {{ $movie->user->name }}
                                        @endif
                                    </a>
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="w-1/5 pl-4">
                <div class="flex flex-col space-y-4 pt-4">
                    <p class="font-bold">Sort by:</p>
                    <a href="?sort=likes"
                       class="px-4 py-2 rounded {{ request('sort') == 'likes' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Likes</a>
                    <a href="?sort=hates"
                       class="px-4 py-2 rounded {{ request('sort') == 'hates' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Hates</a>
                    <a href="?sort=latest"
                       class="px-4 py-2 rounded {{ request('sort') == 'latest' ? 'bg-blue-500 text-white' : 'bg-gray-200' }}">Dates</a>
                </div>
            </div>
        </div>

        <div class="mt-6">
            {{ $movies->appends(['sort' => request('sort')])->links() }}
        </div>
    </div>
@endsection
