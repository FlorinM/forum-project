<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    // Method for showing subcategories
    public function showSubcategories(Category $category)
    {
        // Fetch all subcategories for the given category, including subcategories of subcategories
        $subcategories = $category->subcategories()->with('subcategories')->get();

        // Return the view and pass the category, its subcategories, and their subcategories
        return Inertia::render('Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
        ]);
    }
}
