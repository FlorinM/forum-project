<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'captcha' => ['required', function ($attribute, $value, $fail) {
                // Retrieve the numbers stored in the session
                $captchaNumbers = Session::get('captcha_numbers', []);

                // Calculate the sum of the numbers
                $calculatedSum = array_sum($captchaNumbers);

                // Check if the provided sum matches the calculated sum
                if ((int) $value !== $calculatedSum) {
                    // Clear the session data if CAPTCHA is incorrect
                    Session::forget('captcha_numbers');
                    $fail('The sum of the CAPTCHA numbers is incorrect.');
                }
            }],
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:15',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'nickname' => $request->nickname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        //return redirect(route('dashboard', absolute: false)); original code
        return redirect('/');
    }
}
