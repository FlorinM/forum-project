<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home page with all root categories and their subcategories.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Fetch all root categories with all nested subcategories
        $categories = Category::whereNull('parent_id')
            ->with('subcategories')
            ->get();

        // Initialize latestPosts and threadCounts arrays
        $latestPosts = [];
        $threadCounts = [];

        foreach ($categories as $category) {
            $latestPosts[$category->id] = [];
            $threadCounts[$category->id] = [];

            foreach ($category->subcategories as $subcategory) {
                // Get latest post with thread and user
                $latestPost = $subcategory->threads()
                    ->where('approved', 1)
                    ->latest('created_at')
                    ->first()?->posts()
                    ->where('approved', 1)
                    ->latest('created_at')
                    ->with(['thread', 'user'])
                    ->first();

                // Count threads in the subcategory
                $threadCount = $subcategory->threads()->count();

                // Store data in arrays
                $latestPosts[$category->id][$subcategory->id] = $latestPost;
                $threadCounts[$category->id][$subcategory->id] = $threadCount;
            }
        }

        // Pass data to Inertia
        return Inertia::render('Home/Home', [
            'categories' => $categories,
            'latestPosts' => $latestPosts,
            'threadCounts' => $threadCounts,
        ]);
    }
}
