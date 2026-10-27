<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\Department;
use App\Models\University;
use App\Notifications\ApplicationSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CourseApplicationController extends Controller
{
    public function create(Course $course): View|RedirectResponse
    {
        if (Application::query()->where('user_id', auth()->id())->where('course_id', $course->id)->exists()) {
            return redirect()->route('dashboard.intern')->with('status', 'You have already applied for this role.');
        }

        $course->load(['university', 'department']);
        $registeredUniversities = University::registered()->orderBy('name')->get();

        // Extract Field of Study dropdown options added by HR for this role
        $fieldsOfStudy = collect($course->fields_of_study_list);

        return view('intern.applications.create', compact('course', 'registeredUniversities', 'fieldsOfStudy'));
    }

    public function applyDepartment(Department $department): RedirectResponse
    {
        $role = $department->roles()->open()->first();
        if (! $role) {
            return redirect()->route('dashboard.intern')->with('error', "No open roles have been added by HR for {$department->name} yet.");
        }

        return redirect()->route('intern.courses.apply', $role);
    }

    public function store(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'institution_select' => ['required', 'string', 'max:255'],
            'custom_institution_name' => [
                'nullable',
                'required_if:institution_select,__other__',
                'string',
                'max:255',
            ],
            'custom_institution_location' => ['nullable', 'string', 'max:255'],
            'school_recommendation' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'insurance' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240'],
        ], [
            'school_recommendation.mimes' => 'The school recommendation letter must be a PDF, DOC, DOCX, or image file.',
            'school_recommendation.max' => 'The school recommendation letter must not exceed 10MB.',
            'insurance.mimes' => 'The insurance cover document must be a PDF, DOC, DOCX, or image file.',
            'insurance.max' => 'The insurance cover document must not exceed 10MB.',
            'cv.mimes' => 'The application letter/CV must be a PDF, DOC, DOCX, or image file.',
            'cv.max' => 'The application letter/CV must not exceed 10MB.',
        ]);

        $selectedFieldOfStudy = !empty($validated['field_of_study'])
            ? $validated['field_of_study']
            : null;

        $statusNotice = 'Your application has been submitted to HR.';

        if ($request->input('institution_select') === '__other__') {
            $customName = trim((string) $request->input('custom_institution_name'));
            $customLocation = $request->filled('custom_institution_location')
                ? trim((string) $request->input('custom_institution_location'))
                : null;

            // Check if institution already exists in database (case-insensitive)
            $existing = University::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($customName)])
                ->first();

            if ($existing) {
                $finalInstitution = $existing->name;
                if ($existing->isPending()) {
                    $statusNotice = "Your application was submitted. '{$existing->name}' is currently pending HR approval.";
                }
            } else {
                $newUniversity = University::create([
                    'name' => $customName,
                    'location' => $customLocation,
                    'status' => 'pending',
                    'requested_by' => $request->user()->id,
                ]);
                $finalInstitution = $newUniversity->name;
                $statusNotice = "Application submitted! '{$customName}' was sent to HR for institutional approval.";
            }
        } else {
            $finalInstitution = trim((string) $request->input('institution_select'));
        }

        // Store supporting documents
        $schoolRecPath = $request->hasFile('school_recommendation')
            ? $request->file('school_recommendation')->store('intern_documents/recommendations', 'local')
            : null;

        $insurancePath = $request->hasFile('insurance')
            ? $request->file('insurance')->store('intern_documents/insurance', 'local')
            : null;

        $cvPath = $request->hasFile('cv')
            ? $request->file('cv')->store('intern_documents/cvs', 'local')
            : null;

        $application = Application::query()->firstOrCreate(
            ['user_id' => $request->user()->id, 'course_id' => $course->id],
            [
                'field_of_study' => $selectedFieldOfStudy,
                'institution' => $finalInstitution,
                'status' => 'pending',
                'school_recommendation_path' => $schoolRecPath,
                'insurance_path' => $insurancePath,
                'cv_path' => $cvPath,
                'document_path' => $schoolRecPath ?? $cvPath ?? $insurancePath,
            ],
        );

        if (! $application->wasRecentlyCreated) {
            return redirect()->route('dashboard.intern')->with('status', 'You have already applied for this role.');
        }

        // Keep user's profile school and course/field in sync
        $request->user()->update([
            'school' => $finalInstitution,
            'course' => $selectedFieldOfStudy,
        ]);

        // Send submission confirmation email to intern
        try {
            $request->user()->notify(new ApplicationSubmitted($application));
        } catch (\Throwable $e) {
            Log::warning("Could not send application submitted email: " . $e->getMessage());
        }

        return redirect()->route('dashboard.intern')->with('status', $statusNotice);
    }
}
