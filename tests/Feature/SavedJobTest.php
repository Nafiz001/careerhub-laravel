<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeker_can_toggle_a_saved_job(): void
    {
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();

        // First toggle saves it.
        $this->actingAs($seeker)
            ->post(route('saved-jobs.toggle', $job))
            ->assertRedirect();

        $this->assertDatabaseHas('saved_jobs', [
            'user_id' => $seeker->id,
            'job_post_id' => $job->id,
        ]);
        $this->assertSame(1, $seeker->savedJobs()->count());

        // Second toggle removes it.
        $this->actingAs($seeker)
            ->post(route('saved-jobs.toggle', $job))
            ->assertRedirect();

        $this->assertDatabaseMissing('saved_jobs', [
            'user_id' => $seeker->id,
            'job_post_id' => $job->id,
        ]);
        $this->assertSame(0, $seeker->fresh()->savedJobs()->count());
    }

    public function test_saved_jobs_are_unique_per_user(): void
    {
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();

        $seeker->savedJobs()->syncWithoutDetaching([$job->id]);
        $seeker->savedJobs()->syncWithoutDetaching([$job->id]);

        $this->assertSame(1, $seeker->savedJobs()->count());
    }

    public function test_employer_cannot_save_jobs(): void
    {
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create();

        $this->actingAs($employer)
            ->post(route('saved-jobs.toggle', $job))
            ->assertForbidden();

        $this->assertDatabaseCount('saved_jobs', 0);
    }

    public function test_saved_jobs_index_lists_bookmarked_jobs(): void
    {
        $seeker = User::factory()->seeker()->create();
        $saved = Job::factory()->create(['title' => 'Saved Role']);
        Job::factory()->create(['title' => 'Unsaved Role']);

        $seeker->savedJobs()->attach($saved->id);

        $this->actingAs($seeker)
            ->get(route('saved-jobs.index'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('SavedJobs/Index')
                ->has('jobs.data', 1)
                ->where('jobs.data.0.title', 'Saved Role'));
    }

    public function test_saved_count_is_shared_with_inertia(): void
    {
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create();
        $seeker->savedJobs()->attach($job->id);

        $this->actingAs($seeker)
            ->get(route('jobs.index'))
            ->assertInertia(fn ($page) => $page->where('savedCount', 1));
    }
}
