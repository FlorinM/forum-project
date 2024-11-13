<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // Method for showing subcategories and threads
    public function showSubcategories(Category $category)
    {
        // Fetch the subcategories (immediate subcategories)
        $subcategories = $category->subcategories;

        // Fetch the threads for the current category (eager load the user for each thread)
        $threads = $category->threads()->with('user')->get();

        // Return the view and pass the category, its subcategories, and threads
        return Inertia::render('Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
            'threads' => $threads,  // Pass the threads to the view
        ]);
    }
}
