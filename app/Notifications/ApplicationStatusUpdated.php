<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification
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
        $status = $this->application->status;
        $courseName = $this->application->course?->name ?? $this->application->internship?->course?->name ?? 'Internship Position';
        $applicantName = $this->application->applicant_name ?? $notifiable->name;

        $mail = new MailMessage;

        switch ($status) {
            case 'shortlisted':
                $mail->subject("Congratulations! Your Judiciary Internship Application has been Approved: {$courseName}")
                    ->greeting("Hello {$applicantName},")
                    ->line("We are pleased to inform you that your application for **{$courseName}** has been **approved and shortlisted** for the Judiciary Internship Programme.")
                    ->line("Our Human Resources team has reviewed and vetted your academic qualifications and supporting documents.")
                    ->line("Next Steps: Judiciary Human Resources will contact you regarding your deployment schedule, induction details, and supervisor assignment.");
                break;

            case 'rejected':
                $mail->subject("Update on your Judiciary Internship Application: {$courseName}")
                    ->greeting("Hello {$applicantName},")
                    ->line("Thank you for your interest and application for the **{$courseName}** position under the Judiciary Internship Programme.")
                    ->line("After careful review and vetting of all applications, we regret to inform you that your application was **not successful** for this intake.");

                if ($this->application->rejection_reason) {
                    $mail->line("Reason for Decision: {$this->application->rejection_reason}");
                }
                break;

            case 'info_requested':
                $mail->subject("Action Required: Additional Details Needed for your Internship Application - {$courseName}")
                    ->greeting("Hello {$applicantName},")
                    ->line("The Judiciary Human Resources department has reviewed your application for **{$courseName}** and **requires you to provide additional information or updated documents** before your application can be vetted further.")
                    ->line("Please review the HR remarks below and update your application details promptly.");
                break;

            default:
                $formattedStatus = str($status)->replace('_', ' ')->title();
                $mail->subject("Judiciary Internship Application Status Update: {$formattedStatus}")
                    ->greeting("Hello {$applicantName},")
                    ->line("The status of your application for **{$courseName}** has been updated to: **{$formattedStatus}**.");
                break;
        }

        if ($this->application->hr_comment) {
            $mail->line("HR Remarks / Checklist Notes: {$this->application->hr_comment}");
        }

        return $mail->action('Open Intern Dashboard', route('dashboard.intern'))
            ->line('Thank you for your interest in the Judiciary of Kenya Internship Programme.');
    }
}
