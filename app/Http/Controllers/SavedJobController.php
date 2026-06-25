<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SavedJobController extends Controller
{
    /**
     * Seeker's bookmarked jobs.
     */
    public function index(Request $request): Response
    {
        $jobs = $request->user()->savedJobs()
            ->with('employer:id,name')
            ->withCount('applications')
            ->latest('saved_jobs.created_at')
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('SavedJobs/Index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Toggle a bookmark for the authenticated seeker. Idempotent and
     * safe to call from the listing or detail page.
     */
    public function toggle(Request $request, Job $job): RedirectResponse
    {
        abort_unless($request->user()->isSeeker(), 403);

        $result = $request->user()->savedJobs()->toggle($job->id);

        $saved = in_array($job->id, $result['attached'], true);

        return back()->with('success', $saved ? 'Job saved.' : 'Job removed from saved.');
    }
}
