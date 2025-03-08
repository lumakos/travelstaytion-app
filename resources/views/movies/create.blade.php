@extends('layouts.app')

@section('content')
    <div class="sm:justify-center sm:max-w-2xl mt-6 px-6 py-6 bg-white shadow rounded-lg justify-center mx-auto">
        <div class="mb-4">
            <h1 class="text-xl font-bold">Add New Movie</h1>
            <form action="{{ route('movies.store') }}" method="POST" class="mt-4">
                @csrf
                <input type="text" name="title" placeholder="Title" class="w-full border rounded p-2 mb-2" required>
                <textarea name="description" placeholder="Description" class="w-full border rounded p-2 mb-2" required></textarea>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Save</button>
            </form>
        </div>
    </div>
@endsection
