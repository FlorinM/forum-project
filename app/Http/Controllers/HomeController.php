<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::all(); // Fetch all categories
        return Inertia::render('Home/Home', [
            'categories' => $categories
        ]);
    }
}
