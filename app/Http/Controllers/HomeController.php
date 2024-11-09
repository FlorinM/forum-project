<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch all root categories with all nested subcategories
        $categories = Category::whereNull('parent_id')
            ->with('allSubcategories')
            ->get();

        // Pass the category data to the Inertia view
        return Inertia::render('Home/Home', [
            'categories' => $categories
        ]);
    }
}
