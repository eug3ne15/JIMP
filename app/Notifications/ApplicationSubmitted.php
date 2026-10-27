<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationSubmitted extends Notification
{
    use Queueable;

    public function __construct(public Application $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $courseName = $this->application->course?->name ?? 'Internship Position';
        $institution = $this->application->institution ?? $notifiable->school ?? 'N/A';
        $applicantName = $this->application->applicant_name ?? $notifiable->name;

        return (new MailMessage)
            ->subject("Judiciary Internship Application Received: {$courseName}")
            ->greeting("Hello {$applicantName},")
            ->line("Your application for the position of **{$courseName}** has been successfully submitted to the Judiciary Human Resources department.")
            ->line("Your application is sent, kindly wait for approval while our team reviews and vets your academic credentials.")
            ->line("Application Reference Details:")
            ->line("• **Position:** {$courseName}")
            ->line("• **Institution:** {$institution}")
            ->line("• **Status:** Pending HR Review & Approval")
            ->action('View Intern Dashboard', route('dashboard.intern'))
            ->line("You can monitor the vetting progress of your application and inspect your submitted documents on your intern dashboard.")
            ->line('Thank you for applying to the Judiciary Internship Programme (JIMP).');
    }
}
