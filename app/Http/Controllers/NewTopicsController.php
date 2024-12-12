<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Thread;

class NewTopicsController extends Controller
{
    public function index()
    {
        // Fetch the latest 80 threads without pagination
        $threads = Thread::latest()->take(80)->get();

        // Return the data in a proper format for the frontend to handle pagination
        return response()->json([
            'threads' => $threads,
        ]);
    }
}
