<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'userCount' => User::query()->count(),
            'internCount' => User::query()->where('role', 'intern')->count(),
            'supervisorCount' => User::query()->where('role', 'supervisor')->count(),
            'hrCount' => User::query()->where('role', 'hr')->count(),
        ]);
    }
}
