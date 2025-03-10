<h1 class="text-4xl font-bold"><a href="{{ route('movies.index') }}">Movie World</a></h1>

<div class="authentication flex justify-between items-center">
    @guest
        <p><a href="{{ route('login') }}" class="text-blue-500 font-semibold">Log in </a></p>
        <p class="ml-1 mr-1">or</p>
        <p><a href="{{ route('register') }}"
              class="px-4 py-2 border border-blue-500 text-white font-semibold rounded-xl outline outline-2 outline-blue-700 bg-blue-500 hover:bg-blue-600 transition">Sign
                Up</a></p>
    @else
        Welcome Back <p class="ml-2 text-blue-600">{{ auth()->user()->name }}</p>
    @endguest
</div>
