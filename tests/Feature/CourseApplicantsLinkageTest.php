<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Course;
use App\Models\Internship;
use App\Models\University;
use App\Models\User;
use App\Notifications\InternAssignedToSupervisor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CourseApplicantsLinkageTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_courses_displays_accurate_applicants_count(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $uni = University::create([
            'name' => 'University of Nairobi',
            'status' => 'registered',
        ]);

        $courseA = Course::create([
            'name' => 'Software Development',
            'university_id' => $uni->id,
        ]);

        $courseB = Course::create([
            'name' => 'Legal Research',
            'university_id' => $uni->id,
        ]);

        $intern1 = User::factory()->create(['role' => 'intern']);
        $intern2 = User::factory()->create(['role' => 'intern']);

        // 2 applicants for Course A
        Application::create([
            'user_id' => $intern1->id,
            'course_id' => $courseA->id,
            'applicant_name' => 'Intern One',
            'institution' => 'University of Nairobi',
            'status' => 'pending',
        ]);

        Application::create([
            'user_id' => $intern2->id,
            'course_id' => $courseA->id,
            'applicant_name' => 'Intern Two',
            'institution' => 'University of Nairobi',
            'status' => 'pending',
        ]);

        // Course B has 0 applicants

        $response = $this->actingAs($hr)->get(route('hr.courses.index'));

        $response->assertOk();
        $response->assertSee('2 applicants');
        $response->assertSee('0 applicants');
        $response->assertSee(route('hr.intern.index', ['course_id' => $courseA->id]));
    }

    public function test_hr_interns_can_be_filtered_by_course(): void
    {
        $hr = User::factory()->create(['role' => 'hr']);

        $courseA = Course::create(['name' => 'Software Development']);
        $courseB = Course::create(['name' => 'Legal Studies']);

        $intern1 = User::factory()->create(['role' => 'intern', 'name' => 'Alice Dev']);
        $intern2 = User::factory()->create(['role' => 'intern', 'name' => 'Bob Law']);

        Application::create([
            'user_id' => $intern1->id,
            'course_id' => $courseA->id,
            'applicant_name' => 'Alice Dev',
            'institution' => 'Strathmore University',
            'status' => 'pending',
        ]);

        Application::create([
            'user_id' => $intern2->id,
            'course_id' => $courseB->id,
            'applicant_name' => 'Bob Law',
            'institution' => 'Kenyatta University',
            'status' => 'pending',
        ]);

        // Filter by Course A
        $response = $this->actingAs($hr)->get(route('hr.intern.index', ['course_id' => $courseA->id]));

        $response->assertOk();
        $response->assertSee('Alice Dev');
        $response->assertDontSee('Bob Law');
        $response->assertSee('Showing applicants for course:');
    }

    public function test_hr_can_assign_supervisor_creating_placement(): void
    {
        Notification::fake();

        $hr = User::factory()->create(['role' => 'hr']);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'name' => 'Senior Judge Smith', 'email' => 'judge.smith@judiciary.go.ke']);
        $intern = User::factory()->create(['role' => 'intern', 'name' => 'Charlie Intern', 'email' => 'charlie@example.com']);
        $course = Course::create(['name' => 'Judicial Clerkship']);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $course->id,
            'applicant_name' => 'Charlie Intern',
            'institution' => 'UoN',
            'status' => 'pending',
        ]);

        // HR updates application and assigns supervisor
        $response = $this->actingAs($hr)->put(route('hr.intern.update', $application), [
            'status' => 'shortlisted',
            'applicant_name' => 'Charlie Intern',
            'institution' => 'UoN',
            'supervisor_id' => $supervisor->id,
            'is_qualified' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'status' => 'shortlisted',
        ]);

        $this->assertDatabaseHas('internships', [
            'supervisor_id' => $supervisor->id,
            'course_id' => $course->id,
            'status' => 'active',
        ]);

        $application->refresh();
        $this->assertNotNull($application->internship_id);

        // Assert notification was sent to supervisor
        Notification::assertSentTo($supervisor, InternAssignedToSupervisor::class, function ($notification) use ($supervisor) {
            $mail = $notification->toMail($supervisor);
            $this->assertStringContainsString('New Intern Assigned: Charlie Intern', $mail->subject);
            $this->assertStringContainsString('Senior Judge Smith', $mail->greeting);
            $this->assertStringContainsString('Charlie Intern', implode(' ', $mail->introLines));
            $this->assertStringContainsString('Judicial Clerkship', implode(' ', $mail->introLines));
            $this->assertEquals(route('supervisor.dashboard'), $mail->actionUrl);
            return true;
        });

        // Supervisor now sees the assigned intern in supervisor dashboard
        $supervisorResponse = $this->actingAs($supervisor)->get(route('supervisor.dashboard'));
        $supervisorResponse->assertOk();
        $supervisorResponse->assertSee('Charlie Intern');
    }
}
