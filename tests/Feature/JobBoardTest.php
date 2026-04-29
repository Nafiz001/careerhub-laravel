<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_listing_only_shows_open_jobs(): void
    {
        Job::factory()->create(['title' => 'Open Role']);
        Job::factory()->closed()->create(['title' => 'Closed Role']);

        $this->get('/jobs')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Jobs/Index')
                ->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Open Role'));
    }

    public function test_search_filters_by_title(): void
    {
        Job::factory()->create(['title' => 'Laravel Developer']);
        Job::factory()->create(['title' => 'Designer']);

        $this->get('/jobs?search=Laravel')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Laravel Developer'));
    }

    public function test_type_filter_narrows_results(): void
    {
        Job::factory()->create(['type' => 'internship', 'title' => 'Intern']);
        Job::factory()->create(['type' => 'full-time', 'title' => 'Engineer']);

        $this->get('/jobs?type=internship')
            ->assertOk()
            ->assertInertia(fn ($page) => $page->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Intern'));
    }

    public function test_seeker_can_apply_once(): void
    {
        $seeker = User::factory()->create(['role' => 'seeker']);
        $job = Job::factory()->create();

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'I am very interested in this position and a strong fit.',
            ])
            ->assertRedirect(route('applications.index'));

        $this->assertDatabaseCount('applications', 1);

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'Trying to apply a second time to the same job here.',
            ]);

        $this->assertDatabaseCount('applications', 1);
    }

    public function test_employer_cannot_apply(): void
    {
        $employer = User::factory()->create(['role' => 'employer']);
        $job = Job::factory()->create();

        $this->actingAs($employer)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'Employers should not be able to apply to jobs at all.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('applications', 0);
    }

    public function test_only_owning_employer_can_view_applicants(): void
    {
        $owner = User::factory()->create(['role' => 'employer']);
        $other = User::factory()->create(['role' => 'employer']);
        $job = Job::factory()->create(['employer_id' => $owner->id]);

        $this->actingAs($other)
            ->get(route('jobs.applicants', $job))
            ->assertForbidden();

        $this->actingAs($owner)
            ->get(route('jobs.applicants', $job))
            ->assertOk();
    }

    public function test_seeker_cannot_create_jobs(): void
    {
        $seeker = User::factory()->create(['role' => 'seeker']);

        $this->actingAs($seeker)
            ->get(route('jobs.create'))
            ->assertForbidden();
    }
}
