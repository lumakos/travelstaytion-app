<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoteController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [MovieController::class, 'index'])->name('movies.index');

Route::get('/movies/user/{userId}', [MovieController::class, 'userMovies'])->name('movies.user');

Route::middleware('auth')->group(function () {
    Route::get('/movies/create', [MovieController::class, 'create'])->name('movies.create');
    Route::post('/movies', [MovieController::class, 'store'])->name('movies.store');

    Route::post('/movies/{movie}/vote', [VoteController::class, 'vote'])->name('movies.vote');
});

require __DIR__.'/auth.php';
