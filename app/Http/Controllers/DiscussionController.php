<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StartDiscussionRequest;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    //
    public function inbox() {
        return Inertia::render('Discussions/Inbox');
    }

    public function store(StartDiscussionRequest $request) {
        // Do nothing, just test the validation so far

        return back();
    }
}
