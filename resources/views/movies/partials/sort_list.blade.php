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
