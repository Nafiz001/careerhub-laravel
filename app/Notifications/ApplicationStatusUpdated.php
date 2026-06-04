<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the seeker when an employer changes their application's status.
 * Delivered via the database (in-app bell) and mail channels.
 */
class ApplicationStatusUpdated extends Notification
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
        $status = $this->application->status;

        $message = (new MailMessage)
            ->subject('Update on your application for '.$job->title)
            ->greeting('Hello '.$notifiable->name.',')
            ->line('The status of your application for "'.$job->title.'" at '.$job->company.' is now: '.ucfirst($status).'.');

        if ($status === 'accepted') {
            $message->line('Congratulations! The employer would like to move forward with you.');
        } elseif ($status === 'rejected') {
            $message->line('Unfortunately the employer has decided not to proceed this time. Keep going, the right role is out there.');
        }

        return $message
            ->action('View your applications', url(route('applications.index', [], false)))
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

        return [
            'type' => 'application_status',
            'title' => 'Application status updated',
            'message' => 'Your application for "'.$job->title.'" is now '.$this->application->status.'.',
            'application_id' => $this->application->id,
            'job_id' => $job->id,
            'job_title' => $job->title,
            'status' => $this->application->status,
            'url' => route('applications.index', [], false),
        ];
    }
}
