<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Department;
use App\Models\University;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InternSearchableUniversitySelectTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_dashboard_renders_searchable_university_select_tool(): void
    {
        $uni1 = University::create([
            'name' => 'University of Nairobi (UoN)',
            'location' => 'Nairobi, Kenya',
            'status' => 'registered',
        ]);

        $uni2 = University::create([
            'name' => 'Strathmore University',
            'location' => 'Madaraka, Nairobi',
            'status' => 'registered',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => 'Strathmore University',
        ]);

        $response = $this->actingAs($intern)->get(route('dashboard.intern'));

        $response->assertStatus(200);
        $response->assertSee('dash_institution_select');
        $response->assertSee('searchable-university-select');
        $response->assertSee('data-searchable-university="true"', false);
        $response->assertSee('University of Nairobi (UoN)');
        $response->assertSee('Strathmore University');
        $response->assertSee('data-location="Madaraka, Nairobi"', false);
        $response->assertSee('data-is-other="true"', false);
    }

    public function test_intern_application_form_renders_searchable_university_select_tool(): void
    {
        $dept = Department::create([
            'name' => 'Directorate of ICT',
            'code' => 'DICT',
        ]);

        $role = Course::create([
            'name' => 'Software Developer Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Computer Science',
            'status' => 'open',
        ]);

        $uni = University::create([
            'name' => 'Kenyatta University (KU)',
            'location' => 'Nairobi, Kenya',
            'status' => 'registered',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => null,
        ]);

        $response = $this->actingAs($intern)->get(route('intern.courses.apply', $role));

        $response->assertStatus(200);
        $response->assertSee('institution_select');
        $response->assertSee('searchable-university-select');
        $response->assertSee('data-searchable-university="true"', false);
        $response->assertSee('Kenyatta University (KU)');
        $response->assertSee('data-location="Nairobi, Kenya"', false);
        $response->assertSee('data-is-other="true"', false);
    }

    public function test_intern_can_update_university_selection_via_dashboard(): void
    {
        $uni = University::create([
            'name' => 'Moi University',
            'location' => 'Eldoret, Kenya',
            'status' => 'registered',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => null,
        ]);

        $response = $this->actingAs($intern)->post(route('intern.institution.update'), [
            'institution_select' => 'Moi University',
        ]);

        $response->assertRedirect(route('dashboard.intern', ['tab' => 'institution']));
        $this->assertEquals('Moi University', $intern->fresh()->school);
    }

    public function test_intern_can_request_unlisted_university_via_searchable_other_option(): void
    {
        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => null,
        ]);

        $response = $this->actingAs($intern)->post(route('intern.institution.update'), [
            'institution_select' => '__other__',
            'custom_institution_name' => 'Multimedia University of Kenya',
            'custom_institution_location' => 'Rongai, Nairobi',
        ]);

        $response->assertRedirect(route('dashboard.intern', ['tab' => 'institution']));
        $this->assertEquals('Multimedia University of Kenya', $intern->fresh()->school);

        $this->assertDatabaseHas('universities', [
            'name' => 'Multimedia University of Kenya',
            'location' => 'Rongai, Nairobi',
            'status' => 'pending',
            'requested_by' => $intern->id,
        ]);
    }

    public function test_intern_submits_course_application_with_selected_university(): void
    {
        Storage::fake('local');

        $dept = Department::create([
            'name' => 'Legal Services',
            'code' => 'LS',
        ]);

        $role = Course::create([
            'name' => 'Legal Assistant Intern',
            'department_id' => $dept->id,
            'field_of_study' => 'Law',
            'status' => 'open',
        ]);

        $uni = University::create([
            'name' => 'Egerton University',
            'location' => 'Njoro, Nakuru',
            'status' => 'registered',
        ]);

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => null,
        ]);

        $response = $this->actingAs($intern)->post(route('intern.courses.apply.store', $role), [
            'field_of_study' => 'Law',
            'institution_select' => 'Egerton University',
            'school_recommendation' => UploadedFile::fake()->create('recommendation.pdf', 500, 'application/pdf'),
        ]);

        $response->assertRedirect(route('dashboard.intern'));
        $this->assertDatabaseHas('applications', [
            'user_id' => $intern->id,
            'course_id' => $role->id,
            'institution' => 'Egerton University',
        ]);
    }
}
