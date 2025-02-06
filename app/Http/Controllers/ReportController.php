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

        // Check if the user has already reported this post
        $existingReport = Report::where('post_id', $request->post_id)
            ->where('reporter_id', Auth::id())
            ->first();

        if ($existingReport) {
            return back()->with('report', [
                'message' => 'You have already reported this post.',
                'post_id' => $request->post_id,
                'type' => 'error',
            ]);
        }

        Report::create([
            'post_id' => $request->post_id,
            'reporter_id' => Auth::id(), // Get logged-in user
            'content' => $request->content,
            'status' => 'pending', // Default status
        ]);

        return back()->with('report', [
            'message' => 'Report submitted successfully.',
            'post_id' => $request->post_id,
            'type' => 'success',
        ]);
    }
}
