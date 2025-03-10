<div id="movie-list">
    <ul class="mt-4">
        @foreach($movies as $movie)
            <li class="p-4 rounded-xl outline outline-10 outline-black-700 shadow mb-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold">{{ $movie->title }}</h2>
                    <p class="text-gray-800 text-sm">
                        Posted {{ \Carbon\Carbon::parse($movie->created_at)->format('d/m/Y') }}</p>
                </div>

                <p class="text-gray-600">{{ $movie->description }}</p>

                <div class="mt-4 flex justify-between items-center">
                    <p id="movie-stats">
                        <span class="{{ $movie->user_vote == 'like' ? 'text-green-500' : 'text-gray-600' }}">
                            {{ $movie->likes }} likes
                        </span> |
                        <span class="{{ $movie->user_vote == 'hate' ? 'text-red-500' : 'text-gray-600' }}">
                            {{ $movie->hates }} hates
                        </span>
                    </p>

                    @auth
                        @if ($movie->user_id !== auth()->id())
                            <div class="flex space-x-2">
                                <form action="{{ route('movies.vote', $movie->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote" value="like">
                                    <button type="submit" class="px-2 py-1 rounded rounded-2xl {{ $movie->user_vote == 'like' ? 'bg-blue-500 text-white' : 'text-blue-500' }}">
                                        Like
                                    </button>
                                </form>

                                <div class="mt-2 w-px h-4 bg-gray-800"></div>

                                <form action="{{ route('movies.vote', $movie->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vote" value="hate">
                                    <button type="submit" class="px-2 py-1 rounded rounded-2xl {{ $movie->user_vote == 'hate' ? 'bg-blue-500 text-white' : 'text-blue-500' }}">
                                        Hate
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth

                    <p class="text-sm text-gray-500">
                        Posted by
                        <a href="{{ route('movies.user', $movie->user->id) }}" class="text-blue-500">
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
