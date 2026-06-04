<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the employer when a seeker applies to one of their jobs.
 * Delivered via the database (in-app bell) and mail channels.
 */
class NewApplicationReceived extends Notification
{
    use Queueable;

    public function __construct(public Application $application) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $job = $this->application->job;
        $seeker = $this->application->seeker;

        return (new MailMessage)
            ->subject('New application for '.$job->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line($seeker->name.' has applied to your "'.$job->title.'" listing.')
            ->action('Review applicants', url(route('jobs.applicants', $job, false)))
            ->line('Thank you for using CareerHub.');
    }

    /**
     * Stored shape for the in-app notifications feed.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $job = $this->application->job;
        $seeker = $this->application->seeker;

        return [
            'type' => 'new_application',
            'title' => 'New application received',
            'message' => $seeker->name.' applied to "'.$job->title.'".',
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'seeker_name' => $seeker->name,
            'url' => route('jobs.applicants', $job, false),
        ];
    }
}
