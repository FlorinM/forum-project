<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    //
    public function inbox() {
        return Inertia::render('Discussions/Inbox');
    }
}
