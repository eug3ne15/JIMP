<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:4|max:10|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->login($user);

        return match ($user->role) {
            'hr' => redirect()->route('hr.dashboard'),
            'supervisor' => redirect()->route('supervisor.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard.intern'),
        };
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'The email or password is incorrect.',
            ]);
        }

        auth()->login($user);

        return match ($user->role) {
            'hr' => redirect()->route('hr.dashboard'),
            'supervisor' => redirect()->route('supervisor.dashboard'),
            'intern' => redirect()->route('dashboard.intern'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard.intern'),
        };
    }
}
