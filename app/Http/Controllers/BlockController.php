<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        // Inject the UserService into the controller
        $this->userService = $userService;
    }

    /**
     * Block a user.
     *
     * @param \App\Models\User $blocked
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block(User $blocked)
    {
        $blocker = Auth::user(); // Get the current authenticated user

        // Perform the blocking action
        $this->userService->block($blocker, $blocked);

        return back()->with('success', "You have blocked {$blocked->nickname}.");
    }

    /**
     * Unblock a user.
     *
     * @param \App\Models\User $blocked
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblock(User $blocked)
    {
        $blocker = Auth::user(); // Get the current authenticated user

        // Perform the unblocking action
        $this->userService->unblock($blocker, $blocked);

        return back()->with('success', "You have unblocked {$blocked->nickname}.");
    }
}
