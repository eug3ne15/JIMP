<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Course;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\ApplicationSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApplicationEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_receives_confirmation_email_upon_submitting_application(): void
    {
        Notification::fake();
        Storage::fake('local');

        $intern = User::factory()->create([
            'role' => 'intern',
            'name' => 'Alice Intern',
            'email' => 'alice@example.com',
            'school' => 'Kenyatta University',
        ]);

        $course = Course::create([
            'name' => 'Judicial Records & Archives Management',
        ]);

        $schoolRec = UploadedFile::fake()->create('rec.pdf', 200, 'application/pdf');
        $insurance = UploadedFile::fake()->create('insurance.pdf', 200, 'application/pdf');
        $cv = UploadedFile::fake()->create('cv.pdf', 200, 'application/pdf');

        $response = $this->actingAs($intern)->post(route('intern.courses.apply.store', $course), [
            'applicant_name' => 'Alice Intern',
            'institution_select' => 'Kenyatta University',
            'school_recommendation' => $schoolRec,
            'insurance' => $insurance,
            'cv' => $cv,
        ]);

        $response->assertRedirect(route('dashboard.intern'));

        // Assert ApplicationSubmitted notification was sent to intern
        Notification::assertSentTo($intern, ApplicationSubmitted::class, function ($notification, $channels) use ($course, $intern) {
            $this->assertContains('mail', $channels);

            $mail = $notification->toMail($intern);
            $this->assertStringContainsString('Application Received', $mail->subject);
            $this->assertStringContainsString('Your application is sent, kindly wait for approval', implode(' ', $mail->introLines));
            $this->assertStringContainsString($course->name, implode(' ', $mail->introLines));

            return true;
        });
    }

    public function test_intern_receives_approval_email_when_hr_shortlists_application(): void
    {
        Notification::fake();

        $intern = User::factory()->create([
            'role' => 'intern',
            'name' => 'Bob Intern',
            'email' => 'bob@example.com',
        ]);

        $hr = User::factory()->create([
            'role' => 'hr',
            'name' => 'HR Officer',
        ]);

        $course = Course::create(['name' => 'Legal Research Internship']);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $course->id,
            'applicant_name' => 'Bob Intern',
            'institution' => 'Strathmore University',
            'status' => 'pending',
        ]);

        // HR updates status to shortlisted (approved)
        $response = $this->actingAs($hr)->put(route('hr.intern.update', $application), [
            'status' => 'shortlisted',
            'hr_comment' => 'Meets all statutory requirements.',
            'is_qualified' => 1,
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($intern, ApplicationStatusUpdated::class, function ($notification) use ($intern) {
            $mail = $notification->toMail($intern);
            $this->assertStringContainsString('Approved', $mail->subject);
            $this->assertStringContainsString('approved and shortlisted', implode(' ', $mail->introLines));
            $this->assertStringContainsString('Meets all statutory requirements.', implode(' ', $mail->introLines));

            return true;
        });
    }

    public function test_intern_receives_rejection_email_when_hr_rejects_application(): void
    {
        Notification::fake();

        $intern = User::factory()->create([
            'role' => 'intern',
            'name' => 'Charlie Intern',
            'email' => 'charlie@example.com',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);
        $course = Course::create(['name' => 'ICT Support Internship']);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $course->id,
            'applicant_name' => 'Charlie Intern',
            'institution' => 'JKUAT',
            'status' => 'pending',
        ]);

        // HR rejects application
        $response = $this->actingAs($hr)->put(route('hr.intern.update', $application), [
            'status' => 'rejected',
            'rejection_reason' => 'Academic credentials do not match course criteria.',
            'is_qualified' => 0,
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($intern, ApplicationStatusUpdated::class, function ($notification) use ($intern) {
            $mail = $notification->toMail($intern);
            $this->assertStringContainsString('Update on your Judiciary Internship Application', $mail->subject);
            $this->assertStringContainsString('not successful', implode(' ', $mail->introLines));
            $this->assertStringContainsString('Academic credentials do not match course criteria.', implode(' ', $mail->introLines));

            return true;
        });
    }

    public function test_intern_receives_info_requested_email_when_hr_requests_details(): void
    {
        Notification::fake();

        $intern = User::factory()->create([
            'role' => 'intern',
            'name' => 'David Intern',
            'email' => 'david@example.com',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);
        $course = Course::create(['name' => 'Accounts Internship']);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $course->id,
            'applicant_name' => 'David Intern',
            'institution' => 'Moi University',
            'status' => 'pending',
        ]);

        // HR requests more details
        $response = $this->actingAs($hr)->put(route('hr.intern.update', $application), [
            'status' => 'info_requested',
            'hr_comment' => 'Please provide an updated student insurance policy covering this academic term.',
        ]);

        $response->assertRedirect();

        Notification::assertSentTo($intern, ApplicationStatusUpdated::class, function ($notification) use ($intern) {
            $mail = $notification->toMail($intern);
            $this->assertStringContainsString('Additional Details Needed', $mail->subject);
            $this->assertStringContainsString('requires you to provide additional information', implode(' ', $mail->introLines));
            $this->assertStringContainsString('Please provide an updated student insurance policy', implode(' ', $mail->introLines));

            return true;
        });
    }
}
