<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;

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

/**
 * Always name route parameters {category} and {thread}
 */
Route::middleware(['auth'])->group(function () {
    Route::get('/categories/{category}/threads/create', [ThreadController::class, 'create'])
    ->name('threads.create');

    Route::post('/categories/{category}/threads', [ThreadController::class, 'store'])
    ->name('threads.store')
    ->middleware([HandlePrecognitiveRequests::class]);

    Route::post('/categories/{category}/threads/{thread}/posts', [PostController::class, 'store'])
    ->name('posts.store')
    ->middleware([HandlePrecognitiveRequests::class]);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories/{category}/threads/{thread}', [PostController::class, 'show'])->name('threads.show');
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'showSubcategories'])->name('categories.subcategories');




