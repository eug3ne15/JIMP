<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $supervisorId = auth()->id();

        // Query all applications where internship belongs to this supervisor
        $applicationsQuery = Application::query()
            ->with(['user', 'course.department', 'internship.course', 'internship.supervisor'])
            ->where(function ($query) use ($supervisorId) {
                $query->whereHas('internship', fn ($q) => $q->where('supervisor_id', $supervisorId))
                      ->orWhereIn('internship_id', function ($sub) use ($supervisorId) {
                          $sub->select('id')->from('internships')->where('supervisor_id', $supervisorId);
                      });
            });

        $applications = (clone $applicationsQuery)->latest()->paginate(15);

        // Compute statistics for the supervisor dashboard
        $allAssigned = (clone $applicationsQuery)->get();
        $totalAssigned = $allAssigned->count();
        $reportsSubmitted = $allAssigned->whereNotNull('supervisor_evaluation_path')->count();
        $reportsPending = $totalAssigned - $reportsSubmitted;
        $activePlacements = $allAssigned->where('status', 'shortlisted')->count();
        $supervisedInternshipsCount = Internship::where('supervisor_id', $supervisorId)->count();

        $activeTab = $request->query('tab') === 'interns' ? 'interns-section' : 'overview-section';

        return view('dashboards.supervisor', compact(
            'applications',
            'totalAssigned',
            'reportsSubmitted',
            'reportsPending',
            'activePlacements',
            'supervisedInternshipsCount',
            'activeTab'
        ));
    }
}
