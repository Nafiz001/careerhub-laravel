<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    /**
     * Seeker submits an application to a job (once).
     */
    public function store(Request $request, Job $job): RedirectResponse
    {
        $this->authorize('apply', $job);

        $alreadyApplied = $job->applications()
            ->where('seeker_id', $request->user()->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->with('error', 'You have already applied to this job.');
        }

        $data = $request->validate([
            'cover_letter' => ['required', 'string', 'min:20', 'max:5000'],
        ]);

        Application::create([
            'job_post_id' => $job->id,
            'seeker_id' => $request->user()->id,
            'cover_letter' => $data['cover_letter'],
            'status' => 'pending',
        ]);

        return redirect()->route('applications.index')
            ->with('success', 'Application submitted!');
    }

    /**
     * Seeker dashboard: list of my applications.
     */
    public function index(Request $request): Response
    {
        $applications = $request->user()->applications()
            ->with('job:id,title,company,location,type,is_open')
            ->latest()
            ->get();

        return Inertia::render('Applications/Index', [
            'applications' => $applications,
        ]);
    }

    /**
     * Employer views applicants for one of their jobs.
     */
    public function forJob(Request $request, Job $job): Response
    {
        $this->authorize('viewApplicants', $job);

        $applicants = $job->applications()
            ->with('seeker:id,name,email')
            ->latest()
            ->get();

        return Inertia::render('Jobs/Applicants', [
            'job' => $job->only(['id', 'title', 'company', 'location', 'type', 'is_open']),
            'applicants' => $applicants,
            'statuses' => Application::STATUSES,
        ]);
    }

    /**
     * Employer updates an applicant's status.
     */
    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('viewApplicants', $application->job);

        $data = $request->validate([
            'status' => ['required', Rule::in(Application::STATUSES)],
        ]);

        $application->update($data);

        return back()->with('success', 'Applicant status updated.');
    }
}
