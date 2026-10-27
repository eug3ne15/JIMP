<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Course;
use App\Models\Department;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JudiciaryDepartmentsAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_page_displays_open_roles_and_departments(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of ICT',
            'code' => 'DICT',
            'description' => 'Court automation and infrastructure',
        ]);

        $role = Course::create([
            'name' => 'Software Development & Systems Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Computer Science, Software Engineering',
            'description' => 'Assist in development of case management systems',
            'requirements' => 'Enrolled in Computer Science degree',
            'status' => 'open',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Directorate of ICT');
        $response->assertSee('Software Development & Systems Intern');
        $response->assertSee('Computer Science, Software Engineering');
        $response->assertSee('roleFlashcardModal');
    }

    public function test_intern_dashboard_displays_open_roles_and_filters_without_university_directory(): void
    {
        $dept = Department::create([
            'name' => 'Kenya Judiciary Academy',
            'code' => 'KJA',
        ]);

        $role = Course::create([
            'name' => 'Legal Research Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Law (LL.B)',
            'status' => 'open',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => 'University of Nairobi',
        ]);

        $response = $this->actingAs($intern)->get(route('dashboard.intern'));

        $response->assertStatus(200);
        $response->assertSee('Open Roles');
        $response->assertSee('Kenya Judiciary Academy');
        $response->assertSee('Legal Research Intern');
        $response->assertSee('Law (LL.B)');
        // Ensure the old accredited universities directory block is removed
        $response->assertDontSee('Accredited Universities Directory (');
    }

    public function test_intern_application_form_displays_role_and_department(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of Human Resource',
            'code' => 'DHRM',
        ]);

        $role = Course::create([
            'name' => 'HR Management Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Human Resource Management',
            'status' => 'open',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
        ]);

        $response = $this->actingAs($intern)->get(route('intern.courses.apply', $role));

        $response->assertStatus(200);
        $response->assertSee('HR Management Intern');
        $response->assertSee('Directorate of Human Resource');
        $response->assertSee('Human Resource Management');
    }

    public function test_hr_can_create_and_update_open_role_with_department_and_field_of_study(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of Finance & Accounting',
            'code' => 'DFA',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);

        $storeResponse = $this->actingAs($hr)->post(route('hr.courses.store'), [
            'name' => 'Financial Auditing Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Finance, Accounting, Commerce',
            'description' => 'Support financial compliance and audits',
            'requirements' => 'Enrolled in Bachelor of Commerce (Finance/Accounting)',
            'status' => 'open',
        ]);

        $storeResponse->assertRedirect(route('hr.courses.index'));
        $storeResponse->assertSessionHas('status', 'Open Role created successfully.');

        $role = Course::where('name', 'Financial Auditing Intern')->first();
        $this->assertNotNull($role);
        $this->assertEquals($dept->id, $role->department_id);
        $this->assertEquals('Finance, Accounting, Commerce', $role->field_of_study);

        // Update role
        $updateResponse = $this->actingAs($hr)->put(route('hr.courses.update', $role), [
            'name' => 'Senior Financial Auditing Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Accounting & CPA II',
            'status' => 'open',
        ]);

        $updateResponse->assertRedirect(route('hr.courses.index'));
        $updateResponse->assertSessionHas('status', 'Open Role updated successfully.');

        $this->assertDatabaseHas('courses', [
            'id' => $role->id,
            'name' => 'Senior Financial Auditing Intern',
            'field_of_study' => 'Accounting & CPA II',
        ]);
    }

    public function test_supervisor_dashboard_displays_assigned_interns_and_evaluation_form(): void
    {
        $supervisor = User::factory()->create(['role' => 'supervisor']);
        $intern = User::factory()->create(['role' => 'intern', 'school' => 'Strathmore University']);
        $course = Course::create(['name' => 'Registry Administration']);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $course->id,
            'applicant_name' => $intern->name,
            'status' => 'shortlisted',
        ]);

        $internship = Internship::create([
            'title' => 'Registry Administration - ' . $intern->name,
            'course_id' => $course->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonths(2),
        ]);
        $application->update(['internship_id' => $internship->id]);

        $response = $this->actingAs($supervisor)->get(route('supervisor.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Assigned interns');
        $response->assertSee($intern->name);
        $response->assertSee('Registry Administration');
        $response->assertSee('Submit report');
    }

    public function test_intern_can_apply_via_department_route_and_sees_admin_styled_form(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of ICT',
            'code' => 'DICT',
        ]);

        $role = Course::create([
            'name' => 'ICT Systems Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Computer Science, Software Engineering',
            'status' => 'open',
        ]);

        $intern = User::factory()->create(['role' => 'intern']);

        // Route to department apply
        $deptResponse = $this->actingAs($intern)->get(route('intern.departments.apply', $dept));
        $deptResponse->assertRedirect(route('intern.courses.apply', $role));

        // Follow redirect to application form
        $formResponse = $this->actingAs($intern)->get(route('intern.courses.apply', $role));
        $formResponse->assertStatus(200);
        $formResponse->assertSee('create-user-panel');
        $formResponse->assertSee('create-user-form');
        $formResponse->assertSee('Directorate of ICT');
        $formResponse->assertSee('ICT Systems Intern');
        $formResponse->assertSee('Computer Science');
        $formResponse->assertSee('Software Engineering');
        $formResponse->assertSee('selected_role');
        $formResponse->assertSee('field-of-study-div');
    }

    public function test_roles_are_seeded_without_field_of_study_and_hr_adds_them_for_intern_dropdown(): void
    {
        // Seed departments and open roles
        $this->seed(\Database\Seeders\DepartmentAndRoleSeeder::class);

        $this->assertEquals(9, Department::count());
        $this->assertEquals(9, Course::count());
        $this->assertEquals(0, Course::whereNotNull('field_of_study')->count());

        $hr = User::factory()->create(['role' => 'hr']);
        $intern = User::factory()->create(['role' => 'intern']);

        // HR views open roles directory and sees Add Field of Study button
        $hrIndex = $this->actingAs($hr)->get(route('hr.courses.index'));
        $hrIndex->assertStatus(200);
        $hrIndex->assertSee('+ Add Field of Study');

        // HR adds field of study to ICT Intern role
        $ictRole = Course::where('name', 'Information & Communication Technology (ICT) Intern')->first();
        $this->assertNotNull($ictRole);

        $this->actingAs($hr)->put(route('hr.courses.update', $ictRole), [
            'name' => $ictRole->name,
            'department_id' => $ictRole->department_id,
            'field_of_study' => 'Computer Science, Software Engineering, Information Technology',
            'status' => 'open',
        ]);

        $this->assertEquals('Computer Science, Software Engineering, Information Technology', $ictRole->fresh()->field_of_study);

        // Intern applies and sees the HR-added fields in the dropdown
        $applyResponse = $this->actingAs($intern)->get(route('intern.courses.apply', $ictRole));
        $applyResponse->assertStatus(200);
        $applyResponse->assertSee('Computer Science');
        $applyResponse->assertSee('Software Engineering');
        $applyResponse->assertSee('Information Technology');
    }

    public function test_hr_positions_card_redirects_to_position_info_with_statistics_and_hr_can_add_multiple_fields_of_study(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of ICT',
            'code' => 'DICT',
        ]);

        $role = Course::create([
            'name' => 'Systems & Network Engineering Intern',
            'department_id' => $dept->id,
            'field_of_study' => null,
            'description' => 'Support judiciary local area network and cloud systems',
            'status' => 'open',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);
        $intern = User::factory()->create(['role' => 'intern']);

        // 1. Check HR positions cards view
        $indexResponse = $this->actingAs($hr)->get(route('hr.courses.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('hr-roles-cards-grid');
        $indexResponse->assertSee('hr-role-card');
        $indexResponse->assertSee('Systems &amp; Network Engineering Intern', false);
        $indexResponse->assertSee(route('hr.courses.show', $role));
        $indexResponse->assertSee('0 Fields of Study');

        // Verify intern side does not display any dummy HR approval/specification options
        $internApplyBefore = $this->actingAs($intern)->get(route('intern.courses.apply', $role));
        $internApplyBefore->assertStatus(200);
        $internApplyBefore->assertDontSee('Awaiting HR specification');
        $internApplyBefore->assertDontSee('Awaiting HR assignment');
        $internApplyBefore->assertDontSee('HR approval');
        $internApplyBefore->assertDontSee('hr approval');

        // 2. Click through to position show/statistics page
        $showResponse = $this->actingAs($hr)->get(route('hr.courses.show', $role));
        $showResponse->assertStatus(200);
        $showResponse->assertSee('Back to Open Positions');
        $showResponse->assertSee('Edit Position Details');
        $showResponse->assertSee('Fields of Study');
        $showResponse->assertSee('Total Applicants');
        $showResponse->assertSee('Active Placements');
        $showResponse->assertSee('Systems &amp; Network Engineering Intern', false);
        $showResponse->assertSee('Directorate of ICT');
        $showResponse->assertSee('Eligible Fields of Study');
        $showResponse->assertSee('No fields of study have been added yet for this role');

        // 3. HR adds multiple fields of study at once (comma-separated batch)
        $addResponse = $this->actingAs($hr)->post(route('hr.courses.fields.add', $role), [
            'field_of_study' => 'Computer Science, Telecommunications Engineering, Cyber Security',
        ]);
        $addResponse->assertRedirect(route('hr.courses.show', $role));
        $addResponse->assertSessionHas('status', 'Field of study added successfully. It is now available in the intern application dropdown.');

        // Verify role has 3 fields of study
        $role->refresh();
        $this->assertEquals(3, $role->fields_of_study_count);
        $this->assertEquals(['Computer Science', 'Telecommunications Engineering', 'Cyber Security'], $role->fields_of_study_list);

        // 4. Verify statistics metrics updated on show page
        $updatedShow = $this->actingAs($hr)->get(route('hr.courses.show', $role));
        $updatedShow->assertStatus(200);
        $updatedShow->assertSee('Computer Science');
        $updatedShow->assertSee('Telecommunications Engineering');
        $updatedShow->assertSee('Cyber Security');

        // 5. HR can add an additional single field of study
        $this->actingAs($hr)->post(route('hr.courses.fields.add', $role), [
            'field_of_study' => 'Information Systems',
        ]);
        $role->refresh();
        $this->assertEquals(4, $role->fields_of_study_count);
        $this->assertContains('Information Systems', $role->fields_of_study_list);

        // 6. HR can remove a field of study
        $removeResponse = $this->actingAs($hr)->delete(route('hr.courses.fields.remove', $role), [
            'field' => 'Telecommunications Engineering',
        ]);
        $removeResponse->assertRedirect(route('hr.courses.show', $role));
        $role->refresh();
        $this->assertEquals(3, $role->fields_of_study_count);
        $this->assertNotContains('Telecommunications Engineering', $role->fields_of_study_list);

        // 7. Intern application dropdown reflects all current HR-added fields
        $internApply = $this->actingAs($intern)->get(route('intern.courses.apply', $role));
        $internApply->assertStatus(200);
        $internApply->assertSee('<option value="Computer Science"', false);
        $internApply->assertSee('<option value="Cyber Security"', false);
        $internApply->assertSee('<option value="Information Systems"', false);
        $internApply->assertDontSee('<option value="Telecommunications Engineering"', false);
    }

    public function test_position_details_displays_application_counts_breakdown_per_field_of_study(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of ICT',
            'code' => 'DICT',
        ]);

        $role = Course::create([
            'name' => 'Software & Systems Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Software Engineering, Cybersecurity, Data Science',
            'status' => 'open',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);
        $intern1 = User::factory()->create(['role' => 'intern', 'name' => 'Alice Dev']);
        $intern2 = User::factory()->create(['role' => 'intern', 'name' => 'Bob Coder']);
        $intern3 = User::factory()->create(['role' => 'intern', 'name' => 'Charlie Sec']);

        // Interns register for specific fields of study
        Application::create([
            'user_id' => $intern1->id,
            'course_id' => $role->id,
            'applicant_name' => 'Alice Dev',
            'field_of_study' => 'Software Engineering',
            'institution' => 'University of Nairobi',
            'status' => 'pending',
        ]);

        Application::create([
            'user_id' => $intern2->id,
            'course_id' => $role->id,
            'applicant_name' => 'Bob Coder',
            'field_of_study' => 'Software Engineering',
            'institution' => 'Strathmore University',
            'status' => 'shortlisted',
        ]);

        Application::create([
            'user_id' => $intern3->id,
            'course_id' => $role->id,
            'applicant_name' => 'Charlie Sec',
            'field_of_study' => 'Cybersecurity',
            'institution' => 'Kenyatta University',
            'status' => 'pending',
        ]);

        // HR visits positions index: sees the total applications and chip breakdown
        $indexResponse = $this->actingAs($hr)->get(route('hr.courses.index'));
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('3 applicants');
        $indexResponse->assertSee('Software Engineering');
        $indexResponse->assertSee('(2)');
        $indexResponse->assertSee('Cybersecurity');
        $indexResponse->assertSee('(1)');

        // HR clicks into position details page
        $showResponse = $this->actingAs($hr)->get(route('hr.courses.show', $role));
        $showResponse->assertStatus(200);

        // Position details displays total applications
        $showResponse->assertSee('3 Total Applications');
        $showResponse->assertSee('Applications Breakdown by Field of Study');

        // Field tags show exact counts
        $showResponse->assertSee('2 applicants');
        $showResponse->assertSee('1 applicant');
        $showResponse->assertSee('0 applicants');

        // Candidates table displays each candidate with their field of study
        $showResponse->assertSee('Alice Dev');
        $showResponse->assertSee('Bob Coder');
        $showResponse->assertSee('Charlie Sec');
        $showResponse->assertSee('University of Nairobi');
        $showResponse->assertSee('Strathmore University');
    }

    public function test_delegated_intern_is_visible_on_supervisor_dashboard_with_all_details_and_statistics(): void
    {
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'name' => 'Hon. Supervisor Kimani',
            'email' => 'kimani@judiciary.go.ke',
        ]);

        $hr = User::factory()->create(['role' => 'hr']);

        $dept = Department::create([
            'name' => 'Commercial & Tax Division',
            'code' => 'CTD',
        ]);

        $role = Course::create([
            'name' => 'Commercial Law Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Commercial Law, Tax Law',
            'status' => 'open',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'name' => 'Faith Wanjiku',
            'email' => 'faith@example.com',
            'school' => 'Catholic University of Eastern Africa',
        ]);

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $role->id,
            'applicant_name' => 'Faith Wanjiku',
            'field_of_study' => 'Commercial Law',
            'institution' => 'Catholic University of Eastern Africa',
            'status' => 'pending',
        ]);

        // 1. HR delegates the intern to the supervisor
        $assignResponse = $this->actingAs($hr)->put(route('hr.intern.update', $application), [
            'status' => 'shortlisted',
            'supervisor_id' => $supervisor->id,
        ]);
        $assignResponse->assertRedirect(route('hr.intern.show', $application));

        $application->refresh();
        $this->assertNotNull($application->internship_id);
        $this->assertEquals($supervisor->id, $application->internship->supervisor_id);

        // 2. HR interns index now displays the assigned supervisor
        $hrInternsIndex = $this->actingAs($hr)->get(route('hr.intern.index'));
        $hrInternsIndex->assertStatus(200);
        $hrInternsIndex->assertSee('Hon. Supervisor Kimani');

        // 3. Supervisor logs into their dashboard and sees the delegated intern and statistics
        $supervisorDashboard = $this->actingAs($supervisor)->get(route('supervisor.dashboard'));
        $supervisorDashboard->assertStatus(200);

        // Supervisor sees statistics
        $supervisorDashboard->assertSee('Assigned Interns');
        $supervisorDashboard->assertSee('1 intern assigned to you');
        $supervisorDashboard->assertSee('Reports Submitted');
        $supervisorDashboard->assertSee('Pending Reports');

        // Supervisor sees all intern details: Name, Email, Role, Department, Field of Study, Institution
        $supervisorDashboard->assertSee('Faith Wanjiku');
        $supervisorDashboard->assertSee('faith@example.com');
        $supervisorDashboard->assertSee('Commercial Law Intern');
        $supervisorDashboard->assertSee('Commercial &amp; Tax Division', false);
        $supervisorDashboard->assertSee('Commercial Law');
        $supervisorDashboard->assertSee('Catholic University of Eastern Africa');
        $supervisorDashboard->assertSee('Awaiting report');
        $supervisorDashboard->assertSee('Submit report');

        // 4. Verify Assigned Interns tab, View Details action, and details modal are present
        $supervisorDashboard->assertSee('data-tab-target="interns-section"', false);
        $supervisorDashboard->assertSee('View Details');
        $supervisorDashboard->assertSee('internDetailsModal-' . $application->id);
        $supervisorDashboard->assertSee('Curriculum Vitae / Application Letter');
        $supervisorDashboard->assertSee('University Recommendation Letter');
        $supervisorDashboard->assertSee('Medical / Insurance Cover');
        $supervisorDashboard->assertSee('Supervisor Evaluation & Performance Review', false);
    }

    public function test_supervisor_can_view_assigned_intern_details_and_download_documents(): void
    {
        Storage::fake('local');

        $dept = Department::create(['name' => 'Judicial Service Commission', 'code' => 'JSC']);
        $role = Course::create(['name' => 'Legal Research Intern', 'department_id' => $dept->id, 'field_of_study' => 'Law']);
        $supervisor = User::factory()->create(['role' => 'supervisor', 'name' => 'Hon. Supervisor Otieno']);
        $unauthorizedSupervisor = User::factory()->create(['role' => 'supervisor', 'name' => 'Hon. Other Supervisor']);
        $intern = User::factory()->create(['role' => 'intern', 'name' => 'Kevin Mwangi', 'email' => 'kevin@example.com']);

        $cvPath = 'applications/cvs/test_cv.pdf';
        $recPath = 'applications/recommendations/test_rec.pdf';
        $evalPath = 'supervisor-evaluations/test_eval.pdf';

        Storage::disk('local')->put($cvPath, 'dummy cv content');
        Storage::disk('local')->put($recPath, 'dummy rec content');
        Storage::disk('local')->put($evalPath, 'dummy eval content');

        $application = Application::create([
            'user_id' => $intern->id,
            'course_id' => $role->id,
            'applicant_name' => 'Kevin Mwangi',
            'field_of_study' => 'Law',
            'institution' => 'Kenyatta University',
            'status' => 'shortlisted',
            'cv_path' => $cvPath,
            'school_recommendation_path' => $recPath,
            'supervisor_evaluation_path' => $evalPath,
        ]);

        $internship = Internship::create([
            'title' => 'Legal Research - ' . $intern->name,
            'course_id' => $role->id,
            'supervisor_id' => $supervisor->id,
            'status' => 'active',
            'start_date' => now()->subWeeks(2),
            'end_date' => now()->addWeeks(10),
        ]);
        $application->update(['internship_id' => $internship->id]);

        // 1. Supervisor visits with tab=interns
        $dashResponse = $this->actingAs($supervisor)->get(route('supervisor.dashboard', ['tab' => 'interns']));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('Kevin Mwangi');
        $dashResponse->assertSee('kevin@example.com');
        $dashResponse->assertSee('Legal Research Intern');
        $dashResponse->assertSee('Kenyatta University');
        $dashResponse->assertSee('View Details');
        $dashResponse->assertSee(route('supervisor.applications.document', [$application, 'cv']));
        $dashResponse->assertSee(route('supervisor.applications.document', [$application, 'school_recommendation']));
        $dashResponse->assertSee(route('supervisor.applications.evaluation.download', $application));

        // 2. Supervisor can successfully download assigned intern CV
        $cvDownload = $this->actingAs($supervisor)->get(route('supervisor.applications.document', [$application, 'cv']));
        $cvDownload->assertStatus(200);

        // 3. Supervisor can download evaluation report
        $evalDownload = $this->actingAs($supervisor)->get(route('supervisor.applications.evaluation.download', $application));
        $evalDownload->assertStatus(200);

        // 4. Unauthorized supervisor cannot download intern's documents (403 Forbidden)
        $unauthDownload = $this->actingAs($unauthorizedSupervisor)->get(route('supervisor.applications.document', [$application, 'cv']));
        $unauthDownload->assertStatus(403);
    }
}
