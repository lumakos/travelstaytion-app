@auth
    <div
        class="px-2 py-2 text-center flex flex-col space-y-4 mt-4 rounded-xl outline outline-10 outline-black-700 shadow">
        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
@endauth
