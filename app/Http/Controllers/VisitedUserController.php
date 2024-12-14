<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitedUserController extends Controller
{
    /**
     * Display the profile of a visited user.
     *
     * @param User $user The user whose profile is being viewed. This will be automatically injected by the route model binding.
     * @return \Inertia\Response Renders the profile page of the specified user.
     */
    public function showProfile(User $user)
    {
        // You can add additional logic here, like checking if the user exists
        return Inertia::render('VisitedUser/Profile', [
            'user' => $user,
        ]);
    }
}
