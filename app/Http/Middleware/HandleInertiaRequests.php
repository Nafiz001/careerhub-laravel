<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user
                    ? $user->only([
                        'id', 'name', 'email', 'role', 'avatar',
                        'headline', 'location', 'company_name', 'logo',
                    ])
                    : null,
            ],
            // Lazily-evaluated badges for the navbar (only computed when authed).
            'savedCount' => fn () => $user && $user->isSeeker()
                ? $user->savedJobs()->count()
                : 0,
            'unreadNotifications' => fn () => $user
                ? $user->unreadNotifications()->count()
                : 0,
            'recentNotifications' => fn () => $user
                ? $user->notifications()->latest()->limit(5)->get()
                    ->map(fn ($n) => [
                        'id' => $n->id,
                        'data' => $n->data,
                        'read_at' => $n->read_at,
                        'created_at' => $n->created_at,
                    ])
                : [],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
