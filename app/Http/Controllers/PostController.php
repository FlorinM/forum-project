<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;

class PostController extends Controller
{
    public function show (Category $category, Thread $thread)
    {
        $posts = $thread->posts()->with('user')->get(); // Eager load the user relationship

        return Inertia::render('Posts/Show', [
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts
        ]);
    }
}
