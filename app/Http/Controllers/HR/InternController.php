<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\Internship;
use App\Models\University;
use App\Models\User;
use App\Notifications\ApplicationStatusUpdated;
use App\Notifications\InternAssignedToSupervisor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InternController extends Controller
{
    public function index(Request $request): View
    {
        $query = Application::query()->with(['user', 'course', 'internship.course', 'internship.supervisor']);

        if ($request->filled('course_id')) {
            $courseId = $request->integer('course_id');
            $query->where(function ($q) use ($courseId) {
                $q->where('course_id', $courseId)
                  ->orWhereHas('internship', fn ($iq) => $iq->where('course_id', $courseId));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('institution')) {
            $institution = $request->string('institution')->toString();
            $query->where(function ($query) use ($institution): void {
                $query->where('institution', 'like', '%'.$institution.'%')
                    ->orWhereHas('user', function ($userQuery) use ($institution): void {
                        $userQuery->where('school', 'like', '%'.$institution.'%');
                    });
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search): void {
                $query->where('applicant_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();
        $universities = University::registered()->orderBy('name')->get();
        $courses = Course::orderBy('name')->get();
        $selectedCourse = $request->filled('course_id') ? Course::find($request->course_id) : null;

        return view('hr.interns.index', compact('applications', 'universities', 'courses', 'selectedCourse'));
    }

    public function show(Application $intern): View
    {
        $intern->load(['user', 'course', 'internship.course', 'internship.supervisor']);
        $universities = University::registered()->orderBy('name')->get();
        $supervisors = User::query()->where('role', 'supervisor')->orderBy('name')->get();

        return view('hr.interns.show', compact('intern', 'universities', 'supervisors'));
    }

    public function update(Request $request, Application $intern)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,info_requested,rejected,shortlisted, accepted',
            'applicant_name' => 'nullable|string|max:50',
            'institution' => 'nullable|string|max:255',
            'supervisor_id' => 'nullable|exists:users,id',
            'hr_comment' => 'nullable|string|max:1000',
            'rejection_reason' => 'nullable|string|max:1000',
            'is_qualified' => 'nullable|boolean',
        ]);

        $updateData = [
            'status' => $validated['status'],
            'hr_comment' => $validated['hr_comment'] ?? null,
            'rejection_reason' => $validated['rejection_reason'] ?? null,
            'is_qualified' => $validated['is_qualified'] ?? null,
        ];

        if (isset($validated['applicant_name']) && filled($validated['applicant_name'])) {
            $updateData['applicant_name'] = trim($validated['applicant_name']);
            if ($intern->user) {
                $intern->user->update(['name' => trim($validated['applicant_name'])]);
            }
        }

        if (isset($validated['institution']) && filled($validated['institution'])) {
            $updateData['institution'] = trim($validated['institution']);
            if ($intern->user) {
                $intern->user->update(['school' => trim($validated['institution'])]);
            }
        }

        // Handle Supervisor Assignment
        $assignedSupervisor = null;
        $internship = null;

        if ($request->filled('supervisor_id')) {
            $supervisorId = $request->integer('supervisor_id');
            $previousSupervisorId = $intern->internship?->supervisor_id;

            // Ensure course_id is always present for the internship foreign key
            $courseId = $intern->course_id ?: Course::first()?->id;
            if (! $intern->course_id && $courseId) {
                $updateData['course_id'] = $courseId;
            }

            $internName = $intern->applicant_name ?? $intern->user?->name ?? 'Intern';
            $roleTitle = $intern->course?->name ?? 'Judiciary Placement';

            if ($intern->internship) {
                $intern->internship->update([
                    'supervisor_id' => $supervisorId,
                    'course_id' => $courseId,
                    'status' => 'active',
                ]);
                $internship = $intern->internship;
                $updateData['internship_id'] = $internship->id;
            } else {
                $internship = Internship::create([
                    'title' => "{$roleTitle} - {$internName}",
                    'course_id' => $courseId,
                    'supervisor_id' => $supervisorId,
                    'status' => 'active',
                    'start_date' => now()->toDateString(),
                    'end_date' => now()->addMonths(3)->toDateString(),
                ]);
                $updateData['internship_id'] = $internship->id;
            }

            // If application is still pending, automatically approve/shortlist upon supervisor assignment
            if ($intern->status === 'pending' && (!isset($validated['status']) || $validated['status'] === 'pending')) {
                $updateData['status'] = 'shortlisted' || 'accepted';
            }

            if ($previousSupervisorId !== $supervisorId) {
                $assignedSupervisor = User::find($supervisorId);
            }
        } elseif ($request->has('supervisor_id') && empty($request->input('supervisor_id'))) {
            if ($intern->internship_id) {
                $oldInternship = $intern->internship;
                $updateData['internship_id'] = null;
                if ($oldInternship && ! Application::where('internship_id', $oldInternship->id)->where('id', '!=', $intern->id)->exists()) {
                    $oldInternship->delete();
                }
            }
        }

        $intern->update($updateData);

        // Notify supervisor if newly assigned or reassigned
        if ($assignedSupervisor && $assignedSupervisor->role === 'supervisor') {
            try {
                $intern->load(['user', 'course']);
                $internship = $intern->internship ?? $internship ?? null;
                $assignedSupervisor->notify(new InternAssignedToSupervisor($intern, $internship));
            } catch (\Throwable $e) {
                Log::warning("Could not send supervisor assignment email: " . $e->getMessage());
            }
        }

        // Notify intern
        if (isset($validated['status']) && $intern->user) {
            try {
                $intern->user->notify(new ApplicationStatusUpdated($intern));
            } catch (\Throwable $e) {
                Log::warning("Could not send application status email: " . $e->getMessage());
            }
        }

        Log::info("Application {$intern->id} status updated to {$validated['status']}");

        return redirect()->route('hr.intern.show', $intern)->with('status', 'Application updated successfully.');
    }

    public function downloadDocument(Request $request, Application $intern)
    {
        $type = $request->query('type');
        $action = $request->query('action', 'open');
        $path = match ($type) {
            'school_recommendation' => $intern->school_recommendation_path,
            'insurance' => $intern->insurance_path,
            'cv' => $intern->cv_path,
            default => $intern->school_recommendation_path ?? $intern->cv_path ?? $intern->insurance_path ?? $intern->document_path,
        };

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Requested document not found on server.');
        }

        // Mark document access audit
        Log::info('Document accessed by HR', [
            'application_id' => $intern->id,
            'hr_id' => auth()->id(),
            'document_type' => $type ?? 'default',
            'action' => $action,
        ]);

        if ($action === 'download') {
            return Storage::disk('local')->download($path);
        }

        return Storage::disk('local')->response($path);
    }

    public function downloadEvaluation(Application $intern): StreamedResponse
    {
        abort_unless($intern->supervisor_evaluation_path, 404);
        abort_unless(Storage::disk('local')->exists($intern->supervisor_evaluation_path), 404);

        return Storage::disk('local')->download($intern->supervisor_evaluation_path);
    }

    public function issueRecommendation(Request $request, Application $intern): RedirectResponse
    {
        abort_unless($intern->supervisor_evaluation_path, 404);

        $validated = $request->validate([
            'recommendation_letter' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $previousPath = $intern->recommendation_letter_path;
        $intern->recommendation_letter_path = $validated['recommendation_letter']->store('recommendation-letters', 'local');
        $intern->save();

        if ($previousPath) {
            Storage::disk('local')->delete($previousPath);
        }

        return back()->with('status', 'Recommendation letter issued to the intern.');
    }
}
