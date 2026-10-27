<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class InternDocumentsUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_intern_can_apply_with_documents(): void
    {
        Storage::fake('local');

        $intern = User::factory()->create([
            'role' => 'intern',
            'school' => 'University of Nairobi',
        ]);

        $course = Course::create([
            'name' => 'Legal Research & Judicial Reporting',
        ]);

        $schoolRec = UploadedFile::fake()->create('school_rec.pdf', 500, 'application/pdf');
        $insurance = UploadedFile::fake()->create('insurance_cover.pdf', 300, 'application/pdf');
        $cv = UploadedFile::fake()->create('curriculum_vitae.pdf', 400, 'application/pdf');

        $response = $this->actingAs($intern)->post(route('intern.courses.apply.store', $course), [
            'applicant_name' => 'Jane Intern Doe',
            'institution_select' => 'University of Nairobi',
            'school_recommendation' => $schoolRec,
            'insurance' => $insurance,
            'cv' => $cv,
        ]);

        $response->assertRedirect(route('dashboard.intern'));

        $application = Application::where('user_id', $intern->id)->where('course_id', $course->id)->first();
        $this->assertNotNull($application);
        $this->assertNotNull($application->school_recommendation_path);
        $this->assertNotNull($application->insurance_path);
        $this->assertNotNull($application->cv_path);

        Storage::disk('local')->assertExists($application->school_recommendation_path);
        Storage::disk('local')->assertExists($application->insurance_path);
        Storage::disk('local')->assertExists($application->cv_path);

        // Test intern can open (inline) and download their own documents
        $openRec = $this->actingAs($intern)->get(route('intern.applications.document', [
            'application' => $application,
            'type' => 'school_recommendation',
            'action' => 'open',
        ]));
        $openRec->assertOk();
        $this->assertStringContainsString('inline', (string) $openRec->headers->get('content-disposition'));

        $downloadInsurance = $this->actingAs($intern)->get(route('intern.applications.document', [
            'application' => $application,
            'type' => 'insurance',
            'action' => 'download',
        ]));
        $downloadInsurance->assertOk();
        $this->assertStringContainsString('attachment', (string) $downloadInsurance->headers->get('content-disposition'));

        $downloadCv = $this->actingAs($intern)->get(route('intern.applications.document', [
            'application' => $application,
            'type' => 'cv',
        ]));
        $downloadCv->assertOk();

        // Test HR can see documents on index and open them
        $hr = User::factory()->create(['role' => 'hr']);

        $hrIndex = $this->actingAs($hr)->get(route('hr.intern.index'));
        $hrIndex->assertOk();
        $hrIndex->assertSee('School Rec');
        $hrIndex->assertSee('Insurance');
        $hrIndex->assertSee('CV / Letter');

        // Test HR can open (inline preview)
        $hrOpenRec = $this->actingAs($hr)->get(route('hr.intern.download', [
            'intern' => $application,
            'type' => 'school_recommendation',
            'action' => 'open',
        ]));
        $hrOpenRec->assertOk();
        $this->assertStringContainsString('inline', (string) $hrOpenRec->headers->get('content-disposition'));

        // Test HR can download (attachment)
        $hrDownloadInsurance = $this->actingAs($hr)->get(route('hr.intern.download', [
            'intern' => $application,
            'type' => 'insurance',
            'action' => 'download',
        ]));
        $hrDownloadInsurance->assertOk();
        $this->assertStringContainsString('attachment', (string) $hrDownloadInsurance->headers->get('content-disposition'));

        // Test HR show page renders Open and Download buttons
        $hrShow = $this->actingAs($hr)->get(route('hr.intern.show', $application));
        $hrShow->assertOk();
        $hrShow->assertSee('Submitted Supporting Documents');
        $hrShow->assertSee('Open');
        $hrShow->assertSee('Download');
    }
}
