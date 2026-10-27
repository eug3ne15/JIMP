<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\Department;
use App\Models\University;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::query()
            ->with(['university', 'department'])
            ->withCount(['applications', 'internships']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('field_of_study', 'like', "%{$search}%");
            });
        }

        $courses = $query->orderBy('name')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();
        $universities = University::registered()->orderBy('name')->get();
        $totalApplications = Application::count();

        return view('hr.courses.index', compact('courses', 'departments', 'universities', 'totalApplications'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $universities = University::registered()->orderBy('name')->get();

        return view('hr.courses.create', compact('departments', 'universities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name'),
            ],
            'department_id' => ['nullable', 'exists:departments,id'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,closed'],
            'university_id' => ['nullable', 'exists:universities,id'],
        ]);

        $validated['status'] = $validated['status'] ?? 'open';

        Course::create($validated);

        return redirect()->route('hr.courses.index')->with('status', 'Open Role created successfully.');
    }

    public function edit(Course $course): View
    {
        $course->load(['university', 'department']);
        $departments = Department::orderBy('name')->get();
        $universities = University::registered()->orderBy('name')->get();

        return view('hr.courses.edit', compact('course', 'departments', 'universities'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses', 'name')->ignore($course->id),
            ],
            'department_id' => ['nullable', 'exists:departments,id'],
            'field_of_study' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requirements' => ['nullable', 'string'],
            'status' => ['nullable', 'in:open,closed'],
            'university_id' => ['nullable', 'exists:universities,id'],
        ]);

        $validated['status'] = $validated['status'] ?? 'open';

        $course->update($validated);

        return redirect()->route('hr.courses.index')->with('status', 'Open Role updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->internships()->exists()) {
            return back()->withErrors(['course' => 'Remove this role’s active internships before deleting it.']);
        }

        if ($course->applications()->exists()) {
            return back()->withErrors(['course' => 'This role has candidate applications and cannot be deleted.']);
        }

        $course->delete();

        return redirect()->route('hr.courses.index')->with('status', 'Open Role deleted successfully.');
    }

    public function show(Course $course): View
    {
        $course->load([
            'department',
            'university',
            'applications.user',
            'internships.supervisor',
            'internships.application.user',
        ]);

        $course->loadCount(['applications', 'internships']);

        $pendingApplicantsCount = $course->applications()->where('status', 'pending')->count();
        $shortlistedCount = $course->applications()->where('status', 'shortlisted')->count();
        $fieldsOfStudy = $course->fields_of_study_list;
        $fieldsBreakdown = $course->fields_of_study_breakdown;
        $fieldsCountMap = $fieldsBreakdown->keyBy(fn ($item) => strtolower(trim($item['name'])));
        $departments = Department::orderBy('name')->get();
        $universities = University::registered()->orderBy('name')->get();

        return view('hr.courses.show', compact(
            'course',
            'fieldsOfStudy',
            'fieldsBreakdown',
            'fieldsCountMap',
            'pendingApplicantsCount',
            'shortlistedCount',
            'departments',
            'universities'
        ));
    }

    public function addFieldOfStudy(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'field_of_study' => ['required', 'string', 'max:255'],
        ], [
            'field_of_study.required' => 'Please provide a field of study name to add.',
        ]);

        $course->addFieldOfStudy($validated['field_of_study']);

        return redirect()->route('hr.courses.show', $course)->with('status', 'Field of study added successfully. It is now available in the intern application dropdown.');
    }

    public function removeFieldOfStudy(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['required', 'string', 'max:255'],
        ]);

        $course->removeFieldOfStudy($validated['field']);

        return redirect()->route('hr.courses.show', $course)->with('status', "Removed '{$validated['field']}' from eligible fields of study.");
    }
}
