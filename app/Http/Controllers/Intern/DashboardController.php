<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\Department;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = auth()->user();

        $applications = Application::query()
            ->with(['course.university', 'course.department', 'internship.course.university', 'internship.course.department'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $departments = Department::with(['roles' => fn ($q) => $q->open()->orderBy('name')])->orderBy('name')->get();
        $courses = Course::query()->with(['university', 'department'])->open()->orderBy('name')->get();
        $appliedCourseIds = $applications->pluck('course_id')->filter()->all();
        $fieldsOfStudy = Course::query()->open()->whereNotNull('field_of_study')->pluck('field_of_study')->unique()->values();

        // Retrieve registered accredited universities
        $registeredUniversities = University::registered()->orderBy('name')->get();

        // Retrieve university approval statuses for applied institutions
        $institutions = $applications->pluck('institution')->filter()->unique();
        if ($user->school) {
            $institutions->push($user->school);
        }

        $universityStatusMap = University::query()
            ->whereIn('name', $institutions->unique())
            ->pluck('status', 'name')
            ->all();

        $currentInstitution = $user->school;
        $currentInstitutionStatus = $currentInstitution ? ($universityStatusMap[$currentInstitution] ?? null) : null;

        $myRequestedUniversities = University::query()
            ->where('requested_by', $user->id)
            ->latest()
            ->get();

        return view('dashboards.intern', compact(
            'applications',
            'courses',
            'departments',
            'fieldsOfStudy',
            'appliedCourseIds',
            'registeredUniversities',
            'universityStatusMap',
            'currentInstitution',
            'currentInstitutionStatus',
            'myRequestedUniversities'
        ));
    }

    public function updateInstitution(Request $request): RedirectResponse
    {
        $request->validate([
            'institution_select' => ['required', 'string', 'max:255'],
            'custom_institution_name' => [
                'nullable',
                'required_if:institution_select,__other__',
                'string',
                'max:255',
            ],
            'custom_institution_location' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        if ($request->input('institution_select') === '__other__') {
            $customName = trim((string) $request->input('custom_institution_name'));
            $customLoc = $request->filled('custom_institution_location')
                ? trim((string) $request->input('custom_institution_location'))
                : null;

            $existing = University::query()
                ->whereRaw('LOWER(name) = ?', [strtolower($customName)])
                ->first();

            if ($existing) {
                $user->update(['school' => $existing->name]);
                $msg = $existing->isPending()
                    ? "Institution set to '{$existing->name}'. It is currently pending HR verification."
                    : "Institution successfully updated to '{$existing->name}'.";
            } else {
                $newUni = University::create([
                    'name' => $customName,
                    'location' => $customLoc,
                    'status' => 'pending',
                    'requested_by' => $user->id,
                ]);
                $user->update(['school' => $newUni->name]);
                $msg = "Institution '{$customName}' submitted to Judiciary HR for approval and updated on your profile.";
            }
        } else {
            $selectedName = trim((string) $request->input('institution_select'));
            $user->update(['school' => $selectedName]);
            $msg = "Institution updated successfully to '{$selectedName}'.";
        }

        return redirect()->route('dashboard.intern', ['tab' => 'institution'])->with('status', $msg);
    }

    public function downloadRecommendation(Application $application): StreamedResponse
    {
        abort_unless($application->user_id === auth()->id(), 404);
        abort_unless($application->recommendation_letter_path, 404);
        abort_unless(Storage::disk('local')->exists($application->recommendation_letter_path), 404);

        return Storage::disk('local')->download($application->recommendation_letter_path);
    }

    public function downloadDocument(Request $request, Application $application): StreamedResponse
    {
        abort_unless($application->user_id === auth()->id(), 403, 'Unauthorized access.');

        $type = $request->query('type');
        $action = $request->query('action', 'open');
        $path = match ($type) {
            'school_recommendation' => $application->school_recommendation_path,
            'insurance' => $application->insurance_path,
            'cv' => $application->cv_path,
            default => $application->school_recommendation_path ?? $application->cv_path ?? $application->insurance_path ?? $application->document_path,
        };

        abort_unless($path && Storage::disk('local')->exists($path), 404, 'Requested document not found.');

        if ($action === 'download') {
            return Storage::disk('local')->download($path);
        }

        return Storage::disk('local')->response($path);
    }
}
