<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Role-aware dashboard. Employers get rich analytics for charts;
     * seekers see their submitted applications and saved jobs.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        if ($user->isEmployer()) {
            return $this->employer($request);
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
                'saved' => $user->savedJobs()->count(),
            ],
        ]);
    }

    /**
     * Employer dashboard + analytics payload consumed by the UI charts.
     */
    private function employer(Request $request): Response
    {
        $user = $request->user();

        $jobs = $user->jobs()
            ->withCount('applications')
            ->latest()
            ->get();

        $jobIds = $jobs->pluck('id');

        return Inertia::render('Dashboard/Employer', [
            'jobs' => $jobs,
            'stats' => [
                'jobs' => $jobs->count(),
                'open' => $jobs->where('is_open', true)->count(),
                'applicants' => $jobs->sum('applications_count'),
                'views' => (int) $jobs->sum('views_count'),
            ],
            'analytics' => [
                'applicationsPerDay' => $this->applicationsPerDay($jobIds),
                'funnel' => $this->statusFunnel($jobIds),
                'topJobs' => $this->topJobs($jobs),
                'totalViews' => (int) $jobs->sum('views_count'),
            ],
        ]);
    }

    /**
     * Applications received per day over the trailing 30 days (zero-filled),
     * for a time-series chart. Shape: [{date: 'YYYY-MM-DD', count: int}, ...].
     *
     * @param  Collection<int, int>  $jobIds
     * @return array<int, array{date: string, count: int}>
     */
    private function applicationsPerDay($jobIds): array
    {
        $start = Carbon::today()->subDays(29);

        $counts = Application::query()
            ->whereIn('job_post_id', $jobIds)
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $series = [];
        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $series[] = [
                'date' => $date,
                'count' => (int) ($counts[$date] ?? 0),
            ];
        }

        return $series;
    }

    /**
     * Funnel counts by application status across the employer's jobs.
     * Always returns every status (zero-filled) in canonical order.
     *
     * @param  Collection<int, int>  $jobIds
     * @return array<int, array{status: string, count: int}>
     */
    private function statusFunnel($jobIds): array
    {
        $counts = Application::query()
            ->whereIn('job_post_id', $jobIds)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(Application::STATUSES)
            ->map(fn (string $status) => [
                'status' => $status,
                'count' => (int) ($counts[$status] ?? 0),
            ])
            ->all();
    }

    /**
     * Top 5 of the employer's jobs by applicant count.
     *
     * @param  Collection<int, Job>  $jobs
     * @return array<int, array{id: int, title: string, applicants: int, views: int}>
     */
    private function topJobs($jobs): array
    {
        return $jobs
            ->sortByDesc('applications_count')
            ->take(5)
            ->values()
            ->map(fn (Job $job) => [
                'id' => $job->id,
                'title' => $job->title,
                'applicants' => (int) $job->applications_count,
                'views' => (int) $job->views_count,
            ])
            ->all();
    }
}
