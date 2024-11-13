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
        // Fetch only the current category and its immediate subcategories
        $subcategories = $category->subcategories;  // No need for nested subcategories

        // Return the view and pass the category and its direct subcategories
        return Inertia::render('Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
        ]);
    }
}
