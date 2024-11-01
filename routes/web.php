<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;

/*
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
*/

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/categories/{categoryId}/threads/create', [ThreadController::class, 'create'])
    ->name('threads.create');

    Route::post('/categories/{categoryId}/threads', [ThreadController::class, 'store'])
    ->name('threads.store');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories/{category}', [ThreadController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/threads/{thread}', [PostController::class, 'show'])->name('threads.show');




