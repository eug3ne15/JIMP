<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        return view('hr.dashboard', [
            'applicationCount' => Application::query()->count(),
            'supervisorCount' => User::query()->where('role', 'supervisor')->count(),
            'courseCount' => Course::query()->count(),
        ]);
    }
}
