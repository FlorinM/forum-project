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
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function block(int $userId)
    {
        $blocker = Auth::user(); // Get the current authenticated user
        $blocked = User::findOrFail($userId);

        if (!$blocked->id) {
            return back()->with('error', 'Block operation failed.');
        }

        // Perform the blocking action
        $this->userService->block($blocker, $blocked);

        return back()->with('success', "You have blocked {$blocked->nickname}.");
    }

    /**
     * Unblock a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unblock(int $userId)
    {
        $blocker = Auth::user(); // Get the current authenticated user
        $blocked = User::findOrFail($userId);

        if (!$blocked->id) {
            return back()->with('error', 'Unblock operation failed.');
        }

        // Perform the unblocking action
        $this->userService->unblock($blocker, $blocked);

        return back()->with('success', "You have unblocked {$blocked->nickname}.");
    }
}
