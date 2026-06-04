<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_dashboard_returns_analytics_payload(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id, 'views_count' => 42]);

        Application::factory()->count(2)->create(['job_post_id' => $job->id, 'status' => 'pending']);
        Application::factory()->create(['job_post_id' => $job->id, 'status' => 'accepted']);

        $this->actingAs($employer)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard/Employer')
                ->has('analytics.applicationsPerDay', 30)
                ->has('analytics.funnel', 4)
                ->has('analytics.topJobs')
                ->where('analytics.totalViews', 42)
                ->where('stats.applicants', 3));
    }

    public function test_funnel_counts_match_statuses(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);

        Application::factory()->count(3)->create(['job_post_id' => $job->id, 'status' => 'pending']);
        Application::factory()->create(['job_post_id' => $job->id, 'status' => 'rejected']);

        $this->actingAs($employer)
            ->get(route('dashboard'))
            ->assertInertia(fn ($page) => $page
                ->where('analytics.funnel.0.status', 'pending')
                ->where('analytics.funnel.0.count', 3)
                ->where('analytics.funnel.3.status', 'rejected')
                ->where('analytics.funnel.3.count', 1));
    }

    public function test_seeker_dashboard_includes_saved_count(): void
    {
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();
        $seeker->savedJobs()->attach($job->id);

        $this->actingAs($seeker)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Dashboard/Seeker')
                ->where('stats.saved', 1));
    }
}
