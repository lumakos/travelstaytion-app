<div class="mt-6">
    {{ $movies->appends(['sort' => request('sort')])->links() }}
</div>
