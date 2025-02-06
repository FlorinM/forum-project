<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'roles' => $request->user()->getRoleNames(), // Include roles
                ] : null,
            ],
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'breadcrumbs' => breadcrumbs(),
            'baseUrl' => config('app.url'),

            // Share Laravel's flash messages with Inertia
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'banMessage' => fn () => $request->session()->get('banMessage'),
                'errorNewUserMessage' => fn () => $request->session()->get('errorNewUserMessage'),
                'errorSendMessage' => fn () => $request->session()->get('errorSendMessage'),
                'report' => fn () => $request->session()->get('report'),
            ],
        ];
    }
}
