<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvaluationController extends Controller
{
    private function authorizeSupervisorApplication(Request $request, Application $application): Application
    {
        $supervisorId = $request->user()->id;

        $authorized = Application::query()
            ->whereKey($application->id)
            ->where(function ($query) use ($supervisorId) {
                $query->whereHas('internship', fn ($q) => $q->where('supervisor_id', $supervisorId))
                      ->orWhereIn('internship_id', function ($sub) use ($supervisorId) {
                          $sub->select('id')->from('internships')->where('supervisor_id', $supervisorId);
                      });
            })
            ->first();

        if (! $authorized) {
            abort(403, 'Unauthorized. This intern is not assigned to your supervision.');
        }

        return $authorized;
    }

    public function store(Request $request, Application $application): RedirectResponse
    {
        $application = $this->authorizeSupervisorApplication($request, $application);

        $validated = $request->validate([
            'evaluation' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
        ]);

        $previousPath = $application->supervisor_evaluation_path;
        $previousRecommendationPath = $application->recommendation_letter_path;
        $application->supervisor_evaluation_path = $validated['evaluation']->store('supervisor-evaluations', 'local');
        $application->recommendation_letter_path = null;
        $application->save();

        if ($previousPath) {
            Storage::disk('local')->delete($previousPath);
        }

        if ($previousRecommendationPath) {
            Storage::disk('local')->delete($previousRecommendationPath);
        }

        return redirect()->route('supervisor.dashboard', ['tab' => 'interns'])
            ->with('status', 'Evaluation report submitted successfully.');
    }

    public function downloadDocument(Request $request, Application $application, string $type): StreamedResponse
    {
        $application = $this->authorizeSupervisorApplication($request, $application);

        $path = match ($type) {
            'school_recommendation' => $application->school_recommendation_path,
            'insurance' => $application->insurance_path,
            'cv' => $application->cv_path,
            default => $application->school_recommendation_path ?? $application->cv_path ?? $application->insurance_path ?? $application->document_path,
        };

        if (! $path || ! Storage::disk('local')->exists($path)) {
            abort(404, 'Requested document not found on server.');
        }

        return Storage::disk('local')->download($path);
    }

    public function downloadEvaluation(Request $request, Application $application): StreamedResponse
    {
        $application = $this->authorizeSupervisorApplication($request, $application);

        if (! $application->supervisor_evaluation_path || ! Storage::disk('local')->exists($application->supervisor_evaluation_path)) {
            abort(404, 'Evaluation report not found.');
        }

        return Storage::disk('local')->download($application->supervisor_evaluation_path);
    }
}
