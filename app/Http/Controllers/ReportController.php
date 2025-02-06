<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|min:5',
        ]);

        Report::create([
            'post_id' => $request->post_id,
            'reporter_id' => Auth::id(), // Get logged-in user
            'content' => $request->content,
            'status' => 'pending', // Default status
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }
}
