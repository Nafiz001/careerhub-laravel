<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_category(): void
    {
        Job::factory()->create(['category' => 'Engineering', 'title' => 'Engineer']);
        Job::factory()->create(['category' => 'Design', 'title' => 'Designer']);

        $this->get('/jobs?category=Design')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Designer'));
    }

    public function test_filter_by_experience_level(): void
    {
        Job::factory()->create(['experience_level' => 'senior', 'title' => 'Senior Role']);
        Job::factory()->create(['experience_level' => 'entry', 'title' => 'Entry Role']);

        $this->get('/jobs?experience_level=senior')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Senior Role'));
    }

    public function test_filter_by_remote(): void
    {
        Job::factory()->remote()->create(['title' => 'Remote Role']);
        Job::factory()->create(['remote' => false, 'title' => 'Onsite Role']);

        $this->get('/jobs?remote=1')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Remote Role'));
    }

    public function test_filter_by_salary_range_overlap(): void
    {
        // Requested window: 80k - 120k.
        Job::factory()->create(['title' => 'In Range', 'salary_min' => 90000, 'salary_max' => 130000]);
        Job::factory()->create(['title' => 'Too Low', 'salary_min' => 20000, 'salary_max' => 40000]);
        Job::factory()->create(['title' => 'Too High', 'salary_min' => 200000, 'salary_max' => 300000]);

        $this->get('/jobs?salary_min=80000&salary_max=120000')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'In Range'));
    }

    public function test_filter_by_skills(): void
    {
        Job::factory()->create(['title' => 'Laravel Job', 'skills' => ['PHP', 'Laravel']]);
        Job::factory()->create(['title' => 'React Job', 'skills' => ['React', 'TypeScript']]);

        $this->get('/jobs?skills=Laravel')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Laravel Job'));
    }

    public function test_combined_filters_narrow_results(): void
    {
        Job::factory()->create([
            'title' => 'Match',
            'category' => 'Engineering',
            'experience_level' => 'senior',
            'remote' => true,
            'salary_min' => 100000,
            'salary_max' => 150000,
            'skills' => ['PHP', 'Laravel'],
        ]);
        // Same category but not remote -> excluded.
        Job::factory()->create([
            'title' => 'No Match',
            'category' => 'Engineering',
            'experience_level' => 'senior',
            'remote' => false,
            'salary_min' => 100000,
            'salary_max' => 150000,
            'skills' => ['PHP', 'Laravel'],
        ]);

        $this->get('/jobs?category=Engineering&experience_level=senior&remote=1&skills=Laravel&salary_min=90000')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Match'));
    }

    public function test_search_also_matches_description(): void
    {
        Job::factory()->create(['title' => 'Backend', 'description' => 'We use Kubernetes heavily in production every day.']);
        Job::factory()->create(['title' => 'Frontend', 'description' => 'A pure design role with no infrastructure work at all.']);

        $this->get('/jobs?search=Kubernetes')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Backend'));
    }

    public function test_featured_jobs_are_ordered_first(): void
    {
        Job::factory()->create(['title' => 'Normal', 'is_featured' => false]);
        Job::factory()->featured()->create(['title' => 'Featured']);

        $this->get('/jobs')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->where('jobs.data.0.title', 'Featured'));
    }

    public function test_viewing_a_job_increments_its_view_count(): void
    {
        $job = Job::factory()->create(['views_count' => 0]);

        $this->get(route('jobs.show', $job))->assertOk();

        $this->assertSame(1, $job->fresh()->views_count);
    }
}
