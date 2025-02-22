<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Handles the search functionality by searching posts based on a query term.
     *
     * This method accepts a search query via the request, performs a search
     * on the 'content' field of the 'posts' table using a LIKE query, and
     * returns matching posts along with their associated thread relationships.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming request containing the search term.
     * @return \Inertia\Response  The response rendered with the search results.
     */
    public function search(Request $request)
    {
        $searchTerm = $request->input('query');

        // Perform the search in the posts table (with LIKE query)
        $posts = Post::where('content', 'like', '%' . $searchTerm . '%')
            ->whereNull('deleted_at')  // Ensure deleted posts are not included
            ->limit(50)
            ->get();                   // Get all matching posts

        // Lazy load the 'thread' relationship for each post
        $posts->load('thread');       // Lazy load the thread relationship

        // Return results to the frontend (Inertia.js)
        return Inertia::render('Search/SearchResults', [
            'query' => $request->input('query'),
            'posts' => $posts,
        ]);
    }
}
