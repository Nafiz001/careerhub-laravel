<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\NewApplicationReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employer_is_notified_when_a_seeker_applies(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);

        $this->actingAs($seeker)
            ->post(route('applications.store', $job), [
                'cover_letter' => 'I am very interested in this role and believe I am a strong fit.',
            ]);

        Notification::assertSentTo(
            $employer,
            NewApplicationReceived::class,
            fn ($notification) => in_array('mail', $notification->via($employer), true)
                && in_array('database', $notification->via($employer), true)
        );
    }

    public function test_seeker_is_notified_when_status_changes(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'status' => 'pending',
        ]);

        $this->actingAs($employer)
            ->patch(route('applications.status', $application), ['status' => 'accepted'])
            ->assertRedirect();

        Notification::assertSentTo($seeker, ApplicationStatusUpdated::class);
    }

    public function test_no_notification_when_status_is_unchanged(): void
    {
        Notification::fake();

        $employer = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'status' => 'pending',
        ]);

        $this->actingAs($employer)
            ->patch(route('applications.status', $application), ['status' => 'pending']);

        Notification::assertNothingSentTo($seeker);
    }

    public function test_status_notification_is_persisted_to_database(): void
    {
        $employer = User::factory()->employer()->create();
        $seeker = User::factory()->seeker()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'status' => 'pending',
        ]);

        $this->actingAs($employer)
            ->patch(route('applications.status', $application), ['status' => 'reviewed']);

        $this->assertSame(1, $seeker->fresh()->notifications()->count());
        $this->assertSame(1, $seeker->fresh()->unreadNotifications()->count());
    }

    public function test_unread_count_is_shared_with_inertia(): void
    {
        $seeker = User::factory()->seeker()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
            'status' => 'pending',
        ]);

        // Generate one persisted notification for the seeker.
        $seeker->notify(new ApplicationStatusUpdated($application->load('job')));

        $this->actingAs($seeker)
            ->get(route('jobs.index'))
            ->assertInertia(fn ($page) => $page->where('unreadNotifications', 1));
    }

    public function test_user_can_mark_a_notification_as_read(): void
    {
        $seeker = User::factory()->seeker()->create();
        $employer = User::factory()->employer()->create();
        $job = Job::factory()->create(['employer_id' => $employer->id]);
        $application = Application::factory()->create([
            'job_post_id' => $job->id,
            'seeker_id' => $seeker->id,
        ]);

        $seeker->notify(new ApplicationStatusUpdated($application->load('job')));
        $notification = $seeker->notifications()->first();

        $this->actingAs($seeker)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertSame(0, $seeker->fresh()->unreadNotifications()->count());
    }
}
