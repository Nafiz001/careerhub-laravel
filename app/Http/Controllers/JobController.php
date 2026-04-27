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
     * Public, paginated job listing with search + filters.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'type', 'location']);

        $jobs = Job::query()
            ->with('employer:id,name')
            ->withCount('applications')
            ->where('is_open', true)
            ->filter($filters)
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Jobs/Index', [
            'jobs' => $jobs,
            'filters' => $filters,
            'types' => Job::TYPES,
            // Distinct locations power the filter dropdown.
            'locations' => Job::query()->where('is_open', true)
                ->distinct()->orderBy('location')->pluck('location'),
        ]);
    }

    /**
     * Public job detail. Surfaces whether the seeker already applied.
     */
    public function show(Request $request, Job $job): Response
    {
        $job->load('employer:id,name');

        $hasApplied = false;
        $canApply = false;

        if ($user = $request->user()) {
            $hasApplied = $job->applications()->where('seeker_id', $user->id)->exists();
            $canApply = $user->can('apply', $job) && ! $hasApplied;
        }

        return Inertia::render('Jobs/Show', [
            'job' => $job,
            'hasApplied' => $hasApplied,
            'canApply' => $canApply,
            'applicationsCount' => $job->applications()->count(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Job::class);

        return Inertia::render('Jobs/Create', [
            'types' => Job::TYPES,
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
            'salary_range' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'is_open' => ['required', 'boolean'],
        ]);
    }
}
