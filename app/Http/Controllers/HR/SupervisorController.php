<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupervisorController extends Controller
{
    public function index(): View
    {
        $supervisors = User::query()->where('role', 'supervisor')->withCount('supervisedInternships')->orderBy('name')->paginate(15);

        return view('hr.supervisors.index', compact('supervisors'));
    }

    public function create(): View
    {
        return view('hr.supervisors.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'supervisor',
        ]);

        return redirect()->route('hr.supervisors.index')->with('status', 'Supervisor created successfully.');
    }

    public function edit(User $supervisor): View
    {
        abort_unless($supervisor->role === 'supervisor', 404);

        return view('hr.supervisors.edit', compact('supervisor'));
    }

    public function update(Request $request, User $supervisor): RedirectResponse
    {
        abort_unless($supervisor->role === 'supervisor', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($supervisor->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $supervisor->name = $validated['name'];
        $supervisor->email = $validated['email'];

        if (filled($validated['password'] ?? null)) {
            $supervisor->password = Hash::make($validated['password']);
        }

        $supervisor->save();

        return redirect()->route('hr.supervisors.index')->with('status', 'Supervisor updated successfully.');
    }

    public function destroy(User $supervisor): RedirectResponse
    {
        abort_unless($supervisor->role === 'supervisor', 404);

        if (Internship::query()->where('supervisor_id', $supervisor->id)->exists()) {
            return back()->withErrors(['supervisor' => 'Reassign this supervisor’s internships before deleting the account.']);
        }

        $supervisor->delete();

        return redirect()->route('hr.supervisors.index')->with('status', 'Supervisor deleted successfully.');
    }
}
