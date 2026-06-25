<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JobController extends Controller
{
    /**
     * Public, paginated job listing with search + advanced filters.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only([
            'search', 'type', 'location', 'category',
            'experience_level', 'remote', 'salary_min', 'salary_max', 'skills',
        ]);

        $jobs = Job::query()
            ->with('employer:id,name')
            ->withCount('applications')
            ->where('is_open', true)
            ->filter($filters)
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        // IDs the current seeker has saved, so the listing can show toggles.
        $savedJobIds = $request->user()?->isSeeker()
            ? $request->user()->savedJobs()->pluck('job_posts.id')
            : collect();

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'filters' => $filters,
            'types' => Job::TYPES,
            'categories' => Job::CATEGORIES,
            'experienceLevels' => Job::EXPERIENCE_LEVELS,
            'savedJobIds' => $savedJobIds,
            // Distinct locations power the filter dropdown.
            'locations' => Job::query()->where('is_open', true)
                ->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    /**
     * Public job detail. Surfaces whether the seeker already applied / saved,
     * and counts a view.
     */
    public function show(Request $request, Job $job): Response
    {
        // Count the view (does not touch updated_at).
        $job->incrementQuietly('views_count');

        $job->load('employer:id,name,company_name,logo,website,about');

        $hasApplied = false;
        $canApply = false;
        $isSaved = false;

        if ($user = $request->user()) {
            $hasApplied = $job->applications()->where('seeker_id', $user->id)->exists();
            $canApply = $user->can('apply', $job) && ! $hasApplied;
            $isSaved = $user->isSeeker()
                && $user->savedJobs()->where('job_posts.id', $job->id)->exists();
        }

        return Inertia::render('Jobs/Show', [
            'job' => $job,
            'hasApplied' => $hasApplied,
            'canApply' => $canApply,
            'isSaved' => $isSaved,
            'applicationsCount' => $job->applications()->count(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Job::class);

        return Inertia::render('Jobs/Create', [
            'types' => Job::TYPES,
            'categories' => Job::CATEGORIES,
            'experienceLevels' => Job::EXPERIENCE_LEVELS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Job::class);

        $data = $this->validateJob($request);
        $data['employer_id'] = $request->user()->id;

        Job::create($data);

        return redirect()->route('dashboard')
            ->with('success', 'Job posted successfully.');
    }

    public function edit(Job $job): Response
    {
        $this->authorize('update', $job);

        return Inertia::render('Jobs/Edit', [
            'job' => $job,
            'types' => Job::TYPES,
            'categories' => Job::CATEGORIES,
            'experienceLevels' => Job::EXPERIENCE_LEVELS,
        ]);
    }

    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->authorize('update', $job);

        $job->update($this->validateJob($request));

        return redirect()->route('dashboard')
            ->with('success', 'Job updated successfully.');
    }

    public function destroy(Job $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Job deleted.');
    }

    private function validateJob(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(Job::TYPES)],
            'category' => ['nullable', Rule::in(Job::CATEGORIES)],
            'experience_level' => ['nullable', Rule::in(Job::EXPERIENCE_LEVELS)],
            'salary_range' => ['nullable', 'string', 'max:255'],
            'salary_min' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'salary_max' => ['nullable', 'integer', 'min:0', 'max:100000000', 'gte:salary_min'],
            'description' => ['required', 'string', 'min:20'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:50'],
            'remote' => ['boolean'],
            'application_deadline' => ['nullable', 'date'],
            'is_featured' => ['boolean'],
            'is_open' => ['required', 'boolean'],
        ]);
    }
}
