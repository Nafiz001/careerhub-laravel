<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Role-aware dashboard: employers see their jobs + applicant counts,
     * seekers see their submitted applications.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        if ($user->isEmployer()) {
            $jobs = $user->jobs()
                ->withCount('applications')
                ->latest()
                ->get();

            return Inertia::render('Dashboard/Employer', [
                'jobs' => $jobs,
                'stats' => [
                    'jobs' => $jobs->count(),
                    'open' => $jobs->where('is_open', true)->count(),
                    'applicants' => $jobs->sum('applications_count'),
                ],
            ]);
        }

        $applications = $user->applications()
            ->with('job:id,title,company,location,type,is_open')
            ->latest()
            ->get();

        return Inertia::render('Dashboard/Seeker', [
            'applications' => $applications,
            'stats' => [
                'applications' => $applications->count(),
                'pending' => $applications->where('status', 'pending')->count(),
                'accepted' => $applications->where('status', 'accepted')->count(),
            ],
        ]);
    }
}
