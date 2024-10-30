<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use Inertia\Inertia;

class ThreadController extends Controller
{
    public function show(Category $category)
    {
        $threads = $category->threads()->with('user')->get(); // Eager load the user relationship
        return Inertia::render('Threads/Show', [
            'category' => $category,
            'threads' => $threads,
        ]);
    }
}
