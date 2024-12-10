<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @param ImageManager $imageManager
     */
    public function __construct (ImageManager $imageManager) {
        $this->imageManager = $imageManager;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'avatar' => $request->user()->avatar_url,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's avatar
     */
    public function updateAvatar(Request $request)
    {
        // Validate the avatar file input (optional if you're already doing this in Vue)
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = $request->user();

        // Check if the request contains a file and it's an image
        if ($request->file('avatar')) {
            // If the user already has an avatar, delete the old file
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            // Store the new image
            $path = $request->file('avatar')->store('avatars', 'public');

            // Transform the new image to an avatar by rescaling
            $fullPath = storage_path('app/public/' . $path);
            $image = $this->imageManager->read($fullPath);
            $image->scale(height: 200);
            $image->save($fullPath, 100);

            // Update the user's avatar URL in the database
            $user->update(['avatar_url' => $path]);
        }

        return Redirect::route('profile.edit')->with('status', 'Avatar updated successfully!');
    }


    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        // Check if the user has an avatar
        if ($user->avatar_url) {
            // Delete the avatar from storage
            Storage::disk('public')->delete($user->avatar_url);

            // Set avatar_url to null in the database
            $user->update(['avatar_url' => null]);
        }

        return Redirect::route('profile.edit')->with('status', 'Avatar removed successfully!');
    }
}
