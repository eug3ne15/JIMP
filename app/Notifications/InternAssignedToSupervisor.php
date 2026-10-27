<?php

namespace App\Notifications;

use App\Models\Application;
use App\Models\Internship;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InternAssignedToSupervisor extends Notification
{
    use Queueable;

    public function __construct(
        public Application $application,
        public ?Internship $internship = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $applicantName = $this->application->applicant_name ?? $this->application->user?->name ?? 'Intern';
        $applicantEmail = $this->application->user?->email ?? 'N/A';
        $courseName = $this->application->course?->name ?? $this->application->internship?->course?->name ?? 'Judiciary Internship Position';
        $institution = $this->application->institution ?? $this->application->user?->school ?? 'N/A';
        $startDate = $this->internship?->start_date ? $this->internship->start_date->format('M j, Y') : now()->format('M j, Y');
        $endDate = $this->internship?->end_date ? $this->internship->end_date->format('M j, Y') : now()->addMonths(3)->format('M j, Y');

        return (new MailMessage)
            ->subject("New Intern Assigned: {$applicantName} - {$courseName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been assigned as the supervisor for **{$applicantName}** under the Judiciary Internship Programme (JIMP).")
            ->line("Intern Placement Details:")
            ->line("• **Intern Name:** {$applicantName}")
            ->line("• **Email:** {$applicantEmail}")
            ->line("• **Position / Course:** {$courseName}")
            ->line("• **Institution:** {$institution}")
            ->line("• **Placement Period:** {$startDate} to {$endDate}")
            ->action('Open Supervisor Dashboard', route('supervisor.dashboard'))
            ->line("You can monitor the intern's assignments and submit your official performance evaluation directly through your Supervisor Dashboard.")
            ->line('Thank you for mentoring the next generation of legal and administrative professionals.');
    }
}
