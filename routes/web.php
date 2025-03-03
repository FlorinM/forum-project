<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\NewTopicsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\VisitedUserController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
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
    Route::post('/profile', [ProfileController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/profile', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
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

    Route::post('/message', [MessageController::class, 'sendMessage'])
        ->name('message.send')
        ->middleware([HandlePrecognitiveRequests::class]);

    Route::get('/visited/{user}', [VisitedUserController::class, 'showProfile'])->name('visited.user.show');
    Route::get('/visited-user-threads/{user}', [VisitedUserController::class, 'fetchThreads'])
        ->name('visited.user.threads');
    Route::get('/visited-user-posts/{user}', [VisitedUserController::class, 'fetchPosts'])
        ->name('visited.user.posts');

    Route::get('/discussions/', [DiscussionController::class, 'show'])->name('discussions');
    Route::post('/discussions-start/', [DiscussionController::class, 'store'])
        ->name('discussions.start')
        ->middleware([HandlePrecognitiveRequests::class]);
    Route::get('/discussions-inbox/{user}', [DiscussionController::class, 'inbox'])
        ->name('discussions-inbox');
    Route::get('/discussions-sent/{user}', [DiscussionController::class, 'sent'])
        ->name('discussions-sent');
    Route::get('/discussions-show/{discussion}', [DiscussionController::class, 'showDiscussion'])
        ->name('discussions.show');

    Route::post('/messages-send/', [MessageController::class, 'sendMessage'])
        ->name('send.message')
        ->middleware([HandlePrecognitiveRequests::class]);

    Route::get('/notifications-unread', [NotificationController::class, 'unreadNotifications'])
        ->name('notifications.unread');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::get('/followed-content', [ThreadController::class, 'contentIFollow'])
        ->name('followed.content');

    Route::post('/reports', [ReportController::class, 'store'])
        ->name('report.post');

    Route::post('/block/{user}', [BlockController::class, 'block'])->name('block');
    Route::post('/unblock/{user}', [BlockController::class, 'unblock'])->name('unblock');

    Route::post('/thread/{thread}/read', [ThreadController::class, 'markAsRead'])
        ->name('thread.read.at');
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/categories/{category}/threads/{thread}', [PostController::class, 'show'])->name('threads.show');
Route::get('/post/{post}', [PostController::class, 'showByPostId'])->name('find.post');
Route::get('/categories/{category}/subcategories', [CategoryController::class, 'showSubcategories'])->name('categories.subcategories');
Route::get('/new-topics', [NewTopicsController::class, 'index'])->name('new-topics.index');
Route::get('/captcha', [CaptchaController::class, 'generate'])->middleware('throttle:10,1');
Route::post('/search', [SearchController::class, 'search'])->name('search');
Route::get('/search', function () {
    return back();
});





