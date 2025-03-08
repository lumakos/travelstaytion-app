@extends('layouts.app')

@section('content')
    <div class="max-w-lg mx-auto p-6 bg-white shadow rounded-lg">
        <h1 class="text-xl font-bold">Add New Movie</h1>
        <form action="{{ route('movies.store') }}" method="POST" class="mt-4">
            @csrf
            <input type="text" name="title" placeholder="Title" class="w-full border rounded p-2 mb-2" required>
            <textarea name="description" placeholder="Description" class="w-full border rounded p-2 mb-2" required></textarea>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Save</button>
        </form>
    </div>
@endsection
