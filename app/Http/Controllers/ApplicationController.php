<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\NewApplicationReceived;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ApplicationController extends Controller
{
    /**
     * Seeker submits an application to a job (once), optionally attaching a
     * PDF/DOC resume. The owning employer is notified.
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
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $attributes = [
            'job_post_id' => $job->id,
            'seeker_id' => $request->user()->id,
            'cover_letter' => $data['cover_letter'],
            'status' => 'pending',
        ];

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $attributes['resume_path'] = $file->store('resumes', 'public');
            $attributes['resume_name'] = $file->getClientOriginalName();
        }

        $application = Application::create($attributes);

        // Notify the employer (database + mail).
        $application->loadMissing('job.employer', 'seeker');
        $job->employer->notify(new NewApplicationReceived($application));

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
            ->with('seeker:id,name,email,headline,location,experience_level')
            ->latest()
            ->get();

        return Inertia::render('Jobs/Applicants', [
            'job' => $job->only(['id', 'title', 'company', 'location', 'type', 'is_open']),
            'applicants' => $applicants,
            'statuses' => Application::STATUSES,
        ]);
    }

    /**
     * Employer updates an applicant's status; the seeker is notified.
     */
    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('viewApplicants', $application->job);

        $data = $request->validate([
            'status' => ['required', Rule::in(Application::STATUSES)],
        ]);

        $changed = $application->status !== $data['status'];

        $application->update($data);

        if ($changed) {
            $application->loadMissing('seeker', 'job');
            $application->seeker->notify(new ApplicationStatusUpdated($application));
        }

        return back()->with('success', 'Applicant status updated.');
    }

    /**
     * Owning employer downloads an applicant's uploaded resume.
     */
    public function downloadResume(Request $request, Application $application): mixed
    {
        $this->authorize('viewApplicants', $application->job);

        abort_unless($application->resume_path && Storage::disk('public')->exists($application->resume_path), 404);

        return Storage::disk('public')->download(
            $application->resume_path,
            $application->resume_name ?? basename($application->resume_path)
        );
    }
}
