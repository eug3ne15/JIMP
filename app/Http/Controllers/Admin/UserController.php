<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $selectedRole = $request->query('role', 'all');

        $roleCounts = [
            'all' => User::count(),
            'intern' => User::where('role', 'intern')->count(),
            'hr' => User::where('role', 'hr')->count(),
            'supervisor' => User::where('role', 'supervisor')->count(),
            'admin' => User::where('role', 'admin')->count(),
        ];

        $users = User::query()->orderBy('name')->paginate(50)->withQueryString();
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.index', compact('users', 'roles', 'roleCounts', 'selectedRole'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $roles = Role::query()->orderBy('id')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:25',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|exists:roles,name',
            'school' => 'exclude_unless:role,intern|nullable|string|max:255',
            'course' => 'exclude_unless:role,intern|nullable|string|max:255',
            'password' => 'required|string|min:4|max:10|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'school' => $validated['school'] ?? null,
            'course' => $validated['course'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:25',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|string|exists:roles,name',
            'school' => 'exclude_unless:role,intern|nullable|string|max:255',
            'course' => 'exclude_unless:role,intern|nullable|string|max:255',
            'password' => 'nullable|string|min:4|max:10|confirmed',
        ]);

        if ($user->is(auth()->user()) && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot remove your own admin access.']);
        }

        if ($user->role === 'admin' && $validated['role'] !== 'admin' && User::where('role', 'admin')->count() === 1) {
            return back()->withErrors(['role' => 'The last admin account cannot be demoted.']);
        }

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'school' => $validated['role'] === 'intern' ? ($validated['school'] ?? null) : null,
            'course' => $validated['role'] === 'intern' ? ($validated['course'] ?? null) : null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return back()->withErrors(['user' => 'The last admin account cannot be deleted.']);
        }

        if ($user->supervisedInternships()->exists()) {
            return back()->withErrors(['user' => 'This supervisor has assigned internships. Reassign them before deleting.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully.');
    }
}
